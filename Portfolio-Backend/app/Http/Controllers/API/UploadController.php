<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'folder' => 'required|string',
        ]);

        $file = $request->file('file');
        $folder = $request->input('folder');
        
        $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) 
            . '.' . $file->getClientOriginalExtension();
        
            $path = Storage::disk('s3')->putFileAs(
                $folder, 
                $file, 
                $fileName
            );
        $url = Storage::disk('s3')->url($path);
        
        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $path
        ]);
    }
}