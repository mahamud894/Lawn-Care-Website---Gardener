<?php

namespace App\Console\Commands;

use App\Support\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * php artisan images:optimize
 *
 * Shrinks every image already on the site (theme images and old uploads).
 * Safe to run again — files that are already small are left alone,
 * and paths never change, so nothing in the database needs updating.
 */
class OptimizeImages extends Command
{
    protected $signature = 'images:optimize {--dry-run : Only show what would change}';

    protected $description = 'Resize and compress the site images and uploads for faster pages';

    public function handle(): int
    {
        if (! ImageOptimizer::supported()) {
            $this->error('The PHP GD extension (with WebP) is needed.');

            return self::FAILURE;
        }

        $before = 0;
        $after = 0;

        // Theme images: hero/about are shown full width, the rest are cards
        foreach (File::files(public_path('site/img')) as $file) {
            $max = str_starts_with($file->getFilename(), 'carousel') ? 1920 : 900;
            [$b, $a] = $this->process($file->getPathname(), $max);
            $before += $b;
            $after += $a;
        }

        // Smaller hero copies for phones (used with srcset)
        if (! $this->option('dry-run')) {
            foreach (['carousel-1.jpg', 'carousel-2.jpg', 'about.jpg'] as $name) {
                $path = public_path('site/img/'.$name);
                if (is_file($path)) {
                    ImageOptimizer::variant($path, 800);
                }
            }
        }

        // Uploaded images, sized per folder (icons are tiny on the page)
        foreach (array_keys(ImageOptimizer::MAX_SIZE) as $folder) {
            $dir = public_path('uploads/'.$folder);
            if (! is_dir($dir)) {
                continue;
            }
            foreach (File::files($dir) as $file) {
                if ($file->getFilename() === '.gitkeep') {
                    continue;
                }
                [$b, $a] = $this->process($file->getPathname(), ImageOptimizer::maxSizeFor($folder));
                $before += $b;
                $after += $a;
            }
        }

        $this->newLine();
        $this->info(sprintf('Total: %s → %s (saved %s)', $this->kb($before), $this->kb($after), $this->kb($before - $after)));

        return self::SUCCESS;
    }

    private function process(string $path, int $max): array
    {
        $before = filesize($path);

        if (! @getimagesize($path) || str_contains(basename($path), '-sm.')) {
            return [0, 0];
        }

        if (! $this->option('dry-run')) {
            ImageOptimizer::optimize($path, $max, 78);
            clearstatcache(true, $path);
        }

        $after = filesize($path);
        if ($after < $before) {
            $this->line(sprintf('  %-60s %8s → %s', str_replace(public_path().'/', '', $path), $this->kb($before), $this->kb($after)));
        }

        return [$before, $after];
    }

    private function kb(int $bytes): string
    {
        return $bytes >= 1048576 ? round($bytes / 1048576, 1).' MB' : round($bytes / 1024).' KB';
    }
}
