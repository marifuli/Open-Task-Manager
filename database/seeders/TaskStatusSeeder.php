<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // todo, planning, pending, working, testing, ios testing, safari testing, review, done, audit, completed, archive
        
        $statuses = [
            ['name' => 'todo', 'label' => 'To Do', 'color' => '#f0f0f0', 'order' => 1],
            ['name' => 'planning', 'label' => 'Planning', 'color' => '#d9edf7', 'order' => 2],
            ['name' => 'pending', 'label' => 'Pending', 'color' => '#f2dede', 'order' => 3],
            ['name' => 'working', 'label' => 'Working', 'color' => '#dff0d8', 'order' => 4],
            ['name' => 'testing', 'label' => 'Testing', 'color' => '#fcf8e3', 'order' => 5],
            ['name' => 'ios_testing', 'label' => 'iOS Testing', 'color' => '#d9edf7', 'order' => 6],
            ['name' => 'safari_testing', 'label' => 'Safari Testing', 'color' => '#d9edf7', 'order' => 7],
            ['name' => 'review', 'label' => 'Review', 'color' => '#dff0d8', 'order' => 8],
            ['name' => 'done', 'label' => 'Done', 'color' => '#dff0d8', 'order' => 9],
            ['name' => 'audit', 'label' => 'Audit', 'color' => '#f2dede', 'order' => 10],
            ['name' => 'completed', 'label' => 'Completed', 'color' => '#dff0d8', 'order' => 11],
            ['name' => 'archive', 'label' => 'Archive', 'color' => '#f0f0f0', 'order' => 12],
        ];
        
        foreach ($statuses as $status) {
            \App\Models\TaskStatus::create($status);
        }
    }
}
