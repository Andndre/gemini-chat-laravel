<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateChat extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }
}
