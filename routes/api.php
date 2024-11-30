<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::get('/chat-templates', [ChatController::class, 'chatTemplates']);
    Route::post('/chat-templates', [ChatController::class, 'storeChatTemplate']);
    Route::post('/chat-templates/{id}/use', [ChatController::class, 'useChatTemplate']);
    Route::get('/chat-sessions', [ChatController::class, 'chatSessions']);
    Route::get('/chat-sessions/{id}/', [ChatController::class, 'getChat']);
    Route::post('/chat-sessions/{id}/send-message', [ChatController::class, 'sendMessage']);
});
