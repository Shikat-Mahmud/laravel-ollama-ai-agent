<?php

use App\Ai\Agents\ChatAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::view('/ai-chat', 'ai.chat');

Route::get('/ai-chat-stream', function (Request $request) {

    $request->validate([
        'message' => 'required|string',
    ]);

    $agent = new ChatAgent();
    $prompt = $agent->prompt($request->message);

    return response()->json([
        'message' => $prompt->text
    ]);

})->name('ai.chat-stream');

