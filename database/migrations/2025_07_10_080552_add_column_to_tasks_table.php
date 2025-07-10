<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->foreignId('task_status_id')
                ->nullable()
                ->after('priority')
                ->constrained('task_statuses')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('status')->default('to_do')->after('priority');
            $table->dropForeign(['task_status_id']);
            $table->dropColumn('task_status_id');
        });
    }
};
