<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $path = $request->file('image')->store('projects', 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'path' => $path,
            'url' => $url,
        ], 201);
    }
}
