<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService implements Contracts\FileServiceContract
{
    public function putUploadedFile (UploadedFile $file, string $path = '', string $disk = 'public') : array
    {
        $fileInfo['name']      = "{$file->hashName()}.{$file->extension()}";
        $fileInfo['extension'] = $file->extension();
        $fileInfo['disk']      = $disk;
        $fileInfo['file_path'] = $file->store($path, $disk);
        $fileInfo['url']       = Storage::disk($disk)->url($fileInfo['file_path']);

        return $fileInfo;
    }

    public function putUploadedFileAndKeepName (UploadedFile $file, string $path = '', string $disk = 'public') : array
    {
        $fileInfo['name']      = $file->getClientOriginalName();
        $fileInfo['extension'] = $file->extension();
        $fileInfo['disk']      = $disk;
        $fileInfo['file_path'] = $file->storeAs($path, $fileInfo['name'], $disk);
        $fileInfo['url']       = Storage::disk($disk)->url($fileInfo['file_path']);

        return $fileInfo;
    }

    public function putUploadedFileAs (UploadedFile $file, string $name, string $path = '', string $disk = 'public') : array
    {
        $fileInfo['name']      = "{$name}.{$file->extension()}";
        $fileInfo['extension'] = $file->extension();
        $fileInfo['disk']      = $disk;
        $fileInfo['file_path'] = $file->storeAs($path, $fileInfo['name'], $disk);
        $fileInfo['url']       = Storage::disk($disk)->url($fileInfo['file_path']);

        return $fileInfo;
    }

    public function putUploadedFiles (array $files, string $path = '', string $disk = 'public') : array
    {
        $filesInfo = [];
        foreach ($files as $file)
        {
            $filesInfo[] = $this->putUploadedFile($file, $path, $disk);
        }

        return $filesInfo;
    }

    public function putUploadedFilesAndKeepName (array $files, string $path = '', string $disk = 'public') : array
    {
        $filesInfo = [];
        foreach ($files as $file)
        {
            $filesInfo[] = $this->putUploadedFileAndKeepName($file, $path, $disk);
        }

        return $filesInfo;
    }

    public function putUploadedFilesAs (array $files, array $names, string $path = '', string $disk = 'public') : array
    {
        $filesInfo = [];
        foreach ($files as $file)
        {
            $filesInfo[] = $this->putUploadedFilesAs($file, array_shift($names), $path, $disk);
        }

        return $filesInfo;
    }

    public function deleteFile (string $filePath, string $disk = 'public') : bool
    {
        return Storage::disk($disk)->delete($filePath);
    }

    public function deleteFiles (array $filePaths, string $disk = 'public') : bool
    {
        return Storage::disk($disk)->delete($filePaths);
    }
}
