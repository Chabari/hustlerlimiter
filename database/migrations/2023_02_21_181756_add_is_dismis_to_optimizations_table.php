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
        Schema::table('optimizations', function (Blueprint $table) {
            //
            $table->boolean('isDismiss')->default(false);
            $table->string('date_approved')->default('none');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('optimizations', function (Blueprint $table) {
            //
            $table->dropColumn('isDismiss');
            $table->dropColumn('date_approved');
        });
    }
};
