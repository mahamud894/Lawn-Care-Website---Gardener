<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Saves uploaded images into public/uploads/<folder> with a unique name
 * and removes old ones. Returns paths like "uploads/services/abc.jpg".
 */
class ImageUpload
{
    public static function store(UploadedFile $file, string $folder): string
    {
        $name = now()->format('YmdHis').'_'.Str::random(10).'.'.strtolower($file->getClientOriginalExtension());
        $file->move(public_path('uploads/'.$folder), $name);

        return 'uploads/'.$folder.'/'.$name;
    }

    /** Replace an old image with a new upload (deletes the old file). */
    public static function replace(?string $oldPath, UploadedFile $file, string $folder): string
    {
        self::delete($oldPath);

        return self::store($file, $folder);
    }

    public static function delete(?string $path): void
    {
        // Only ever delete files inside public/uploads
        if ($path && str_starts_with($path, 'uploads/') && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
