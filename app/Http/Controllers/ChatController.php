<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\TemplateChat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $allChats = ChatSession::all();
        $allChatTemplates = TemplateChat::all();
        return view('index', compact('allChats', 'allChatTemplates'));
    }

    public function form()
    {
        return view('form');
    }

    public function chat()
    {
        return view('chat');
    }
}
