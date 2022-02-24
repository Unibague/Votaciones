<?php

namespace Database\Seeders;

use App\Models\Voter;
use Illuminate\Database\Seeder;

class VoterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $headerWithTranslation = [
            'name' => [
                'translation' => 'Nombre',
            ],
            'identification_number' => [
                'translation' => 'Identificación',
            ],
            'faculty_code' => [
                'translation' => 'Cód. Facultad',
            ],
            'program_code' => [
                'translation' => 'Cód. Programa',
            ],
        ];
        $users = \App\Helpers\CSV2Seeders::generateSeederArray(base_path('database/users.csv'), ';', $headerWithTranslation);
        foreach ($users as $user) {
            Voter::create([
                'name' => $user['name'],
                'identification_number' => $user['identification_number'],
                'faculty_code' => $user['faculty_code'],
                'program_code' => $user['program_code'],
            ]);
        }
    }
}
