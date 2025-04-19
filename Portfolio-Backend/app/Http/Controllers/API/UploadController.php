<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . Str::slug($file->getClientOriginalName());
        
        $path = Storage::disk('s3')->putFileAs('uploads', $file, $fileName);
        $url = Storage::disk('s3')->url($path);
        
        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $path
        ]);
    }
}