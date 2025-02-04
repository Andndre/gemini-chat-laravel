<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;

class DiskusiController extends Controller
{
    // store
    public function store(Request $request, int $materi_id) {
        $request->validate([
            'nama' => 'required|string',
        ]);

        $materi = Materi::find($materi_id);
        $materi->diskusi()->create([
            'nama' => $request->nama
        ]);

        return response()->json([
            'data' => $materi
        ], 200);
    }
}
