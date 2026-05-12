@extends('layouts.app')

@section('title', $tournament->nama)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $tournament->nama }}</h1>
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium
                            @if($tournament->status == 'pendaftaran') bg-yellow-100 text-yellow-800
                            @elseif($tournament->status == 'berlangsung') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ ucfirst($tournament->status) }}
                        </span>
                    </div>
                    <div class="mt-2 flex items-center space-x-6 text-sm text-gray-500">
                        @if($tournament->tempat)
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $tournament->tempat }}
                            </span>
                        @endif
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $tournament->jumlah_peserta }} Peserta
                        </span>
                        @if($tournament->tanggal_mulai)
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $tournament->tanggal_mulai->format('d M Y') }}
                                @if($tournament->tanggal_selesai)
                                    - {{ $tournament->tanggal_selesai->format('d M Y') }}
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-wrap gap-3">
                @if($tournament->status == 'pendaftaran')
                    <a href="{{ route('tournament.import.form', $tournament) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Peserta
                    </a>
                    @if($tournament->participants->count() >= 2)
                        <form action="{{ route('tournament.generate.bracket', $tournament) }}" method="POST" onsubmit="return confirm('Generate bracket akan mengacak semua peserta menjadi pasangan. Lanjutkan?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Generate Bracket
                            </button>
                        </form>
                    @endif
                @endif

                @if($tournament->status == 'berlangsung' || $tournament->status == 'selesai')
                    <form action="{{ route('tournament.reset.bracket', $tournament) }}" method="POST" onsubmit="return confirm('Reset bracket? Semua pertandingan akan dihapus.')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Bracket
                        </button>
                    </form>
                @endif

                <a href="{{ route('tournament.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

   <!-- Bracket Visualization -->
    @if($babaks->isNotEmpty())
        @php
            /**
             * ── Label Babak ──────────────────────────────────────────────────────
             * Babak terakhir        → "Final"
             * Babak ke-2 dari akhir → "Semifinal"
             * Sisanya               → "Babak 1", "Babak 2", …
             */
            $maxBabak    = $babaks->keys()->max();
            $babakKeys   = $babaks->keys()->sort()->values(); // [1,2,3,…] sorted
            $totalRounds = $babakKeys->count();

            $babakNames = [];
            foreach ($babakKeys as $i => $key) {
                if ($i === $totalRounds - 1) {
                    $babakNames[$key] = 'Final';
                } elseif ($i === $totalRounds - 2) {
                    $babakNames[$key] = 'Semifinal';
                } else {
                    $babakNames[$key] = 'Babak ' . ($i + 1);
                }
            }

            /**
             * ── Posisi Kartu (Bracket Formula) ───────────────────────────────────
             *
             * cardH  = tinggi satu match-card (px) — harus sinkron dengan CSS
             * minGap = jarak antar kartu di round 1 (px)
             * unit   = cardH + minGap
             *
             * Untuk round ke-r (0-indexed) dan kartu ke-i (0-indexed):
             *   topPx = ( 2^r × (i + 0.5) − 0.5 ) × unit
             *
             * Derivasinya:
             *   • Round 1: kartu i → center = i × unit + cardH/2
             *   • Round 2: kartu i → center = rata-rata center(2i) dan center(2i+1)
             *                              = (2i + 0.5) × unit + cardH/2
             *   • dst. secara rekursif, spacing antar kartu = 2^r × unit
             *
             * Total tinggi area = N × unit  (N = jumlah match di round 1)
             */
            $cardH  = 70;   // px — 2 baris × (py-2 × 2 + line-height ~20px) + border
            $minGap = 60;   // px — gap minimum antar kartu di round pertama
            $unit   = $cardH + $minGap;
            $N      = $babaks->first()->count();
            $totalH = $N * $unit;
        @endphp

        {{-- Podium: Juara 1, 2, 3 --}}
        @if($tournament->status == 'selesai')
            @php
                $finalMatch = $tournament->matches->where('babak', $maxBabak)->first();
                $semifinalMatches = $tournament->matches->where('babak', $maxBabak - 1);

                // Juara 1 = pemenang final
                $juara1 = $finalMatch?->pemenang;

                // Juara 2 = lawan dari pemenang final (runner-up)
                $juara2 = null;
                if ($finalMatch && $finalMatch->pemenang_id) {
                    $juara2 = $finalMatch->pemenang_id == $finalMatch->participant1_id
                        ? $finalMatch->participant2
                        : $finalMatch->participant1;
                }

                // Juara 3 = pemenang dari match semifinal yang KALAH di final
                // Ambil pemenang dari semifinal yang bukan juara 1
                $juara3 = null;
                if ($semifinalMatches->count() >= 2) {
                    $semifinalPemenang = $semifinalMatches->pluck('pemenang_id')->filter()->values();
                    // Pemenang semifinal yang bukan juara 1
                    $juara3Id = $semifinalPemenang->first(fn($id) => $id != $juara1?->id);
                    if ($juara3Id) {
                        $juara3 = $tournament->participants->firstWhere('id', $juara3Id);
                    }
                }
            @endphp

            @if($juara1 || $juara2 || $juara3)
                <div class="flex items-end justify-center gap-4">
                    {{-- Juara 2 --}}
                    @if($juara2)
                        <div class="flex-shrink-0">
                            <div class="bg-gradient-to-br from-gray-300 to-gray-400
                                        rounded-2xl shadow-lg p-5 border-2 border-gray-200 text-center w-58">
                                <div class="text-4xl mb-2">🥈</div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-1">Juara 2</h3>
                                <p class="text-base font-extrabold text-white leading-tight truncate">{{ $juara2->nama }}</p>
                                @if($juara2->pb)
                                    <p class="text-xs text-gray-100 mt-1 truncate">{{ $juara2->pb }}</p>
                                @endif
                                <div class="mt-2 inline-block bg-white/20 rounded-full px-3 py-0.5">
                                    <span class="text-xs font-bold text-white">#2</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Juara 1 (lebih tinggi) --}}
                    @if($juara1)
                        <div class="flex-shrink-0 -mt-4">
                            <div class="bg-gradient-to-br from-yellow-400 via-yellow-500 to-yellow-600
                                        rounded-2xl shadow-xl p-6 border-4 border-yellow-300 text-center w-62">
                                <div class="text-5xl mb-3">🥇</div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-2">Juara 1</h3>
                                <p class="text-lg font-extrabold text-white leading-tight truncate">{{ $juara1->nama }}</p>
                                @if($juara1->pb)
                                    <p class="text-sm text-yellow-100 mt-1 truncate">{{ $juara1->pb }}</p>
                                @endif
                                <div class="mt-3 inline-block bg-white/20 rounded-full px-4 py-1">
                                    <span class="text-sm font-bold text-white">#1</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Juara 3 --}}
                    @if($juara3)
                        <div class="flex-shrink-0">
                            <div class="bg-gradient-to-br from-amber-600 to-amber-700
                                        rounded-2xl shadow-lg p-5 border-2 border-amber-500 text-center w-58">
                                <div class="text-4xl mb-2">🥉</div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-1">Juara 3</h3>
                                <p class="text-base font-extrabold text-white leading-tight truncate">{{ $juara3->nama }}</p>
                                @if($juara3->pb)
                                    <p class="text-xs text-amber-100 mt-1 truncate">{{ $juara3->pb }}</p>
                                @endif
                                <div class="mt-2 inline-block bg-white/20 rounded-full px-3 py-0.5">
                                    <span class="text-xs font-bold text-white">#3</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Bracket Pertandingan</h2>
            </div>

            <div class="p-6 overflow-x-auto">
                <div class="relative inline-flex items-start" id="bracket-outer">

                    @foreach($babaks as $babak => $matches)
                        @php
                            $babakName = $babakNames[$babak];
                            $isLast    = ($babak == $maxBabak);

                            // Posisi round ini (0-indexed)
                            $roundIdx = $babakKeys->search($babak);
                            $pow      = pow(2, $roundIdx);
                            $firstTop = ($pow * 0.5 - 0.5) * $unit;   // top kartu pertama
                            $spacing  = $pow * $unit;                   // jarak antar kartu
                        @endphp

                        {{-- Round Column --}}
                        <div class="bracket-round flex-shrink-0"
                             style="width:270px;{{ !$isLast ? 'margin-right:52px;' : '' }}"
                             data-round="{{ $babak }}">

                            {{-- Header --}}
                            <div class="text-center mb-4">
                                <div class="inline-block bg-gradient-to-r from-indigo-500 to-indigo-600
                                            text-white text-xs font-bold px-5 py-2 rounded-full
                                            uppercase tracking-wider shadow-md">
                                    {{ $babakName }}
                                </div>
                                <p class="text-xs text-gray-400 mt-1">{{ $matches->count() }} pertandingan</p>
                            </div>

                            {{-- Match Area: tinggi tetap, kartu absolute ──────── --}}
                            <div class="relative" style="height:{{ $totalH }}px;">

                                @foreach($matches as $index => $match)
                                    @php
                                        $isBy      = $match->is_by;
                                        $isSelesai = $match->is_selesai;
                                        $p1Menang  = $match->pemenang_id && $match->pemenang_id == $match->participant1_id;
                                        $p2Menang  = $match->pemenang_id && $match->pemenang_id == $match->participant2_id;
                                        $cardTop   = round($firstTop + $index * $spacing);
                                    @endphp

                                    @if($isBy)
                                        {{-- ── BY Card: peserta lolos otomatis, BUKAN pertandingan ── --}}
                                        @php $byPeserta = $match->participant1 ?? $match->participant2; @endphp
                                        <div class="match-card absolute overflow-hidden rounded-lg border-2 border-dashed border-purple-300 bg-purple-50 shadow-sm"
                                             style="width:270px;top:{{ $cardTop }}px;"
                                             data-babak="{{ $babak }}"
                                             data-index="{{ $index }}"
                                             data-is-by="true">
                                            <div class="flex items-center px-3 py-2">
                                                {{-- Badge BY --}}
                                                <div class="flex-shrink-0 mr-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-200 text-purple-700 uppercase tracking-wider">
                                                        BY
                                                    </span>
                                                </div>
                                                {{-- Nama peserta --}}
                                                <div class="flex-1 min-w-0 mr-1">
                                                    @if($byPeserta)
                                                        <p class="text-sm font-semibold text-purple-800 truncate">{{ $byPeserta->nama }}</p>
                                                        <p class="text-xs text-purple-400 truncate">{{ $byPeserta->pb ?? 'Lolos Otomatis' }}</p>
                                                    @else
                                                        <p class="text-sm font-semibold text-purple-400 truncate">Lolos Otomatis</p>
                                                    @endif
                                                </div>
                                                {{-- Status / Tombol konfirmasi --}}
                                                @if($isSelesai)
                                                    <svg class="flex-shrink-0 w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @else
                                                    <button type="button"
                                                        onclick="if(confirm('Konfirmasi {{ $byPeserta?->nama }} lolos otomatis (BY)?')) { document.getElementById('by-{{ $match->id }}').submit(); }"
                                                        class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-500 hover:bg-purple-600 text-white flex items-center justify-center transition-colors text-xs font-bold"
                                                        title="Konfirmasi BY">
                                                        ✓
                                                    </button>
                                                    <form id="by-{{ $match->id }}" action="{{ route('tournament.set.pemenang', $match) }}" method="POST" class="hidden">
                                                        @csrf
                                                        <input type="hidden" name="pemenang_id" value="{{ $match->participant1_id ?? $match->participant2_id }}">
                                                    </form>
                                                @endif
                                            </div>
                                        </div>{{-- /by-card --}}

                                    @else
                                        {{-- ── Match Card biasa: dua peserta bertanding ── --}}
                                        <div class="match-card absolute bg-white rounded-lg shadow-md border-2
                                                    {{ $isSelesai ? 'border-green-400' : 'border-gray-300' }}
                                                    overflow-hidden"
                                             style="width:270px;top:{{ $cardTop }}px;"
                                             data-babak="{{ $babak }}"
                                             data-index="{{ $index }}">

                                            {{-- Participant 1 --}}
                                            <div class="flex items-center px-3 py-2 border-b border-gray-100
                                                        {{ $p1Menang ? 'bg-green-50' : 'bg-white' }}">
                                                <div class="flex-shrink-0 w-5 h-5 rounded-full mr-2 flex items-center justify-center
                                                            {{ $p1Menang ? 'bg-green-500' : 'bg-gray-300' }}">
                                                    @if($p1Menang)
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @else
                                                        <span class="text-[10px] font-bold text-white">{{ $index * 2 + 1 }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0 mr-1">
                                                    @if($match->participant1)
                                                        <p class="text-sm font-semibold truncate {{ $p1Menang ? 'text-green-700' : 'text-gray-800' }}">
                                                            {{ $match->participant1->nama }}
                                                        </p>
                                                        <p class="text-xs text-gray-400 truncate">{{ $match->participant1->pb ?? '-' }}</p>
                                                    @else
                                                        <p class="text-sm font-semibold text-gray-400 truncate">&mdash;</p>
                                                        <p class="text-xs text-gray-300 truncate">&mdash;</p>
                                                    @endif
                                                </div>
                                                @if(!$isSelesai && $match->participant1_id && $match->participant2_id)
                                                    <button type="button"
                                                        onclick="if(confirm('Apakah {{ $match->participant1->nama }} sebagai pemenang?')) { document.getElementById('p1-{{ $match->id }}').submit(); }"
                                                        class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center transition-colors"
                                                        title="Pilih {{ $match->participant1->nama }} sebagai pemenang">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                    <form id="p1-{{ $match->id }}" action="{{ route('tournament.set.pemenang', $match) }}" method="POST" class="hidden">
                                                        @csrf
                                                        <input type="hidden" name="pemenang_id" value="{{ $match->participant1_id }}">
                                                    </form>
                                                @elseif($p1Menang)
                                                    <span class="flex-shrink-0 text-xs font-bold text-green-600">✓</span>
                                                @endif
                                            </div>

                                            {{-- Participant 2 --}}
                                            <div class="flex items-center px-3 py-2 {{ $p2Menang ? 'bg-green-50' : 'bg-white' }}">
                                                <div class="flex-shrink-0 w-5 h-5 rounded-full mr-2 flex items-center justify-center
                                                            {{ $p2Menang ? 'bg-green-500' : 'bg-gray-300' }}">
                                                    @if($p2Menang)
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @else
                                                        <span class="text-[10px] font-bold text-white">{{ $index * 2 + 2 }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0 mr-1">
                                                    @if($match->participant2)
                                                        <p class="text-sm font-semibold truncate {{ $p2Menang ? 'text-green-700' : 'text-gray-800' }}">
                                                            {{ $match->participant2->nama }}
                                                        </p>
                                                        <p class="text-xs text-gray-400 truncate">{{ $match->participant2->pb ?? '-' }}</p>
                                                    @else
                                                        <p class="text-sm font-semibold text-gray-400 truncate">&mdash;</p>
                                                        <p class="text-xs text-gray-300 truncate">&mdash;</p>
                                                    @endif
                                                </div>
                                                @if(!$isSelesai && $match->participant1_id && $match->participant2_id)
                                                    <button type="button"
                                                        onclick="if(confirm('Apakah {{ $match->participant2->nama }} sebagai pemenang?')) { document.getElementById('p2-{{ $match->id }}').submit(); }"
                                                        class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center transition-colors"
                                                        title="Pilih {{ $match->participant2->nama }} sebagai pemenang">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                    <form id="p2-{{ $match->id }}" action="{{ route('tournament.set.pemenang', $match) }}" method="POST" class="hidden">
                                                        @csrf
                                                        <input type="hidden" name="pemenang_id" value="{{ $match->participant2_id }}">
                                                    </form>
                                                @elseif($p2Menang)
                                                    <span class="flex-shrink-0 text-xs font-bold text-green-600">✓</span>
                                                @endif
                                            </div>

                                        </div>{{-- /match-card --}}
                                    @endif
                                @endforeach

                            </div>{{-- /match-area --}}
                        </div>{{-- /bracket-round --}}
                    @endforeach

                    {{-- SVG overlay garis konektor (z di belakang kartu) --}}
                    <svg id="bracket-svg"
                         style="position:absolute;top:0;left:0;pointer-events:none;overflow:visible;z-index:0;"
                         aria-hidden="true"></svg>

                </div>{{-- /bracket-outer --}}
            </div>
        </div>

    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada bracket</h3>
            <p class="mt-2 text-sm text-gray-500">Import peserta terlebih dahulu, lalu generate bracket untuk memulai turnamen.</p>
            @if($tournament->status == 'pendaftaran')
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('tournament.import.form', $tournament) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Peserta
                    </a>
                </div>
            @endif
        </div>
    @endif

    {{-- ====================================================================
         Script: gambar garis konektor SVG setelah DOM siap.
         Kartu sudah diposisi dengan formula bracket di PHP, JS hanya
         membaca posisi DOM aktual dan menggambar garis penghubung.
         ==================================================================== --}}
    <script>
    (function () {
        'use strict';

        function drawBracketLines() {
            var outer = document.getElementById('bracket-outer');
            var svg   = document.getElementById('bracket-svg');
            if (!outer || !svg) return;

            // Kosongkan SVG
            while (svg.firstChild) svg.removeChild(svg.firstChild);

            var outerRect = outer.getBoundingClientRect();
            svg.setAttribute('width',  outer.offsetWidth);
            svg.setAttribute('height', outer.offsetHeight);

            var rounds = Array.from(outer.querySelectorAll('.bracket-round'));

            for (var r = 0; r < rounds.length - 1; r++) {
                var curCards  = Array.from(rounds[r].querySelectorAll('.match-card'));
                var nextCards = Array.from(rounds[r + 1].querySelectorAll('.match-card'));

                // midX = tengah gap antara dua kolom
                var curRight = rounds[r].getBoundingClientRect().right     - outerRect.left;
                var nxtLeft  = rounds[r + 1].getBoundingClientRect().left  - outerRect.left;
                var midX     = (curRight + nxtLeft) / 2;

                nextCards.forEach(function (dest, m) {
                    var src1 = curCards[m * 2];
                    var src2 = curCards[m * 2 + 1];
                    if (!src1 || !dest) return;

                    var src1IsBy = src1.dataset.isBy === 'true';
                    var src2IsBy = src2 ? src2.dataset.isBy === 'true' : false;

                    var rd  = dest.getBoundingClientRect();
                    var xDst = rd.left  - outerRect.left;
                    var yd   = rd.top + rd.height / 2 - outerRect.top;

                    if (!src1IsBy && src2 && !src2IsBy) {
                        // ── Kasus normal: keduanya match biasa ──
                        var r1   = src1.getBoundingClientRect();
                        var r2   = src2.getBoundingClientRect();
                        var xSrc = r1.right - outerRect.left;
                        var y1   = r1.top + r1.height / 2 - outerRect.top;
                        var y2   = r2.top + r2.height / 2 - outerRect.top;

                        addLine(svg, xSrc, y1, midX, y1);   // horizontal src1
                        addLine(svg, xSrc, y2, midX, y2);   // horizontal src2
                        addLine(svg, midX, y1, midX, y2);   // vertikal penghubung
                        addLine(svg, midX, yd, xDst, yd);   // horizontal ke dest

                    } else if (!src1IsBy && (!src2 || src2IsBy)) {
                        // ── src2 adalah BY atau tidak ada: hanya tarik garis dari src1 ──
                        var r1   = src1.getBoundingClientRect();
                        var xSrc = r1.right - outerRect.left;
                        var y1   = r1.top + r1.height / 2 - outerRect.top;

                        addLine(svg, xSrc, y1, midX, y1);              // horizontal src1
                        if (Math.abs(y1 - yd) > 1) {
                            addLine(svg, midX, y1, midX, yd);          // vertikal ke dest
                        }
                        addLine(svg, midX, yd, xDst, yd);              // horizontal ke dest

                    } else if (src1IsBy && src2 && !src2IsBy) {
                        // ── src1 adalah BY: hanya tarik garis dari src2 ──
                        var r2   = src2.getBoundingClientRect();
                        var xSrc = r2.right - outerRect.left;
                        var y2   = r2.top + r2.height / 2 - outerRect.top;

                        addLine(svg, xSrc, y2, midX, y2);              // horizontal src2
                        if (Math.abs(y2 - yd) > 1) {
                            addLine(svg, midX, y2, midX, yd);          // vertikal ke dest
                        }
                        addLine(svg, midX, yd, xDst, yd);              // horizontal ke dest
                    }
                    // Jika keduanya BY → tidak ada garis sama sekali
                });
            }
        }

        function addLine(svg, x1, y1, x2, y2) {
            var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', x1.toFixed(1));
            line.setAttribute('y1', y1.toFixed(1));
            line.setAttribute('x2', x2.toFixed(1));
            line.setAttribute('y2', y2.toFixed(1));
            line.setAttribute('stroke', '#cbd5e1');
            line.setAttribute('stroke-width', '2');
            line.setAttribute('stroke-linecap', 'round');
            svg.appendChild(line);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', drawBracketLines);
        } else {
            requestAnimationFrame(drawBracketLines);
        }

        window.addEventListener('resize', drawBracketLines);
    })();
    </script>

    <!-- Participants List -->
    @if($tournament->participants->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Peserta ({{ $tournament->participants->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PB</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tournament->participants as $index => $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $p->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p->pb ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection