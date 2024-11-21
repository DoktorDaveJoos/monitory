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
        Schema::table('monitors', function (Blueprint $table) {
            $table->string('auth')->after('success')->nullable();
            $table->string('auth_username')->after('success')->nullable();
            $table->string('auth_password')->after('success')->nullable();
            $table->string('auth_token')->after('success')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn('auth');
            $table->dropColumn('auth_username');
            $table->dropColumn('auth_password');
            $table->dropColumn('auth_token');
        });
    }
};
