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

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('name');
            $table->string('type');

            $table->string('url');

            $table->integer('interval')->default(5);
            $table->boolean('active')->default(true);

            $table->string('method')->nullable();
            $table->timestamp('last_checked_at')->nullable();

            $table->integer('alert_count')->default(0);
            $table->boolean('success')->default(false);

            // Create indices
            $table->index('interval');

            $table->timestamps();

            $table->softDeletes();
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
