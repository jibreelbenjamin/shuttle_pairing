<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    protected $fillable = [
        'nama',
        'tempat',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'jumlah_peserta',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchGame::class);
    }
}
