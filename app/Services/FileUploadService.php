<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class FileUploadService
{
    /**
     * Upload a file to public storage with category folder organization and optional old file cleanup.
     *
     * @param UploadedFile $file
     * @param string $category Folder category e.g. 'profile', 'settings', 'customers', 'leads', 'videos', 'documents'
     * @param string|null $oldFilePath Relative path of old file to delete e.g. 'uploads/profile/foo.jpg'
     * @param string|null $customPrefix Optional filename prefix
     * @return string Relative path of saved file e.g. 'uploads/profile/profile_1692000000_64d2e.jpg'
     */
    public static function upload(UploadedFile $file, string $category = 'documents', ?string $oldFilePath = null, ?string $customPrefix = null): string
    {
        // 1. Delete old file if provided
        static::delete($oldFilePath);

        // 2. Determine target folder in public/uploads/{category}
        $folder = trim($category, '/\\');
        $targetDir = public_path('uploads/' . $folder);

        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        // 3. Generate unique filename while preserving extension
        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $prefix = $customPrefix ? rtrim($customPrefix, '_') . '_' : '';
        $filename = $prefix . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 8) . '.' . $extension;

        // 4. Move file to target directory
        $file->move($targetDir, $filename);

        return 'uploads/' . $folder . '/' . $filename;
    }

    /**
     * Delete a file from public path if it exists.
     *
     * @param string|null $filePath Relative path of the file
     * @return bool
     */
    public static function delete(?string $filePath): bool
    {
        if (empty($filePath)) {
            return false;
        }

        $fullPath = public_path($filePath);
        if (File::exists($fullPath) && !File::isDirectory($fullPath)) {
            return File::delete($fullPath);
        }

        return false;
    }
}
