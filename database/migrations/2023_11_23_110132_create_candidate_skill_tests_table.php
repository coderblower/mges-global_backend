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
        Schema::create('candidate_skill_tests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('skill_id')->nullable();
            $table->string('center_name')->nullable();
            $table->integer('preskill_test')->default(false);
            $table->boolean('crash_training')->default(false);
            $table->integer('skill_test')->default(false);
            $table->boolean('advence_training')->default(false);
            $table->integer('final_test')->default(false);
            $table->string('report')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_skill_tests');
    }
};
