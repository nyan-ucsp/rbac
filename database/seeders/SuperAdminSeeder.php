<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'id' => 1,
            'role_title' => 'Super Admin',
            'role_slug' => '@sa',
            'role_active' => 1,
            'role_description' => 'Super Admin (The user who control all statement)',
            'role_content' => 'This is the super admin of the office.',
            'created_at' => Carbon::now(),
        ]);
    }
}
