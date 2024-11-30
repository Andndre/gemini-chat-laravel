<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatItem extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'chat_session_id', 'role'];

    public function chatSession()
    {
        return $this->belongsTo(ChatSession::class);
    }
}
