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
        Schema::table('triggers', function (Blueprint $table) {
            // make column operator & value nullable
            $table->string('operator')->nullable()->change();
            $table->float('value')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('triggers', function (Blueprint $table) {
            $table->string('operator')->nullable(false)->change();
            $table->float('value')->nullable(false)->change();
        });
    }
};
