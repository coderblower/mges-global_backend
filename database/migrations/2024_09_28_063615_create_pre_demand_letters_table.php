<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pre_demand_letters', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->json('positions');
            $table->text('terms_conditions');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->json('bd_agency_agree')->change();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pre_demand_letters');
    }
};
