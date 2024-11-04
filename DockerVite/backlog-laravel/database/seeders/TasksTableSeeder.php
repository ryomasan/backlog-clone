<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Task::create([
        //     'name' => '引越し準備',
        //     'tags' => ['tag1', 'tag2'],
        //     'status' => '完了'
        // ]);

        DB::table('tasks')->insert([
            [
                'id' => '1',
                'name' => '引越し準備',
                'status' => '未対応',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '2',
                'name' => '掃除',
                'status' => '処理中',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
