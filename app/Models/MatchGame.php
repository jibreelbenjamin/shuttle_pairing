<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchGame extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'tournament_id',
        'babak',
        'urutan',
        'participant1_id',
        'participant2_id',
        'pemenang_id',
        'is_by',
        'is_selesai',
    ];

    protected $casts = [
        'is_by' => 'boolean',
        'is_selesai' => 'boolean',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function participant1(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant1_id');
    }

    public function participant2(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant2_id');
    }

    public function pemenang(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'pemenang_id');
    }
}
