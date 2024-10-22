<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminApprovedToPreDemandLettersTable extends Migration
{
    public function up()
    {
        Schema::table('pre_demand_letters', function (Blueprint $table) {
            $table->json('admin_approved_pre_demand')->nullable(); // Adding the new JSON column
        });
    }

    public function down()
    {
        Schema::table('pre_demand_letters', function (Blueprint $table) {
            $table->dropColumn('admin_approved_pre_demand');
        });
    }
}
