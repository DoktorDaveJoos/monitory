<?php

namespace App\Console\Commands;

use App\Jobs\PerformCheck;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class ScheduleChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:schedule-checks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        PerformCheck::dispatch();

        //        $url = text(
        //            label: 'Enter the URL to check',
        //            default: 'https://google.com'
        //        );
        //
        //        $expectedStatusCode = select(
        //            label: 'Select the expected status code',
        //            options: [
        //                '200' => 200,
        //                '201' => 201,
        //                '202' => 202,
        //                '204' => 204,
        //                '400' => 400,
        //                '401' => 401,
        //                '403' => 403,
        //                '404' => 404,
        //                '500' => 500,
        //                '502' => 502,
        //                '503' => 503,
        //            ],
        //            default: 200
        //        );
        //
        //        $startTime = null;
        //        $endTime = null;
        //
        //        $response = spin(
        //            function () use (&$startTime, &$endTime, $url) {
        //                $startTime = microtime(true);
        //                $test = Http::get($url);
        //                $endTime = microtime(true);
        //
        //                return $test;
        //            },
        //            'Checking URL...'
        //        );
        //
        //        $executionTime = ($endTime - $startTime) * 1000;
        //
        //        if ($response->status() !== $expectedStatusCode) {
        //            error('The status code does not match the expected status code');
        //        } else {
        //            \Laravel\Prompts\info('The status code matches the expected status code');
        //        }
        //
        //        note('See details:');
        //        table([
        //            ['Status', $response->status()],
        //            ['Response Time', (int) $executionTime.'ms'],
        //        ]);

    }
}
