<?php

use App\Http\Controllers\RandomizerController;

Route::get('/randomizer', [RandomizerController::class, 'index'])->name('randomizer.form');
Route::get('/randomizer/template', [RandomizerController::class, 'template'])->name('randomizer.template');
Route::post('/randomizer/import', [RandomizerController::class, 'randomize'])->name('randomizer.import');