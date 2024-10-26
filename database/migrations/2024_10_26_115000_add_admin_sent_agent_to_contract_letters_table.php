<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminSentAgentToContractLettersTable extends Migration
{
    public function up()
    {
        Schema::table('contract_letters', function (Blueprint $table) {
            $table->date('admin_sent_agent')->nullable()->after('custom_message');
        });
    }

    public function down()
    {
        Schema::table('contract_letters', function (Blueprint $table) {
            $table->dropColumn('admin_sent_agent');
        });
    }
}
