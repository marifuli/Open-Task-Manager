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
        Schema::create('point_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_point_id')->constrained()->onDelete('cascade'); // Assuming points are related to a user point record
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('set null'); // Optional, if points are related to a specific task
            $table->integer('points')->default(0);
            $table->string('reason'); // e.g., 'Task completed', 'Bonus points', etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_histories');
    }
};
