<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class NewsTypeSeeder extends Seeder
{
    public function run()
    {
        // Check if record with id=1 already exists
        if (!DB::table('news_type')->where('id', 1)->exists()) {
            DB::table('news_type')->insert([
                'id' => 1,
                'name' => 'Default',
                'description' => 'Default news type'
            ]);
        }
    }
}
