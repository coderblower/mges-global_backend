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
        Schema::create('demand_letters', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('license_no');
            $table->string('visa_number');
            $table->date('visa_date');
            $table->json('positions');
            $table->text('terms_conditions');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('demand_letters');
    }
};
