<?php

namespace App\Http\Controllers;

use App\Imports\PesertaImport;
use App\Models\MatchGame;
use App\Models\Participant;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TournamentController extends Controller
{
    // Daftar semua tournament
    public function index()
    {
        $tournaments = Tournament::orderBy('created_at', 'desc')->get();
        return view('tournament.index', compact('tournaments'));
    }

    // Form buat tournament baru
    public function create()
    {
        return view('tournament.create');
    }

    // Simpan tournament baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tempat' => 'nullable|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        Tournament::create($request->all());

        return redirect()->route('tournament.index')->with('success', 'Turnamen berhasil dibuat!');
    }

    // Detail tournament + bracket
    public function show(Tournament $tournament)
    {
        $tournament->load(['participants', 'matches.participant1', 'matches.participant2', 'matches.pemenang']);

        // Kelompokkan match berdasarkan babak, filter babak yang SEMUA isinya BY
        $babaks = $tournament->matches
            ->groupBy('babak')
            ->sortKeys()
            ->filter(function ($matches) {
                // Tampilkan babak ini hanya jika ada minimal 1 match NON-BY
                return $matches->where('is_by', false)->isNotEmpty();
            });

        return view('tournament.show', compact('tournament', 'babaks'));
    }

    // Form import peserta
    public function importForm(Tournament $tournament)
    {
        return view('tournament.import', compact('tournament'));
    }

    // Proses import peserta dari XLSX
    public function importPeserta(Request $request, Tournament $tournament)
    {
        $request->validate([
            'file_peserta' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new PesertaImport($tournament->id), $request->file('file_peserta'));

            // Update jumlah peserta
            $tournament->update([
                'jumlah_peserta' => $tournament->participants()->count(),
            ]);

            return redirect()->route('tournament.show', $tournament)
                ->with('success', 'Peserta berhasil diimport!');
        } catch (\Exception $e) {
            return back()->withErrors('Gagal import: ' . $e->getMessage());
        }
    }

    // Download template XLSX
    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\PesertaTemplateExport(), 'template_peserta.xlsx');
    }

    // Generate bracket: acak peserta jadi pasangan di babak 1
    // BY participant (jika ganjil) dapat match BY di babak 1 dan otomatis masuk slot babak 2
    public function generateBracket(Tournament $tournament)
    {
        $participants = $tournament->participants()->get();

        if ($participants->count() < 2) {
            return back()->withErrors('Minimal 2 peserta untuk membuat bracket.');
        }

        // Hapus match sebelumnya
        $tournament->matches()->delete();

        // Acak peserta
        $participants = $participants->shuffle();

        // Buat pasangan
        $pasangan = [];
        $temp = [];
        foreach ($participants as $p) {
            $temp[] = $p;
            if (count($temp) == 2) {
                $pasangan[] = $temp;
                $temp = [];
            }
        }

        // Jika ada sisa 1 peserta → BY
        $byParticipant = count($temp) === 1 ? $temp[0] : null;

        // Buat match babak 1 (pasangan biasa)
        $urutan = 1;
        foreach ($pasangan as $pair) {
            MatchGame::create([
                'tournament_id'   => $tournament->id,
                'babak'           => 1,
                'urutan'          => $urutan++,
                'participant1_id' => $pair[0]->id,
                'participant2_id' => $pair[1]->id,
                'is_by'           => false,
                'is_selesai'      => false,
            ]);
        }

        // BY participant: buat match BY di babak 1 (hidden/tidak ditampilkan sebagai babak)
        if ($byParticipant) {
            MatchGame::create([
                'tournament_id'   => $tournament->id,
                'babak'           => 1,
                'urutan'          => $urutan++,
                'participant1_id' => $byParticipant->id,
                'participant2_id' => null,
                'is_by'           => true,
                'is_selesai'      => true,
                'pemenang_id'     => $byParticipant->id,
            ]);
        }

        // Generate struktur bracket kosong untuk babak selanjutnya
        // BY participant otomatis dimasukkan ke slot babak 2
        $this->generateEmptyRounds($tournament, $byParticipant);

        $tournament->update(['status' => 'berlangsung']);

        return redirect()->route('tournament.show', $tournament)
            ->with('success', 'Bracket berhasil digenerate!');
    }

    /**
     * Generate struktur bracket kosong untuk babak selanjutnya.
     *
     * - Hanya match NON-BY babak 1 yang dihitung sebagai slot.
     * - Jika ada BY participant, ia langsung dimasukkan ke slot babak 2
     *   (participant1 atau participant2 dari match pertama yang ada slot kosong).
     * - TIDAK ada match dengan is_by=true di babak > 1.
     */
    private function generateEmptyRounds(Tournament $tournament, $byParticipant = null)
    {
        // Hanya hitung match NON-BY di babak 1 sebagai slot aktif
        $matchesBabak1 = $tournament->matches()
            ->where('babak', 1)
            ->where('is_by', false)
            ->count();

        // Total slot babak 2 = ceil((matchesBabak1 + (by ? 1 : 0)) / 2)
        // Tapi kita generate slot berdasarkan pemenang yang akan ada:
        // pemenang dari matchesBabak1 match biasa + 1 BY participant (jika ada)
        $totalWinners = $matchesBabak1 + ($byParticipant ? 1 : 0);
        $totalSlots   = $matchesBabak1; // slot dari match biasa saja untuk struktur
        $babak        = 2;

        // Untuk babak 2, hitung berdasarkan total pemenang (termasuk BY)
        $currentSlots = $totalWinners;

        while ($currentSlots > 1) {
            $matchCount = (int) ceil($currentSlots / 2);

            for ($i = 1; $i <= $matchCount; $i++) {
                MatchGame::create([
                    'tournament_id'   => $tournament->id,
                    'babak'           => $babak,
                    'urutan'          => $i,
                    'participant1_id' => null,
                    'participant2_id' => null,
                    'is_by'           => false,
                    'is_selesai'      => false,
                ]);
            }

            // Jika ada BY participant dan ini adalah babak 2,
            // masukkan langsung ke slot pertama yang tersedia di babak ini.
            // BY participant mengisi participant1 dari match terakhir babak 2
            // (karena ia berasal dari posisi terakhir babak 1).
            if ($byParticipant && $babak === 2) {
                $lastMatch = $tournament->matches()
                    ->where('babak', 2)
                    ->orderBy('urutan', 'desc')
                    ->first();

                if ($lastMatch) {
                    $lastMatch->update(['participant1_id' => $byParticipant->id]);
                }
            }

            $currentSlots = $matchCount;
            $babak++;
        }
    }

    // Hapus tournament
    public function destroy(Tournament $tournament)
    {
        $tournament->delete();
        return redirect()->route('tournament.index')->with('success', 'Turnamen berhasil dihapus.');
    }

    // Hapus satu peserta
    public function deleteParticipant(Tournament $tournament, Participant $participant)
    {
        if ($participant->tournament_id !== $tournament->id) {
            abort(404);
        }

        $participant->delete();

        // Update jumlah peserta
        $tournament->update([
            'jumlah_peserta' => $tournament->participants()->count(),
        ]);

        return redirect()->route('tournament.show', $tournament)
            ->with('success', 'Peserta berhasil dihapus!');
    }

    // Hapus semua peserta
    public function deleteAllParticipants(Tournament $tournament)
    {
        $tournament->participants()->delete();

        // Reset jumlah peserta
        $tournament->update([
            'jumlah_peserta' => 0,
        ]);

        return redirect()->route('tournament.show', $tournament)
            ->with('success', 'Semua peserta berhasil dihapus!');
    }

    // Reset bracket (generate ulang)
    public function resetBracket(Tournament $tournament)
    {
        $tournament->matches()->delete();
        $tournament->update(['status' => 'pendaftaran']);
        return redirect()->route('tournament.show', $tournament)
            ->with('success', 'Bracket direset. Silakan generate ulang.');
    }
}