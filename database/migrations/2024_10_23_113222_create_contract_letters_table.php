<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractLettersTable extends Migration
{
    public function up()
    {
        Schema::create('contract_letters', function (Blueprint $table) {
            $table->id();
            $table->json('primary_candidates')->nullable(); // Cast as array in model
            $table->json('confirmed_candidates')->nullable(); // Cast as array in model
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('demand_letter_id')->constrained('demand_letter_issues')->onDelete('cascade'); // One-to-one relationship
            $table->timestamp('agency_agree')->nullable();
            $table->timestamp('agency_reject')->nullable();
            $table->timestamp('admin_approve')->nullable();
            $table->timestamp('admin_reject')->nullable();
            $table->json('custom_message')->nullable(); // Cast as array in model
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_letters');
    }
}
