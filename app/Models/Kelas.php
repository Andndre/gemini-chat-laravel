<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    // created by
    public function user()
    {
        return $this->belongsTo(User::class, 'guru_id', 'id');
    }

    // siswa
    public function siswa()
    {
        return $this->belongsToMany(User::class, 'user_join_kelas', 'kelas_id', 'user_id');
    }

    // materi
    public function materi()
    {
        return $this->hasMany(Materi::class, 'kelas_id', 'id');
    }

    // diskusi
    public function diskusi()
    {
        return $this->hasMany(Diskusi::class, 'kelas_id', 'id');
    }

    // ebook
    public function ebook()
    {
        return $this->hasMany(Ebook::class, 'kelas_id', 'id');
    }
}
