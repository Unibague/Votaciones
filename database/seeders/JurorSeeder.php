<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class JurorSeeder extends Seeder
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
                'translation' => 'Nombre completo',
            ],
            'email' => [
                'translation' => 'Correo electronico institucional',
            ],
            'table_id' => [
                'translation' => 'ID de mesa',
            ],

        ];
        $jurors = \App\Helpers\CSV2Seeders::generateSeederArray(base_path('database/jurors.csv'), ';', $headerWithTranslation);
        foreach ($jurors as $juror) {
            User::create([
                'name' => $juror['name'],
                'email' => $juror['email'],
                'role_id' => 3,
                'table_id' => $juror['table_id'],
                'password' => 'automatic_generate_password'
            ]);
        }

    }
}
