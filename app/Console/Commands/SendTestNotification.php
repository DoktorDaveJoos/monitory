<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\TriggerAlert;
use Illuminate\Console\Command;

class SendTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-test-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Send a test notification
        $user = User::first();

        $user->notify(new TriggerAlert('eCovery Landingpage', 1, ['HTTP Status Code Not Equals 200 triggered.']));
    }
}
