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
        Schema::table('pre_demand_letters', function (Blueprint $table) {
            //
            Schema::table('pre_demand_letters', function (Blueprint $table) {
                $table->json('approved_agency_list')->default(json_encode([]))->after('status'); // Add a new column
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_demand_letters', function (Blueprint $table) {
            $table->dropColumn('approved_agency_list'); // Remove the column when rolling back
        });
    }
};
