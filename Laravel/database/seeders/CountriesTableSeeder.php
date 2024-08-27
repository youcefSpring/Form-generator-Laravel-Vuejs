<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use File;
use DB;
class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load the JSON file
        $json = File::get(public_path('countries.json'));
        $countries = json_decode($json, true);

        // Insert data into the countries table
        DB::table('countries')->insert($countries);
    }
}
