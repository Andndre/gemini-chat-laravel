<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\TemplateChat;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Mengambil semua template chat
    public function chatTemplates()
    {
        $allChatTemplates = TemplateChat::all();
        return response()->json($allChatTemplates);
    }

    // Menampilkan halaman index
    public function index() {
        return view('index');
    }

    // Menyimpan template chat baru
    public function storeChatTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $templateChat = new TemplateChat();
        $templateChat->name = $request->name;
        $templateChat->description = $request->description;
        $templateChat->save();

        return response()->json($templateChat);
    }

    // Mendapatkan riwayat briefing berdasarkan deskripsi dan riwayat sebelumnya
    public function getBriefingHistory($description, $history = []) {
        $chat = Gemini::chat()->startChat(history: array_merge([
            Content::parse(part: 'Hello'),
            Content::parse(part: 'Halo, saya adalah AI canggih yang dikembangkan oleh Ryan Darmayasa untuk menjawab pertanyaan terkait hal berikut : "' . $description . '". Saya tidak bisa menjawab diluar dari konteks ini', role: Role::MODEL),
        ], $history));

        return $chat;
    }

    // Menggunakan template chat untuk memulai sesi chat baru
    public function useChatTemplate(Request $request, $id)
    {
        $request->validate([
            'first_message' => 'required|string',
        ]);

        $templateChat = TemplateChat::find($id);

        if (!$templateChat) {
            return response()->json(['message' => 'Template chat not found'], 404);
        }

        $chatSession = new ChatSession();
        $chatSession->template_chat_id = $templateChat->id;

        $chat = $this->getBriefingHistory($templateChat->description);

        $response = $chat->sendMessage($request->first_message);

        // Menghasilkan nama untuk judul chat
        $responseName = Gemini::geminiPro()->generateContent("Buatkan judul singkat untuk chat ini, terkait " . $request->first_message);

        $chatSession->name = $responseName->text();
        $chatSession->save();

        // Menyimpan riwayat chat ke database
        $chatSession->chatItems()->createMany([
            [
                'content' => $request->first_message,
                'role' => Role::USER,
            ],
            [
                'content' => $response->text(),
                'role' => Role::MODEL,
            ]
        ]);

        return response()->json([
            'response' => $response->text(),
            'chat_title' => $responseName->text(),
            'chat_session_id' => $chatSession->id,
        ]);
    }

    public function chatSessions()
    {
        $allChatSessions = ChatSession::all();
        return response()->json($allChatSessions);
    }

    // Mendapatkan riwayat chat berdasarkan ID sesi chat
    public function getChat($id)
    {
        $chatSession = ChatSession::find($id);

        if (!$chatSession) {
            return response()->json(['message' => 'Chat session not found'], 404);
        }

        $chatItems = $chatSession->chatItems()->get();

        return response()->json($chatItems);
    }

    // Mengirim pesan dalam sesi chat yang sudah ada
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $chatSession = ChatSession::find($id);

        if (!$chatSession) {
            return response()->json(['message' => 'Chat session not found'], 404);
        }

        $previousChatItems = $chatSession->chatItems()->get();

        $history = $previousChatItems->map(function ($item) {
            return Content::parse(part: $item->content, role: $item->role === 'user' ? Role::USER : Role::MODEL);
        })->toArray();

        $chatTemplate = TemplateChat::find($chatSession->template_chat_id);

        $chat = $this->getBriefingHistory($chatTemplate->description, $history);

        $response = $chat->sendMessage($request->message);

        // Menyimpan riwayat chat ke database
        $chatSession->chatItems()->create([
            'content' => $request->message,
            'role' => Role::USER,
        ]);

        $chatSession->chatItems()->create([
            'content' => $response->text(),
            'role' => Role::MODEL,
        ]);

        return response()->json([
            'response' => $response->text(),
        ]);
    }
}
