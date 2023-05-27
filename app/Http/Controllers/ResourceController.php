<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPostRequest;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    private FileService $fileService;

    /**
     * @param FileService $fileService
     */
    public function __construct (FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function upload (UploadPostRequest $request) : JsonResponse
    {
        $fileInfo = $this->fileService->putUploadedFileAndKeepName($request->file('upload_file', (string)time()));
        return response()->json(['data' => $fileInfo]);
    }
}
