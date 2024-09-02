<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserIdentitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all rows from the table
        $rows = DB::table('users')->get();

        foreach ($rows as $row) {
            DB::table('users')
                ->where('id', $row->id)
                ->update(['identity' => Str::random(10)]);
        }
    }
}
