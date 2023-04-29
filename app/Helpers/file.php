<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

function renameUploadedFile (string $filePath, string $newFileName, string $additionalTag = '')
{
    $array       = explode('/', $filePath);
    $newFileName = preg_replace('/[a-zA-Z0-9_.]+\./', "{$newFileName}_{$additionalTag}.",
                                end($array));
    $newFilePath = str_replace(end($array), $newFileName, $filePath);
    Storage::move($filePath, $newFilePath);
}

function renameUploadedFileWIthUrl (string $url, string $newFileName, string $additionalTag = '')
{
    $filePath = convertFromUrlToLocalPath($url);
    renameUploadedFile($filePath, $newFileName, $additionalTag);
}

function convertFromUrlToLocalPath (string $url) : string
{
    return preg_replace('/^[a-zA-Z0-9\/:.]+storage/', 'public', $url);
}

function checkIfFileExist (string $filePath) : bool
{
    return Storage::exists($filePath);
}

function checkIfFileExistWithUrl (string $url) : bool
{
    $filePath = convertFromUrlToLocalPath($url);
    return Storage::exists($filePath);
}

function deleteFile (string $filePath)
{
    Storage::delete($filePath);
}