<?php

use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('tournament.index');
});

Route::prefix('tournament')->name('tournament.')->group(function () {
    Route::get('/', [TournamentController::class, 'index'])->name('index');
    Route::get('/create', [TournamentController::class, 'create'])->name('create');
    Route::post('/', [TournamentController::class, 'store'])->name('store');
    Route::get('/{tournament}', [TournamentController::class, 'show'])->name('show');
    Route::delete('/{tournament}', [TournamentController::class, 'destroy'])->name('destroy');

    // Import peserta
    Route::get('/{tournament}/import', [TournamentController::class, 'importForm'])->name('import.form');
    Route::post('/{tournament}/import', [TournamentController::class, 'importPeserta'])->name('import.peserta');

    // Template download
    Route::get('/template/download', [TournamentController::class, 'downloadTemplate'])->name('template.download');

    // Peserta
    Route::delete('/{tournament}/participants/{participant}', [TournamentController::class, 'deleteParticipant'])->name('participant.delete');
    Route::delete('/{tournament}/participants', [TournamentController::class, 'deleteAllParticipants'])->name('participant.deleteAll');

    // Bracket
    Route::post('/{tournament}/generate-bracket', [TournamentController::class, 'generateBracket'])->name('generate.bracket');
    Route::post('/{tournament}/reset-bracket', [TournamentController::class, 'resetBracket'])->name('reset.bracket');
});
