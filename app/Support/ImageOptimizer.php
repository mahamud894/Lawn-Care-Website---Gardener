<?php

namespace App\Support;

/**
 * Shrinks and compresses images with PHP's built-in GD library.
 * Big photos straight from a phone (3–8 MB) become ~100–200 KB,
 * which is the biggest single win for page speed on mobile.
 */
class ImageOptimizer
{
    /** Longest side (px) for images saved in each uploads folder. */
    public const MAX_SIZE = [
        'services/icons' => 200,
        'services' => 900,
        'team' => 800,
        'projects' => 1600,
        'posts' => 1600,
        'bookings' => 1600,
    ];

    public static function maxSizeFor(string $folder): int
    {
        return self::MAX_SIZE[$folder] ?? 1600;
    }

    public static function supported(): bool
    {
        return function_exists('imagecreatetruecolor') && function_exists('imagewebp');
    }

    /**
     * Resize (only ever shrink) and re-compress an image file.
     *
     * $format: 'webp' converts the file (the path changes to .webp);
     *          null keeps the original format and path.
     * Returns the final path, or the original path if nothing could be done.
     * The original is only replaced when the new file is actually smaller
     * or had to be converted.
     */
    public static function optimize(string $path, int $maxSide = 1600, int $quality = 78, ?string $format = null): string
    {
        if (! self::supported() || ! is_file($path)) {
            return $path;
        }

        $info = @getimagesize($path);
        if (! $info) {
            return $path;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => @imagecreatefromwebp($path),
            IMAGETYPE_GIF => @imagecreatefromgif($path),
            default => false,
        };

        if (! $image) {
            return $path;
        }

        if ($info[2] === IMAGETYPE_JPEG) {
            $image = self::fixOrientation($image, $path);
        }

        $image = self::shrink($image, $maxSide);

        $target = $format === 'webp'
            ? preg_replace('/\.[^.\/]+$/', '', $path).'.webp'
            : $path;
        $tmp = $target.'.tmp';

        $saved = match (true) {
            $format === 'webp', $info[2] === IMAGETYPE_WEBP => imagewebp($image, $tmp, $quality),
            $info[2] === IMAGETYPE_PNG => imagepng($image, $tmp, 9),
            $info[2] === IMAGETYPE_GIF => imagegif($image, $tmp),
            default => self::saveJpeg($image, $tmp, $quality),
        };
        imagedestroy($image);

        if (! $saved || ! is_file($tmp)) {
            @unlink($tmp);

            return $path;
        }

        // Keep the original if re-compressing made it bigger (already optimised)
        if ($target === $path && filesize($tmp) >= filesize($path)) {
            unlink($tmp);

            return $path;
        }

        rename($tmp, $target);
        if ($target !== $path) {
            @unlink($path);
        }

        return $target;
    }

    /** Make a smaller copy next to the original, e.g. hero.jpg → hero-sm.jpg */
    public static function variant(string $path, int $maxSide, string $suffix = '-sm', int $quality = 72): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $copy = preg_replace('/(\.[^.\/]+)$/', $suffix.'$1', $path);
        copy($path, $copy);

        // Force a re-save even if not smaller, so the copy is really resized
        $info = getimagesize($copy);
        if ($info && max($info[0], $info[1]) > $maxSide) {
            self::optimize($copy, $maxSide, $quality);
        }

        return $copy;
    }

    private static function shrink(\GdImage $image, int $maxSide): \GdImage
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $scale = $maxSide / max($w, $h);

        if ($scale >= 1) {
            return $image;
        }

        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));

        $resized = imagecreatetruecolor($nw, $nh);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($image);

        return $resized;
    }

    private static function saveJpeg(\GdImage $image, string $path, int $quality): bool
    {
        imageinterlace($image, true); // progressive JPEG shows a preview while loading

        return imagejpeg($image, $path, $quality);
    }

    /** Phone photos are often stored sideways with an EXIF "rotate" flag. */
    private static function fixOrientation(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $angle = match ($exif['Orientation'] ?? 1) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        return $angle ? (imagerotate($image, $angle, 0) ?: $image) : $image;
    }
}
