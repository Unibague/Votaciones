<?php

namespace App\Imports;

use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class VotersImport implements ToCollection
{
    public $voters;
    public $errors = [];

    private const PROGRAM_TO_FACULTY_MAP = [
        '11' => '10',
        '12' => '10',
        '13' => '10',
        '14' => '10',
        '15' => '10',
        '16' => '10',
        '51' => '10',
        '52' => '10',
        '32' => '20',
        '33' => '20',
        '34' => '20',
        '35' => '20',
        '21' => '30',
        '22' => '30',
        '23' => '30',
        '24' => '30',
        '25' => '30',
        '26' => '30',
        '42' => '30',
        '61' => '30',
    ];

    public function collection(Collection $rows)
    {
        $data = $rows->skip(1);
        $this->voters = collect();

        $data->each(function ($row, $index) {
            $identification = $this->normalizeSpreadsheetValue($row[0] ?? null);
            $name = trim((string) ($row[1] ?? ''));
            $programCode = $this->normalizeCode($row[2] ?? null);
            $email = trim((string) ($row[3] ?? ''));
            $facultyCode = self::PROGRAM_TO_FACULTY_MAP[$programCode] ?? null;

            $programExists = Program::where('code', $programCode)->exists();
            $facultyExists = $facultyCode !== null
                && Faculty::where('code', $facultyCode)->exists();

            if (!$programExists) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'identification_number' => $identification,
                    'program_code' => $programCode,
                    'reason' => 'Codigo de programa no encontrado en la base de datos',
                ];
                return;
            }

            if (!$facultyExists) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'identification_number' => $identification,
                    'program_code' => $programCode,
                    'faculty_code' => $facultyCode,
                    'reason' => 'No se pudo determinar la facultad para el codigo de programa suministrado',
                ];
                return;
            }

            $this->voters->push([
                'identification_number' => $identification,
                'name' => $name,
                'program_code' => $programCode,
                'faculty_code' => $facultyCode,
                'email' => $email,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    private function normalizeSpreadsheetValue($value): string
    {
        return trim((string) $value);
    }

    private function normalizeCode($value): string
    {
        $normalizedValue = $this->normalizeSpreadsheetValue($value);

        if ($normalizedValue === '') {
            return '';
        }

        if (is_numeric($normalizedValue)) {
            return (string) intval($normalizedValue);
        }

        return preg_replace('/\.0+$/', '', $normalizedValue);
    }
}
