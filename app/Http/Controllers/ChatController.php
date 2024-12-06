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
    public function getBriefingHistory($name, $description, $history = []) {
        $chat = Gemini::chat()->startChat(history: array_merge([
            Content::parse(part: 'Hello'),
            Content::parse(part: 'Halo, saya adalah AI canggih yang dikembangkan oleh Ryan Darmayasa untuk menjawab pertanyaan terkait hal berikut : "' . $description . '". Saya tidak bisa menjawab diluar dari konteks ini', role: Role::MODEL),
            Content::parse(part: "Saya hanya ingin Anda menjawab pertanyaan tentang " . $name . ". Jika saya bertanya tentang topik lain, abaikan saja."),
            Content::parse(part: "Saya akan mencoba membantu Anda sebaik mungkin. Mari kita mulai.", role: Role::MODEL),
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

        $chat = $this->getBriefingHistory($templateChat->name, $templateChat->description);

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

        $chat = $this->getBriefingHistory($chatTemplate->name, $chatTemplate->description, $history);

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

    // Mendapatkan skor chat berdasarkan ID sesi chat
    // Skor chat dihitung berdasarkan seberapa sesuai pertanyaan pengguna dengan topik chat, dan seberapa kritis pertanyaan pengguna (jika sudah dijelaskan dalam deskripsi chat maka skor nya rendah, jika belum dijelaskan maka skor nya tinggi), tapi tetap harus sesuai topik chat
    // Untuk itu mungkin fungsi briefing nya akan berbeda
    // Harapan dari output nya adalah dalam format json:
    /**
     {
        "score": 0.8,
        "message": "Pertanyaan Anda sangat sesuai dengan topik chat, namun sudah dijelaskan dalam deskripsi chat. Skor Anda adalah 0.8"
    }
     */

    public function chatScore($id) {
        $chatSession = ChatSession::find($id);

        if (!$chatSession) {
            return response()->json(['message' => 'Chat session not found'], 404);
        }

        $chatItems = $chatSession->chatItems()->get();

        $userMessages = $chatItems->filter(function ($item) {
            return $item->role === 'user';
        });

        $chatTemplate = TemplateChat::find($chatSession->template_chat_id);

        $score = 0;
        $message = '';

        if ($userMessages->count() === 0) {
            $score = 0;
            $message = 'Anda belum mengirimkan pertanyaan apapun';
        } else {
            $userMessagesArray = $userMessages->pluck('content')->toArray();
            $numberedMessages = array_map(function($message, $index) {
                return ($index + 1) . '. "' . $message . '"';
            }, $userMessagesArray, array_keys($userMessagesArray));

            // dd($numberedMessages);

            $response = Gemini::geminiPro()->generateContent("Beri nilai semua pertanyaan ini berdasarkan deskripsi: {$chatTemplate->description} | Pertanyaan: \n" . implode('\n', $numberedMessages) . "\n | Note: - jawab dalam format json: {score: berupa integer, message: pesan anda} \nAturan penilaian: - Input berupa lebih dari satu pertanyaan \n- Nilai semua pertanyaan dan akumulasikan menjadi 1 score total \n- Jika ada pertanyaan yang diluar konteks \n- Ambil pertanyaan yang berkaitan dengan deskripsi chat (tidak harus sama) \n- Score antara 0 sampai 1 untuk 1 pertanyaan, 1 adalah pertanyaan yang masih berkaitan dengan deskripsi chat, 0 adalah pertanyaan yang tidak sesuai dengan deskripsi chat");
            $text = $response->text();

            $jsonStart = strpos($text, '{');
            $jsonEnd = strrpos($text, '}') + 1;
            $jsonString = substr($text, $jsonStart, $jsonEnd - $jsonStart);
            $parsedResponse = json_decode($jsonString, true);

            return response()->json([
                'score' => $parsedResponse['score'] ?? 0,
                'message' => $parsedResponse['message'] ?? 'Error parsing response',
            ]);
        }

        return response()->json([
            'score' => $score,
            'message' => $message,
        ]);
    }
}
