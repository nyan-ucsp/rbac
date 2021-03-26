<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'user_name' => 'superadmin',
            'password' => bcrypt('superadmin23'),
            'role_id' => 1,
            'organization' => 'ananda_ark',
            'created_at' => Carbon::now(),
        ]);
    }
}
