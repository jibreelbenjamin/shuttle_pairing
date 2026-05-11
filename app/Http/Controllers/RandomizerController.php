<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RandomizerController extends Controller
{
    // Halaman form upload & download template
    public function index()
    {
        return view('randomizer.form');
    }

    // Download template CSV
    public function template()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_peserta.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Header
            fputcsv($file, ['nama', 'pb']);
            // Contoh data (bisa dikosongkan atau diberi contoh)
            fputcsv($file, ['Contoh Nama', 'PB Contoh']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Proses upload & randomize
    public function randomize(Request $request)
    {
        $request->validate([
            'file_peserta' => 'required|file|mimes:csv,txt',
        ]);

        // Baca file CSV
        $file = $request->file('file_peserta');
        $path = $file->getRealPath();
        $peserta = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle); // baca header, pastikan ada 'nama' dan 'pb'
            if (!$header || !in_array('nama', $header) || !in_array('pb', $header)) {
                fclose($handle);
                return back()->withErrors('File CSV harus memiliki kolom "nama" dan "pb".');
            }

            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) >= 2) {
                    $peserta[] = [
                        'nama' => trim($data[0]),
                        'pb'   => trim($data[1]),
                    ];
                }
            }
            fclose($handle);
        }

        if (empty($peserta)) {
            return back()->withErrors('File peserta kosong atau tidak valid.');
        }

        // Acak urutan peserta
        shuffle($peserta);

        // Bagi menjadi pasangan (masing-masing 2)
        $pasangan = array_chunk($peserta, 2);

        // Jika jumlah ganjil, peserta terakhir sendirian -> BY
        // array_chunk otomatis membuat elemen terakhir hanya 1 jika ganjil
        // Namun kita ingin menandai BY dengan lebih eksplisit
        $hasil = [];
        foreach ($pasangan as $pair) {
            if (count($pair) == 2) {
                $hasil[] = [
                    'tipe' => 'pasangan',
                    'pemain1' => $pair[0],
                    'pemain2' => $pair[1],
                ];
            } else {
                // Hanya 1 pemain = bye
                $hasil[] = [
                    'tipe' => 'bye',
                    'pemain1' => $pair[0],
                    'pemain2' => null,
                ];
            }
        }

        return view('randomizer.result', compact('hasil'));
    }
}