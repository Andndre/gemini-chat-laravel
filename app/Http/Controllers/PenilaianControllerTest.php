<?php

namespace App\Http\Controllers;

use App\Helpers\GeminiAPI;

class PenilaianControllerTest extends Controller
{
    public function nilaiDiskusi()
    {
        $nama_materi = 'Hukum Newton';
        $ringkasan_ebook = [
            'Hukum Newton dalam Fisika SMA membahas tiga hukum dasar gerak: Hukum I tentang kelembaman yang menyatakan bahwa benda akan tetap diam atau bergerak lurus beraturan jika tidak ada gaya total yang bekerja padanya, Hukum II yang menyatakan bahwa percepatan benda berbanding lurus dengan gaya total dan berbanding terbalik dengan massanya (ΣF = m.a), serta Hukum III yang menjelaskan bahwa setiap aksi memiliki reaksi yang sama besar tetapi berlawanan arah. Materi ini juga mencakup berbagai gaya seperti gaya normal, gesek, tegangan tali, dan gaya berat, yang berperan dalam dinamika gerak benda. Penerapannya dapat dilihat dalam kehidupan sehari-hari, seperti pergerakan kendaraan, peluncuran roket, dan berjalan kaki, serta diaplikasikan dalam berbagai soal yang melibatkan analisis gaya dan percepatan.',
        ];

        $ringkasan_ebook_str = implode('. ', $ringkasan_ebook);

        $kriteria_penilaian_pertanyaan = [
            [
            'nama' => 'Relevansi Pertanyaan',
            'penilaian' => 'Skor 1: Pertanyaan tidak relevan dengan materi atau di luar konteks pembelajaran.
            Skor 2: Pertanyaan kurang relevan atau hanya sekadar meminta klarifikasi sederhana tanpa menunjukkan pemahaman yang luas.
            Skor 3: Pertanyaan cukup relevan tetapi masih bisa dikembangkan lebih dalam dan lebih bersifat faktual.
            Skor 4: Pertanyaan sangat relevan dengan materi dan menunjukkan pemahaman mendalam serta keterkaitan dengan aspek lain (keterkaitan dengan diluar materi yang diajarkan).',
            ],
            [
            'nama' => 'Kejelasan Pertanyaan',
            'penilaian' => 'Skor 1: Pertanyaan tidak jelas atau tidak memiliki struktur yang baik sehingga sulit dipahami.
            Skor 2: Pertanyaan kurang jelas, ambigu, atau banyak ada kesalahan mengetik, sehingga bisa menimbulkan kebingungan dalam menjawab.
            Skor 3: Pertanyaan cukup jelas tetapi masih bisa diperbaiki dalam struktur atau pemilihan kata.
            Skor 4: Pertanyaan sangat jelas, terstruktur, dan spesifik sehingga mudah dipahami tanpa ambiguitas.',
            ],
            [
            'nama' => 'Kedalaman Berpikir',
            'penilaian' => 'Skor 1: Pertanyaan sangat dangkal dan tidak memiliki nilai analitis, hanya bertanya tentang definisi atau fakta sederhana.
            Skor 2: Pertanyaan masih dangkal dan hanya meminta informasi dasar tanpa analisis lebih lanjut.
            Skor 3: Pertanyaan cukup analitis tetapi belum sepenuhnya menjelaskan hubungan antar konsep secara luas.
            Skor 4: Pertanyaan menunjukkan pemikiran kritis dan analitis serta mampu menghubungkan konsep secara mendalam.',
            ],
            [
            'nama' => 'Orisinalitas Pertanyaan',
            'penilaian' => 'Skor 1: Pertanyaan sudah pernah diajukan sebelumnya tanpa variasi atau pengembangan baru (sama persis).
            Skor 2: Pertanyaan kurang orisinal, hanya memodifikasi pertanyaan yang sudah ada (sama persis, hanya diparafrase saja).
            Skor 3: Ada hal baru yang ditanyakan.
            Skor 4: Pertanyaan unik dan belum pernah ditanyakan sebelumnya.',
            ],
            [
            'nama' => 'Indikasi Membaca Materi Sebelumnya',
            'penilaian' => 'Skor 1: Pertanyaan menunjukkan bahwa siswa bertanya tanpa membaca materi sama sekali, sering kali bersifat sangat umum atau dasar.
            Skor 2: Pertanyaan menunjukkan bahwa siswa belum cukup memahami materi sebelum bertanya dan terlihat dari ketidakjelasan pertanyaannya.
            Skor 3: Pertanyaan menunjukkan bahwa siswa memiliki pemahaman tetapi belum terlalu mendalam terhadap materi.
            Skor 4: Pertanyaan menunjukkan bahwa siswa telah membaca dan memahami materi sebelumnya dengan merujuk pada konsep yang dipelajari.',
            ],
        ];

        $kriteria_penilaian_str = implode('\n', array_map(function ($kriteria) {
            return $kriteria['nama'].' ('.$kriteria['penilaian'] . ')';
        }, $kriteria_penilaian_pertanyaan));

        $pertanyaan_sebelumnya = [
            'Apakah hukum Newton mempunyai perbedaan dengan hukum kekuatan yang lain?',
            'Bagaimana Hukum Newton I dapat menjelaskan fenomena ketika sebuah benda tampak berhenti meskipun terdapat gaya kecil yang bekerja padanya, seperti mobil yang melaju dan akhirnya berhenti karena gesekan udara dan permukaan jalan?',
            'Apakah ada situasi nyata di mana Hukum Newton II tidak berlaku, dan bagaimana konsep ini diadaptasi dalam fisika modern seperti teori relativitas atau mekanika kuantum?',
        ];

        $pertanyaan_sebelumnya_str = implode("\n", array_map(function ($item, $index) {
            return ($index + 1).'. '.$item;
        }, $pertanyaan_sebelumnya, array_keys($pertanyaan_sebelumnya)));

        // Pertanyaan yang relevan dengan materi
        $diskusi = [
            'pertanyaan' => 'Apakah hukum newton mempunyai kesaman dengn hukum kekuatan lainnya?',
        ];

        $promt = [
            [
                'role' => 'user',
                'parts' => [['text' => 'Hello, kamu adalah AI yang bisa menilai suatu diskusi terhadap suatu materi tertentu, dan berdasarkan suatu kriteria penilaian.']],
            ],
            [
                'role' => 'model',
                'parts' => [['text' => 'Baik, saya akan memulai.Berikan saya data nama_materi, ringkasan_ebook, pertanyaan_sebelumnya, kriteria_penilaian_pertanyaan, dan diskusi yang relevan dengan materi tersebut. Saya akan merespons dalam format JSON']],
            ],
        ];

        $geminiAPI = new GeminiAPI('gemini-2.0-flash');
        $chat = $geminiAPI->startChat($promt);

        // Replace \n with actual new lines in the final prompt
        $promt = <<<PROMPT
Perhatikan konteks berikut:
Nama materi: "{$nama_materi}"

Ringkasan materi: "{$ringkasan_ebook_str}"

Pertanyaan yang sudah pernah ditanyakan sebelumnya (digunakan untuk menilai orisinalitas):
{$pertanyaan_sebelumnya_str}

Kriteria penilaian pertanyaan:
{$kriteria_penilaian_str}

Ini pertanyaan yang harus kamu nilai:
"{$diskusi['pertanyaan']}", dengan memerhatikan kriteria penilaian pertanyaan.
PROMPT;

        // dd($promt);

        $response = $chat->chat(
            $promt,
            [
                'type' => 'object',
                'properties' => [
                    'pertanyaan' => [
                        'type' => 'object',
                        'properties' => [
                            'relevansi' => ['type' => 'integer'],
                            'kejelasan_pertanyaan' => ['type' => 'integer'],
                            'kedalaman_berpikir' => ['type' => 'integer'],
                            'orisinalitas_pertanyaan' => ['type' => 'integer'],
                            'indikasi_membaca_sebelumnya' => ['type' => 'integer'],
                        ],
                        'required' => [
                            'relevansi',
                            'kejelasan_pertanyaan',
                            'kedalaman_berpikir',
                            'orisinalitas_pertanyaan',
                            'indikasi_membaca_sebelumnya',
                        ],
                    ],
                ],
                'required' => ['pertanyaan'],
            ]
        );

        // Extract JSON response from the chat response
        $text = $response['candidates'][0]['content']['parts'][0]['text'];
        $jsonStart = strpos($text, '{');
        $jsonEnd = strrpos($text, '}') + 1;
        $jsonString = substr($text, $jsonStart, $jsonEnd - $jsonStart);
        $parsedResponse = json_decode($jsonString, true);

        return response()->json($parsedResponse);
    }
}
