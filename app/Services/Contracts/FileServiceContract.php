<?php

namespace App\Services\Contracts;

use Illuminate\Http\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileServiceContract
{
    public function putUploadedFile (UploadedFile $file, string $path = '', string $disk = 'public');

    public function putUploadedFileAndKeepName (UploadedFile $file, string $path = '', string $disk = 'public');

    public function putUploadedFileAs (UploadedFile $file, string $name, string $path = '', string $disk = 'public');

    public function putUploadedFiles (array $files, string $path = '', string $disk = 'public');

    public function putUploadedFilesAndKeepName (array $files, string $path = '', string $disk = 'public');

    public function putUploadedFilesAs (array $files, array $names, string $path = '', string $disk = 'public');

    public function deleteFile (string $filePath, string $disk = 'public');

    public function deleteFiles (array $filePaths, string $disk = 'public');
}
