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

        // Kelompokkan match berdasarkan babak
        $babaks = $tournament->matches->groupBy('babak')->sortKeys();

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

    // Simpan match babak 1 TANPA membuat match BYE
    public function generateBracket(Tournament $tournament)
    {
        $participants = $tournament->participants()->get();

        if ($participants->count() < 2) {
            return back()->withErrors('Minimal 2 peserta untuk membuat bracket.');
        }

        $tournament->matches()->delete();
        $participants = $participants->shuffle();

        $pasangan = [];
        $temp = [];
        foreach ($participants as $p) {
            $temp[] = $p;
            if (count($temp) == 2) {
                $pasangan[] = $temp;
                $temp = [];
            }
        }

        $byParticipant = count($temp) === 1 ? $temp[0] : null;

        $urutan = 1;
        foreach ($pasangan as $pair) {
            MatchGame::create([
                'tournament_id'  => $tournament->id,
                'babak'          => 1,
                'urutan'         => $urutan++,
                'participant1_id'=> $pair[0]->id,
                'participant2_id'=> $pair[1]->id,
                'is_by'          => false,
                'is_selesai'     => false,
            ]);
        }

        // BY participant dibuat match BY di babak 1 (lolos otomatis)
        // agar ikut dalam pool pemenang saat advance round
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

        // Generate struktur bracket untuk babak selanjutnya
        $this->generateNextRounds($tournament);

        $tournament->update(['status' => 'berlangsung']);

        return redirect()->route('tournament.show', $tournament)
            ->with('success', 'Bracket berhasil digenerate!');
    }

    // Generate struktur bracket untuk semua babak setelah babak 1
    // BY participant sudah dibuat match BY di babak 1, jadi ikut dalam pool pemenang
    private function generateNextRounds(Tournament $tournament)
    {
        // Hitung semua match babak 1 (termasuk BY match) sebagai slot
        $matchesBabak1 = $tournament->matches()->where('babak', 1)->count();

        // Total slot babak 2 = semua pemenang babak 1 (termasuk BY participant)
        $totalSlots = $matchesBabak1;
        $babak      = 2;

        while ($totalSlots > 1) {
            $matchCount = (int) floor($totalSlots / 2);
            $sisaSlot   = $totalSlots % 2;

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

            // Jika ada sisa (ganjil), tambahkan 1 match BY (kosong)
            // Akan diisi oleh pemenang yang tersisa saat advance round
            if ($sisaSlot > 0) {
                $matchCount++;
                MatchGame::create([
                    'tournament_id'   => $tournament->id,
                    'babak'           => $babak,
                    'urutan'          => $matchCount,
                    'participant1_id' => null,
                    'participant2_id' => null,
                    'is_by'           => true,
                    'is_selesai'      => false,
                ]);
            }

            $totalSlots = $matchCount;
            $babak++;
        }
    }

    // Menentukan pemenang pertandingan
    public function setPemenang(Request $request, MatchGame $match)
    {
        $request->validate([
            'pemenang_id' => 'required|exists:participants,id',
        ]);

        $match->update([
            'pemenang_id' => $request->pemenang_id,
            'is_selesai' => true,
        ]);

        // Jika match BY, pemenang sudah otomatis
        if ($match->is_by) {
            $match->update(['is_selesai' => true]);
        }

        // Cek apakah semua match di babak ini sudah selesai
        $this->checkAndAdvanceRound($match->tournament_id, $match->babak);

        return redirect()->route('tournament.show', $match->tournament_id)
            ->with('success', 'Pemenang pertandingan berhasil ditentukan!');
    }

    // Advance ke babak berikutnya, mengisi slot yang kosong dengan pemenang
    private function checkAndAdvanceRound($tournamentId, $babak)
    {
        $matchesInBabak = MatchGame::where('tournament_id', $tournamentId)
            ->where('babak', $babak)
            ->get();

        $allSelesai = $matchesInBabak->every(fn($m) => $m->is_selesai);
        if (!$allSelesai) return;

        // Babak final (hanya 1 match) → tournament selesai
        if ($matchesInBabak->count() === 1) {
            Tournament::find($tournamentId)->update(['status' => 'selesai']);
            return;
        }

        $babakSelanjutnya = $babak + 1;
        $nextMatches = MatchGame::where('tournament_id', $tournamentId)
            ->where('babak', $babakSelanjutnya)
            ->orderBy('urutan')
            ->get();

        // Kumpulkan pemenang babak ini (termasuk dari match BY), urut berdasarkan urutan match
        $pemenang = $matchesInBabak->sortBy('urutan')
            ->pluck('pemenang_id')
            ->filter()
            ->values();
        $idx = 0;

        foreach ($nextMatches as $nextMatch) {
            // Isi slot yang masih kosong
            if (!$nextMatch->participant1_id && isset($pemenang[$idx])) {
                $nextMatch->participant1_id = $pemenang[$idx++];
            }
            if (!$nextMatch->participant2_id && isset($pemenang[$idx])) {
                $nextMatch->participant2_id = $pemenang[$idx++];
            }

            // Jika setelah diisi hanya 1 peserta (lawannya tidak ada) → auto BY
            if ($nextMatch->participant1_id && !$nextMatch->participant2_id) {
                $nextMatch->is_by       = true;
                $nextMatch->is_selesai  = true;
                $nextMatch->pemenang_id = $nextMatch->participant1_id;
            } elseif (!$nextMatch->participant1_id && $nextMatch->participant2_id) {
                $nextMatch->is_by       = true;
                $nextMatch->is_selesai  = true;
                $nextMatch->pemenang_id = $nextMatch->participant2_id;
            }

            $nextMatch->save();
        }

        // Rekursif: cek apakah babak selanjutnya juga perlu auto-advance
        $this->checkAndAdvanceRound($tournamentId, $babakSelanjutnya);
    }

    // Hapus tournament
    public function destroy(Tournament $tournament)
    {
        $tournament->delete();
        return redirect()->route('tournament.index')->with('success', 'Turnamen berhasil dihapus.');
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
