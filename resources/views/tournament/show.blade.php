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
                <form action="{{ route('tournament.reset.bracket', $tournament) }}" method="POST" onsubmit="return confirm('Reset bracket? Semua pertandingan akan dihapus.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset Bracket
                    </button>
                </form>
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
            // $babaks sudah difilter di controller (hanya babak dengan minimal 1 non-BY match)
            $babakKeys   = $babaks->keys()->sort()->values();
            $totalRounds = $babakKeys->count();

            // Label babak berdasarkan urutan tampil (bukan nomor babak asli)
            // sehingga BY tidak mengacak label
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

            // Hitung tinggi bracket berdasarkan match non-BY di babak pertama yang ditampilkan
            $firstBabakKey = $babakKeys->first();
            $cardH  = 70;
            $minGap = 60;
            $unit   = $cardH + $minGap;
            // Hitung N dari match NON-BY di babak pertama yang ditampilkan
            $N      = $babaks[$firstBabakKey]->where('is_by', false)->count();
            $totalH = max($N, 1) * $unit;
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" id="bracket-container">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Bracket Pertandingan</h2>
                <button onclick="exportBracket('bracket-{{ Str::slug($tournament->nama) }}.png')" id="export-btn"
                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span id="export-label">Export Gambar</span>
                </button>
            </div>

            <div class="p-6 overflow-x-auto" id="bracket-content">
                <div class="p-3 relative inline-flex items-start" id="bracket-outer">

                    @foreach($babaks as $babak => $matches)
                        @php
                            $babakName   = $babakNames[$babak];
                            $isLast      = ($babak == $babakKeys->max());
                            $roundIdx    = $babakKeys->search($babak);
                            $pow         = pow(2, $roundIdx);
                            $firstTop    = ($pow * 0.5 - 0.5) * $unit;
                            $spacing     = $pow * $unit;
                            // Hanya tampilkan match NON-BY
                            $visibleMatches = $matches->where('is_by', false)->values();
                        @endphp

                        <div class="bracket-round flex-shrink-0"
                             style="width:270px;{{ !$isLast ? 'margin-right:52px;' : '' }}"
                             data-round="{{ $babak }}">

                            <div class="text-center mb-4">
                                <div class="mt-1 inline-block bg-gradient-to-r from-indigo-500 to-indigo-600
                                            text-white text-xs font-bold px-5 py-2 rounded-full
                                            uppercase tracking-wider shadow-md">
                                    {{ $babakName }}
                                </div>
                            </div>

                            <div class="relative" style="height:{{ $totalH }}px;">
                                @foreach($visibleMatches as $index => $match)
                                    @php
                                        $cardTop  = round($firstTop + $index * $spacing);
                                        $hasP1    = !is_null($match->participant1);
                                        $hasP2    = !is_null($match->participant2);
                                        $isEmpty  = !$hasP1 && !$hasP2;
                                    @endphp

                                    <div class="match-card absolute bg-white rounded-lg shadow-md border-2 overflow-hidden
                                        {{ $isEmpty ? 'border-dashed border-gray-300 bg-gray-50' : 'border-gray-300' }}"
                                         style="width:270px;top:{{ $cardTop }}px;"
                                         data-babak="{{ $babak }}"
                                         data-index="{{ $index }}">
                                        <div class="flex items-center px-3 py-2 border-b border-gray-100 bg-white">
                                            <div class="flex-shrink-0 w-5 h-5 rounded-full mr-2
                                                {{ $isEmpty ? 'bg-gray-200' : 'bg-gray-300' }} flex items-center justify-center">
                                                <span class="text-[10px] font-bold {{ $isEmpty ? 'text-gray-400' : 'text-white' }}">{{ $index * 2 + 1 }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                @if($hasP1)
                                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $match->participant1->nama }}</p>
                                                    <p class="text-xs text-gray-400 truncate">{{ $match->participant1->pb ?? '-' }}</p>
                                                @else
                                                    <p class="text-sm font-semibold text-gray-400 truncate">...</p>
                                                    <p class="text-xs text-gray-300 truncate">...</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center px-3 py-2 bg-white">
                                            <div class="flex-shrink-0 w-5 h-5 rounded-full mr-2
                                                {{ $isEmpty ? 'bg-gray-200' : 'bg-gray-300' }} flex items-center justify-center">
                                                <span class="text-[10px] font-bold {{ $isEmpty ? 'text-gray-400' : 'text-white' }}">{{ $index * 2 + 2 }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                @if($hasP2)
                                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $match->participant2->nama }}</p>
                                                    <p class="text-xs text-gray-400 truncate">{{ $match->participant2->pb ?? '-' }}</p>
                                                @else
                                                    <p class="text-sm font-semibold text-gray-400 truncate">...</p>
                                                    <p class="text-xs text-gray-300 truncate">...</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{-- SVG overlay garis konektor --}}
                    <svg id="bracket-svg"
                         style="position:absolute;top:0;left:0;pointer-events:none;overflow:visible;z-index:0;"
                         aria-hidden="true"></svg>

                </div>
            </div>
        </div>

        {{-- Script: gambar garis konektor --}}
        <script>
        (function () {
            'use strict';

            function drawBracketLines() {
                var outer = document.getElementById('bracket-outer');
                var svg   = document.getElementById('bracket-svg');
                if (!outer || !svg) return;

                while (svg.firstChild) svg.removeChild(svg.firstChild);

                var outerRect = outer.getBoundingClientRect();
                svg.setAttribute('width',  outer.offsetWidth);
                svg.setAttribute('height', outer.offsetHeight);

                var rounds = Array.from(outer.querySelectorAll('.bracket-round'));

                for (var r = 0; r < rounds.length - 1; r++) {
                    var curCards  = Array.from(rounds[r].querySelectorAll('.match-card'));
                    var nextCards = Array.from(rounds[r + 1].querySelectorAll('.match-card'));

                    var curRight = rounds[r].getBoundingClientRect().right     - outerRect.left;
                    var nxtLeft  = rounds[r + 1].getBoundingClientRect().left  - outerRect.left;
                    var midX     = (curRight + nxtLeft) / 2;

                    nextCards.forEach(function (dest, m) {
                        var src1 = curCards[m * 2];
                        var src2 = curCards[m * 2 + 1];
                        if (!src1 || !dest) return;

                        var rd   = dest.getBoundingClientRect();
                        var xDst = rd.left + rd.width / 2 - outerRect.left; // tengah card tujuan
                        var xDstEdge = rd.left - outerRect.left;             // tepi kiri card tujuan
                        var yd   = rd.top + rd.height / 2 - outerRect.top;

                        if (src2) {
                            var r1   = src1.getBoundingClientRect();
                            var r2   = src2.getBoundingClientRect();
                            var xSrc = r1.right - outerRect.left;
                            var y1   = r1.top + r1.height / 2 - outerRect.top;
                            var y2   = r2.top + r2.height / 2 - outerRect.top;

                            addLine(svg, xSrc, y1, midX, y1);
                            addLine(svg, xSrc, y2, midX, y2);
                            addLine(svg, midX, y1, midX, y2);
                            addLine(svg, midX, yd, xDstEdge, yd);
                        } else {
                            var r1   = src1.getBoundingClientRect();
                            var xSrc = r1.right - outerRect.left;
                            var y1   = r1.top + r1.height / 2 - outerRect.top;

                            addLine(svg, xSrc, y1, midX, y1);
                            if (Math.abs(y1 - yd) > 1) {
                                addLine(svg, midX, y1, midX, yd);
                            }
                            addLine(svg, midX, yd, xDstEdge, yd);
                        }
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

        @vite('resources/js/bracket-export.js')

    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada bracket</h3>
            @if($tournament->participants->count() >= 2)
                <p class="mt-2 text-sm text-gray-500">Seluruh peserta telah diinput, silahkan generate bracket untuk memulai tournament.</p>
                <div class="mt-6 flex justify-center">
                    <form action="{{ route('tournament.generate.bracket', $tournament) }}" method="POST" onsubmit="return confirm('Generate bracket akan mengacak semua peserta menjadi pasangan. Lanjutkan?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Generate Bracket
                        </button>
                    </form>
                </div>
            @else
                <p class="mt-2 text-sm text-gray-500">Import peserta terlebih dahulu, lalu generate bracket untuk memulai turnamen.</p>
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

    <!-- Participants List -->
    @if($tournament->participants->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Peserta ({{ $tournament->participants->count() }})</h2>
                @if($babaks->isEmpty())
                    <form action="{{ route('tournament.participant.deleteAll', $tournament) }}" method="POST" onsubmit="return confirm('Hapus semua peserta? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Semua
                        </button>
                    </form>
                @else
                    <button type="button" onclick="alert('Reset bracket terlebih dahulu sebelum menghapus peserta.')" class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Semua
                    </button>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PB</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tournament->participants as $index => $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $p->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p->pb ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if($babaks->isEmpty())
                                        <form action="{{ route('tournament.participant.delete', [$tournament, $p]) }}" method="POST" onsubmit="return confirm('Hapus peserta {{ $p->nama }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus peserta">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" onclick="alert('Reset bracket terlebih dahulu sebelum menghapus peserta.')" class="text-gray-400 cursor-not-allowed" title="Reset bracket terlebih dahulu">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
