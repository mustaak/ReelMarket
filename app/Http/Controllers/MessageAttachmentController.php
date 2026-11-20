<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageAttachmentController extends Controller
{
    public function show(Message $message): StreamedResponse
    {
        abort_unless(
            $message->attachment_path !== null
                && $message->conversation()
                    ->whereHas(
                        'users',
                        fn ($query) => $query
                            ->whereKey(auth()->id())
                            ->whereNotNull('conversation_user.accepted_at'),
                    )
                    ->exists(),
            404,
        );

        abort_unless(
            Storage::disk('local')->exists($message->attachment_path),
            404,
        );

        $disposition = str_starts_with((string) $message->attachment_mime, 'image/')
            ? 'inline'
            : 'attachment';

        return Storage::disk('local')->response(
            $message->attachment_path,
            $message->attachment_name,
            [
                'Content-Type' => $message->attachment_mime ?? 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
            ],
            $disposition,
        );
    }
}
