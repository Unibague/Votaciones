<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Get all faculties
        $faculties = Faculty::all();
        //Create a table for each faculty, based on the name of the faculty
        foreach ($faculties as $faculty){
            Table::create([
                'name' => 'Mesa de '.$faculty->name,
                'faculty_code' => $faculty->code,
            ]);
        }
    }
}
