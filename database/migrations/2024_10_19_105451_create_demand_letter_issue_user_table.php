<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandLetterIssueUserTable extends Migration
{
    public function up()
    {
        Schema::create('demand_letter_issue_user', function (Blueprint $table) {
            $table->id(); // Optional: auto-incrementing ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('demand_letter_issue_id')->constrained()->onDelete('cascade');
            $table->json('candidate_list')->nullable(); // Candidate list as JSON
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('demand_letter_issue_user');
    }
}
