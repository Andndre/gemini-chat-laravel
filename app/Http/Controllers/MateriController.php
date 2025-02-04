<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index(int $id) {
        $kelas = Kelas::find($id);

        return response()->json([
            'data' => $kelas
        ], 200);
    }

    public function store(Request $request, int $id) {
        $request->validate([
            'nama' => 'required|string',
        ]);

        $kelas = Kelas::find($id);
        $kelas->materi()->create([
            'nama' => $request->nama
        ]);

        return response()->json([
            'data' => $kelas
        ], 200);
    }
}
