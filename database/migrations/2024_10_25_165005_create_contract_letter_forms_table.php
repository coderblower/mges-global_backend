<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('contract_letter_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_letter_id'); // Ensure this is set correctly
            $table->string('contract_title');
            $table->string('employers_title');
            $table->string('work_address');
            $table->string('employer_phone');
            $table->string('email');
            $table->text('description');
            $table->date('issued_date');
            $table->timestamps();
        
            // Add foreign key constraint if needed
            $table->foreign('contract_letter_id')->references('id')->on('contract_letters')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_letter_forms');
    }
};
