<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use App\Models\Tag;
use Illuminate\Support\Facades\DB;


class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tags')->insert([
            [
                'id' => '1',
                'name' => 'tag1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '2',
                'name' => 'tag2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
