<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        User::all()->each(function (User $user) {
            $user->settings = [
                'mail' => true,
                'slack' => false,
                'sms' => false,
            ];
            $user->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::all()->each(function (User $user) {
            $user->settings = null;
            $user->save();
        });
    }
};
