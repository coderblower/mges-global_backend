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
        DB::table('pre_demand_letters')->get()->each(function ($item) {
            // Convert boolean values to empty array or user ID array
            if ($item->bd_agency_agree === true) {
                DB::table('pre_demand_letters')
                    ->where('id', $item->id)
                    ->update(['bd_agency_agree' => json_encode([1])]); // Replace `1` with the correct user ID
            } else {
                DB::table('pre_demand_letters')
                    ->where('id', $item->id)
                    ->update(['bd_agency_agree' => json_encode([])]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_demand_letters', function (Blueprint $table) {
            //
        });
    }
};
