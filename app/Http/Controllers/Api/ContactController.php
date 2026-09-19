<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    /** POST /api/contact */
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        // Honeypot terisi = bot. Balas sukses supaya bot tidak mencoba cara lain, tapi jangan simpan.
        if (filled($request->input('website'))) {
            return response()->json(['message' => 'Pesan terkirim.'], 201);
        }

        ContactMessage::create([
            ...$request->safe()->only(['name', 'email', 'subject', 'message']),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return response()->json(['message' => 'Pesan terkirim.'], 201);
    }
}
