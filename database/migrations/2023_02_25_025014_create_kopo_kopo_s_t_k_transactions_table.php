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
        Schema::create('kopo_kopo_s_t_k_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('amount');
            $table->string('reference');
            $table->string('trans_id');
            $table->string('status');
            $table->string('senderPhoneNumber');
            $table->string('tillNumber');
            $table->string('senderFirstName');
            $table->string('senderLastName');
            $table->string('request_reference');
            $table->string('customer_id');
            $table->string('result_desc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kopo_kopo_s_t_k_transactions');
    }
};
