<?php

namespace Feature;

use App\Actions\MonitorStrategies\PingMonitorStrategy;
use App\Enums\ActionType;
use App\Models\Monitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Before;
use Tests\TestCase;

class PingMonitorStrategyTest extends TestCase
{
    use RefreshDatabase;

    const HOST = '192.168.1.1';

    #[Before]
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_check_is_returned(): void
    {
        Process::fake();

        $monitor = Monitor::factory()->create([
            'user_id' => $this->user->id,
            'type' => ActionType::PING,
            'url' => self::HOST,
        ]);

        $strategy = PingMonitorStrategy::make(
            attributes: $monitor->toArray()
        );

        $check = $strategy->check();

        $this->assertNotNull($check);
        $this->assertThat($check->monitor_id, $this->equalTo($monitor->id));

        Process::assertRan('ping -c 1 '.self::HOST);
    }

    public function test_check_is_returned_with_failed_ping(): void
    {
        Process::fake([
            '*' => Process::result(
                output: 'Test output',
                errorOutput: 'Test error output',
                exitCode: 1,
            ),
        ]);

        $monitor = Monitor::factory()->create([
            'user_id' => $this->user->id,
            'type' => ActionType::PING,
            'url' => self::HOST,
        ]);

        $strategy = PingMonitorStrategy::make(
            attributes: $monitor->toArray()
        );

        $check = $strategy->check();

        $this->assertNotNull($check);
        $this->assertThat($check->monitor_id, $this->equalTo($monitor->id));
        $this->assertThat($check->value, $this->equalTo(0));

        Process::assertRan('ping -c 1 '.self::HOST);
    }

    public function test_check_value_is_0_when_100_percent_packet_loss(): void
    {
        Process::fake([
            '*' => Process::result(
                output: 'PING 192.168.1.1 (192.168.1.1): 56 data bytes
                        36 bytes from p3e9bf5e2.dip0.t-ipconnect.de (62.155.245.226): Destination Net Unreachable
                        Vr HL TOS  Len   ID Flg  off TTL Pro  cks      Src      Dst
                        4  5  00 5400 481d   0 0000  3f  01 fe81 192.168.178.184  192.168.1.1


                        --- 192.168.1.1 ping statistics ---
                        1 packets transmitted, 0 packets received, 100.0% packet loss',
            ),
        ]);

        $monitor = Monitor::factory()->create([
            'user_id' => $this->user->id,
            'type' => ActionType::PING,
            'url' => self::HOST,
        ]);

        $strategy = PingMonitorStrategy::make(
            attributes: $monitor->toArray()
        );

        $check = $strategy->check();

        $this->assertNotNull($check);
        $this->assertThat($check->monitor_id, $this->equalTo($monitor->id));
        $this->assertThat($check->value, $this->equalTo(0));

        Process::assertRan('ping -c 1 '.self::HOST);
    }

    public function test_check_value_is_1_when_0_percent_packet_loss(): void
    {
        Process::fake([
            '*' => Process::result(
                output: 'PING google.de (142.250.185.195): 56 data bytes
                        64 bytes from 142.250.185.195: icmp_seq=0 ttl=118 time=35.234 ms

                        --- google.de ping statistics ---
                        1 packets transmitted, 1 packets received, 0.0% packet loss',
            ),
        ]);

        $monitor = Monitor::factory()->create([
            'user_id' => $this->user->id,
            'type' => ActionType::PING,
            'url' => self::HOST,
        ]);

        $strategy = PingMonitorStrategy::make(
            attributes: $monitor->toArray()
        );

        $check = $strategy->check();

        $this->assertNotNull($check);
        $this->assertThat($check->monitor_id, $this->equalTo($monitor->id));
        $this->assertThat($check->value, $this->equalTo(1));

        Process::assertRan('ping -c 1 '.self::HOST);
    }
}
