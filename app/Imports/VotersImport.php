<?php

namespace App\Imports;

use App\Models\Program;
use App\Models\Faculty;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class VotersImport implements ToCollection
{
    public $voters;
    public $errors = [];

    public function collection(Collection $rows)
    {
        $data = $rows->skip(1);
        $this->voters = collect();

        $data->each(function ($row, $index) {
            $identification = $row[0];
            $name = $row[1];
            $programCode = $row[2];
            $email = $row[3];

            $facultyCode = $programCode === '61'
                ? '30'
                : substr($programCode, 0, 1) . '0';

            $programExists = Program::where('code', $programCode)->exists();
            $facultyExists = Faculty::where('code', $facultyCode)->exists();

            if (!$programExists) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'identification_number' => $identification,
                    'program_code' => $programCode,
                    'reason' => 'Código de programa no encontrado en la base de datos'
                ];
                return;
            }

            if (!$facultyExists) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'identification_number' => $identification,
                    'faculty_code' => $facultyCode,
                    'reason' => 'Código de facultad no encontrado en la base de datos'
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
}
