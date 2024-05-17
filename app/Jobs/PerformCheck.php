<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class PerformCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $url = text(
            label: 'Enter the URL to check',
            default: 'https://google.com'
        );

        $startTime = microtime(true);

        $response = spin(
            fn () => Http::get($url),
            'Checking URL...'
        );

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        table([
            ['Status', $response->status()],
            ['Response Time', $executionTime],
        ]);

    }
}
