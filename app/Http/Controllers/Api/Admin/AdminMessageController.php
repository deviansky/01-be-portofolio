<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        // Filter status: unread | read | all
        $status = $request->query('status', 'all');
        if ($status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        // Search in name, email, subject, message
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 15);
        $messages = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($messages);
    }

    /**
     * Get count of unread messages.
     */
    public function unreadCount()
    {
        $count = ContactMessage::whereNull('read_at')->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Display the specified contact message and mark it as read.
     */
    public function show($id)
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            return response()->json([
                'message' => 'Pesan tidak ditemukan.',
            ], 404);
        }

        if (is_null($message->read_at)) {
            $message->read_at = now();
            $message->save();
        }

        return response()->json($message);
    }

    /**
     * Toggle read/unread status.
     */
    public function toggleRead($id)
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            return response()->json([
                'message' => 'Pesan tidak ditemukan.',
            ], 404);
        }

        if (is_null($message->read_at)) {
            $message->read_at = now();
        } else {
            $message->read_at = null;
        }

        $message->save();

        return response()->json($message);
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy($id)
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            return response()->json([
                'message' => 'Pesan tidak ditemukan.',
            ], 404);
        }

        $message->delete();

        return response()->json([
            'message' => 'Pesan berhasil dihapus.',
        ]);
    }
}
