<?php

namespace App\Http\Controllers;

use Gemini\Data\Content;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Enums\Role;


class PenilaianControllerTest extends Controller
{
    public function nilaiDiskusi() {
        $nama_materi = 'Hukum Newton';
        $ringkasan_ebook = [
            'Hukum Newton dalam Fisika SMA membahas tiga hukum dasar gerak: Hukum I tentang kelembaman yang menyatakan bahwa benda akan tetap diam atau bergerak lurus beraturan jika tidak ada gaya total yang bekerja padanya, Hukum II yang menyatakan bahwa percepatan benda berbanding lurus dengan gaya total dan berbanding terbalik dengan massanya (ΣF = m.a), serta Hukum III yang menjelaskan bahwa setiap aksi memiliki reaksi yang sama besar tetapi berlawanan arah. Materi ini juga mencakup berbagai gaya seperti gaya normal, gesek, tegangan tali, dan gaya berat, yang berperan dalam dinamika gerak benda. Penerapannya dapat dilihat dalam kehidupan sehari-hari, seperti pergerakan kendaraan, peluncuran roket, dan berjalan kaki, serta diaplikasikan dalam berbagai soal yang melibatkan analisis gaya dan percepatan.'
        ];

        $ringkasan_ebook_str = implode('. ', $ringkasan_ebook);

        $kriteria_penilaian_pertanyaan = [
            [
                'nama' => 'Relevansi Pertanyaan',
                'kondisi' => 'Berdasarkan materi, pertanyaan tersebut relevan dengan materi tersebut, jika relevan maka akan mendapatkan nilai 1, jika tidak relevan maka akan mendapatkan nilai 0',
            ],
            [
                'nama' => 'Kedalaman Pertanyaan',
                'kondisi' => 'Pertanyaan tersebut memuat informasi yang cukup mendalam tentang materi tersebut',
            ],
            [
                'nama' => 'Orisinalitas Pertanyaan',
                'kondisi' => 'Pertanyaan tersebut belum pernah ditanyakan sebelumnya',
            ],
            [
                'nama' => 'Kejelasan Struktur',
                'kondisi' => 'Pertanyaan tersebut memuat informasi yang cukup jelas tentang materi tersebut',
            ]
        ];

        $kriteria_penilaian_str = implode(', ', array_map(function ($kriteria) {
            return $kriteria['nama'] . ' (' . $kriteria['kondisi'] . ')';
        }, $kriteria_penilaian_pertanyaan));

        $pertanyaan_sebelumnya = [
            'Apakah hukum Newton mempunyai perbedaan dengan hukum kekuatan yang lain?',
            'Bagaimana Hukum Newton I dapat menjelaskan fenomena ketika sebuah benda tampak berhenti meskipun terdapat gaya kecil yang bekerja padanya, seperti mobil yang melaju dan akhirnya berhenti karena gesekan udara dan permukaan jalan?',
            'Apakah ada situasi nyata di mana Hukum Newton II tidak berlaku, dan bagaimana konsep ini diadaptasi dalam fisika modern seperti teori relativitas atau mekanika kuantum?'
        ];

        $pertanyaan_sebelumnya_str = implode("\n", array_map(function($item, $index) {
            return ($index + 1) . '. ' . $item;
        }, $pertanyaan_sebelumnya, array_keys($pertanyaan_sebelumnya)));

        // Pertanyaan yang relevan dengan materi
        $diskusi = [
            'pertanyaan' => 'hukum Newton apakah punya perbedaan sama hukum kekuatan yang lain?',
            'jawaban' => [
                'Gaya itu kalau menurut HK Newton III kalau tidak salah memiliki gaya reaksi yang sama besar tapi berlawanan arah. Maaf kalau salah 🙏',
                'Izin melengkapi, dalam konteks Hukum Newton III, setiap gaya aksi selalu memiliki gaya reaksi yang sama besar tetapi berlawanan arah. Namun, meskipun gaya yang bekerja sama besar, efek yang ditimbulkan pada benda bisa berbeda, terutama jika massa kedua benda tidak sama. Misalnya, ketika seseorang mendorong sebuah truk yang diam, orang tersebut memberikan gaya aksi pada truk, dan truk memberikan gaya reaksi yang sama besar ke orang tersebut. Namun, karena massa truk jauh lebih besar dibandingkan massa orang, percepatan yang dihasilkan pada truk sangat kecil hingga hampir tidak terlihat (ΣF = m.a dengan m besar, maka a kecil). Sebaliknya, jika truk menabrak orang, gaya aksi-reaksi tetap sama besar, tetapi karena massa orang jauh lebih kecil, percepatan yang dialaminya jauh lebih besar, menyebabkan orang terdorong atau terjatuh. Kasus lain adalah ketika Anda melompat dari perahu. Anda mendorong perahu ke belakang (aksi), dan perahu mendorong Anda ke depan (reaksi). Karena massa perahu lebih besar, ia hanya sedikit bergerak mundur, sedangkan tubuh Anda yang massanya lebih kecil terdorong lebih jauh ke depan. Jadi, meskipun gaya aksi-reaksi selalu sama besar, efeknya berbeda karena percepatan yang dialami benda bergantung pada massanya sesuai dengan Hukum Newton II (a = F/m).'
            ]
        ];

        $jawaban_str_with_number = implode("\n", array_map(function($item, $index) {
            return ($index + 1) . '. \"' . $item . "\"";
        }, $diskusi['jawaban'], array_keys($diskusi['jawaban'])));

        $kriteria_penilaian_jawaban = [
            [
                'nama' => 'Relevansi Jawaban',
                'kondisi' => 'Jawaban tersebut relevan dengan pertanyaan tersebut',
            ],
            [
                'nama' => 'Kedalaman dan Kompleksitas',
                'kondisi' => 'Jawaban tersebut memuat informasi yang cukup mendalam tentang pertanyaan tersebut',
            ],
            [
                'nama' => 'Keakuratan',
                'kondisi' => 'Jawaban tersebut memuat informasi yang jelas tentang pertanyaan tersebut',
            ],
        ];

        $kriteria_penilaian_jawaban_str = implode(', ', array_map(function ($kriteria) {
            return $kriteria['nama'] . ' (' . $kriteria['kondisi'] . ')';
        }, $kriteria_penilaian_jawaban));

        $promt = [
            Content::parse(role: Role::USER, part: 'Hello, kamu adalah AI yang bisa menilai suatu diskusi terhadap suatu materi tertentu, dan berdasarkan suatu kriteria penilaian.'),

            Content::parse(role: Role::MODEL, part: "Baik, saya akan memulai.Berikan saya data nama_materi, ringkasan_ebook, pertanyaan_sebelumnya, kriteria_penilaian_pertanyaan, dan diskusi yang relevan dengan materi tersebut. Saya akan merespons dalam format JSON"),
        ];

        $chat = Gemini::chat()->startChat(history: $promt);

        $response = $chat->sendMessage("Nama materi: \"" . $nama_materi . "\", ringkasan: \"" . $ringkasan_ebook_str . "\", pertanyaan sebelumnya: \"" . $pertanyaan_sebelumnya_str . "\", kriteria penilaian pertanyaan: \"" . $kriteria_penilaian_str . "\", dan kriteria penilaian jawaban: \"" . $kriteria_penilaian_jawaban_str . "\", jawaban saat ini: " . $jawaban_str_with_number . ". Adapun pertanyaan yang harus kamu nilai yaitu: \"" . $diskusi['pertanyaan'] . "\", dengan memerhatikan kriteria penilaian. Response dalam format JSON: {pertanyaan: { relevansi: 1 atau 0, kedalaman_pertanyaan: 1 atau 0, orisinalitas_pertanyaan: 1 atau 0, kejelasan_struktur: 1 atau 0 }, jawaban: [{ number: urutan pertanyaan, relevansi: 1 atau 0, kedalaman_jawaban: 1 atau 0, keakuratan: 1 atau 0 }]}");

        // parse response json
        $text = $response->text();
        $jsonStart = strpos($text, '{');
        $jsonEnd = strrpos($text, '}') + 1;
        $jsonString = substr($text, $jsonStart, $jsonEnd - $jsonStart);
        $parsedResponse = json_decode($jsonString, true);

        return response()->json($parsedResponse);
    }
}
