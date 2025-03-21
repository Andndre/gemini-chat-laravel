<?php

namespace App\Http\Controllers;

use App\Models\User;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;

class EbookController extends Controller
{
    // ringkas AI
    public function ringkas(Request $request)
    {
        $pdf = $request->file('pdf');

        if (! $pdf) {
            return response()->json([
                'message' => 'PDF tidak ditemukan',
            ], 400);
        }

        // generate ringkas
        $ringkas = Gemini::geminiPro()->generateContent([
            'Ringkas file PDF ini',
            new Blob(
                mimeType: MimeType::APPLICATION_PDF,
                data: $pdf
            ),
        ]);

        // return ringkas
        return response()->json([
            'data' => $ringkas,
        ]);
    }

    // upload ebook
    public function upload(Request $request, int $materi_id)
    {
        $request->validate([
            'file' => 'required|mimetypes:application/pdf',
            'ringkasan' => 'required|string',
        ]);

        $authUser = $request->user();
        $user = User::find($authUser->id);

        $pdf = $request->file('file');
        $pdf_path = $pdf->store('ebooks', 'public');
        $ringkasan = $request->ringkasan;

        $ebook = $user->ebooks()->create([
            'file' => $pdf_path,
            'ringkasan' => $ringkasan,
            'materi_id' => $materi_id,
        ]);

        return response()->json([
            'data' => $ebook,
        ], 200);
    }
}
