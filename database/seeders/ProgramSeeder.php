<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
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
                'translation' => 'Programa',
            ],
            'code' => [
                'translation' => 'Codigo',
            ],

        ];
        $faculties = \App\Helpers\CSV2Seeders::generateSeederArray(base_path('database/programs.csv'), ';', $headerWithTranslation);
        foreach ($faculties as $faculty) {
            Program::create([
                'name' => $faculty['name'],
                'code' => $faculty['code'],
            ]);
        }
    }
}
