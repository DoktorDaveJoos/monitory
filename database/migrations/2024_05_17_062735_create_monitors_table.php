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
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('type');
            $table->string('url');
            $table->integer('expected_status_code');

            $table->integer('frequency')->default(5);
            $table->integer('timeout')->default(60);
            $table->boolean('active')->default(true);

            $table->timestamp('last_checked_at')->nullable();

            // Create indices
            $table->index('frequency');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
