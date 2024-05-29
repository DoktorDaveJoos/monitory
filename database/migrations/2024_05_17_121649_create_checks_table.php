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
        Schema::create('checks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('monitor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('response_time')->nullable();

            $table->text('response_headers')->nullable();
            $table->text('response_body')->nullable();

            $table->boolean('success')->default(false);

            $table->timestamp('started_at');
            $table->timestamp('finished_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
