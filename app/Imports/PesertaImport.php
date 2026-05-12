<?php

namespace App\Imports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PesertaImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $tournamentId;

    public function __construct($tournamentId)
    {
        $this->tournamentId = $tournamentId;
    }

    public function model(array $row)
    {
        return new Participant([
            'tournament_id' => $this->tournamentId,
            'nama' => $row['nama'],
            'pb' => $row['pb'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'pb' => 'nullable|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Kolom nama wajib diisi.',
        ];
    }
}
