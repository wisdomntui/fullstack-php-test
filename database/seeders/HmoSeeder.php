<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HmoSeeder extends Seeder
{
    private $hmos = [
        ['name' => 'HMO A', 'code' => 'HMO-A', 'email' => 'a@gmail.com', 'batch_by_encounter' => 0],
        ['name' => 'HMO B', 'code' => 'HMO-B', 'email' => 'b@gmail.com', 'batch_by_encounter' => 1],
        ['name' => 'HMO C', 'code' => 'HMO-C', 'email' => 'c@gmail.com', 'batch_by_encounter' => 1],
        ['name' => 'HMO D', 'code' => 'HMO-D', 'email' => 'd@gmail.com', 'batch_by_encounter' => 1],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hmos')->insert($this->hmos);
    }
}
