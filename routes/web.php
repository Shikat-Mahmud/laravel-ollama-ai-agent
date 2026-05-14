<?php

use App\Ai\Agents\ChatAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::view('/ai-chat', 'ai.chat');

Route::post('/ai-chat-stream', function (Request $request) {

    $request->validate([
        'message' => 'required|string',
        'attachments' => 'nullable|array',
        'attachments.*' => 'file|max:10240', // 10MB limit
    ]);

    $attachments = [];

    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            $mimeType = $file->getMimeType();

            if (str_starts_with($mimeType, 'image/')) {
                $attachments[] = Laravel\Ai\Files\Image::fromPath($file->getRealPath());
            } else {
                $attachments[] = Laravel\Ai\Files\Document::fromPath($file->getRealPath());
            }
        }
    }

    $agent = new ChatAgent();
    $prompt = $agent->prompt($request->message, attachments: $attachments);

    return response()->json([
        'message' => $prompt->text
    ]);

})->name('ai.chat-stream');

