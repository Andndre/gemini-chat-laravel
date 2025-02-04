<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function myClasses(Request $request) {
        $authUser = $request->user();
        $user = User::find($authUser->id);

        $kelas = $user->kelas()->get();
        
        return response()->json([
            'data' => $kelas
        ], 200);
    }

    public function regenerateClassCode(Request $request, int $id) {
        $authUser = $request->user();
        $user = User::find($authUser->id);

        $kelas = $user->kelas()->find($id);

        $kelas->update([
            'kode_kelas' => $this->generateRandomString(6)
        ]);

        return response()->json([
            'data' => $kelas
        ], 200);
    }

    public function joinClass(Request $request) {
        // code
        $request->validate([
            'kode_kelas' => 'required|string'
        ]);

        $authUser = $request->user();
        $user = User::find($authUser->id);

        $kelas = Kelas::where('kode_kelas', $request->kode_kelas)->first();

        if (!$kelas) {
            return response()->json([
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        $user->joinedKelas()->attach($kelas->id);

        return response()->json([
            'data' => $kelas
        ], 200);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
