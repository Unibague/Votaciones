<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
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
                'translation' => 'Facultad',
            ],
            'code' => [
                'translation' => 'Cod.',
            ],

        ];
        $faculties = \App\Helpers\CSV2Seeders::generateSeederArray(base_path('database/faculties.csv'), ';', $headerWithTranslation);
        foreach ($faculties as $faculty) {
            Faculty::create([
                'name' => $faculty['name'],
                'code' => $faculty['code'],
            ]);
        }
    }
}
