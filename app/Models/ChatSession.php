<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'chat_template_id'];

    public function chatItems()
    {
        return $this->hasMany(ChatItem::class);
    }

    public function chatTemplate()
    {
        return $this->belongsTo(TemplateChat::class);
    }
}
