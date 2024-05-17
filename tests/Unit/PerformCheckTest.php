<?php

namespace Tests\Unit;

use App\Jobs\PerformCheck;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PerformCheckTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_check_http_success(): void
    {

        $job = PerformCheck::make(
            action: HttpAction::make(
                url: 'https://google.com',
                expectedStatusCode: 200
            )
        );

        $job->handle();
    }
}
