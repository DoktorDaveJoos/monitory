<?php

namespace Tests\Feature;

use App\Actions\PerformCheckAction;
use App\Actions\PerformCheckValidation;
use App\DTOs\MonitorPassableDTO;
use App\Enums\ActionType;
use App\Enums\HttpMethod;
use App\Enums\Operator;
use App\Enums\TriggerType;
use App\Models\Check;
use App\Models\Monitor;
use App\Models\Trigger;
use App\Models\User;
use Database\Factories\CheckFactory;
use Database\Factories\TriggerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PerformCheckValidationTest extends TestCase
{
    use RefreshDatabase;

    #[Before]
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_alert_is_triggered_when_check_fails(): void
    {
        Http::fake(
            fn ($request) => Http::response(['status' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR)
        );

        $monitor = Monitor::factory()
            ->has(
                Trigger::factory()
                    ->state([
                        'type' => TriggerType::HTTP_STATUS_CODE,
                        'value' => Response::HTTP_OK,
                        'operator' => Operator::NOT_EQUALS,
                    ])
            )->create([
                'type' => ActionType::HTTP,
                'method' => HttpMethod::GET,
                'user_id' => $this->user->id,
            ]);

        $result = PerformCheckAction::run(
            monitorPassableDTO: MonitorPassableDTO::make($monitor),
            next: fn (MonitorPassableDTO $dto) => PerformCheckValidation::run(
                monitorPassableDTO: $dto,
                next: fn (MonitorPassableDTO $dto) => $dto
            )
        );

        $this->assertTrue($result->failed());

        $this->assertDatabaseHas('checks', [
            'monitor_id' => $monitor->id,
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]);

        Http::assertSentCount(1);
    }

    public function test_alert_is_not_triggered_when_check_passes(): void
    {
        Http::fake([
            '*' => Http::response(['status' => 'ok'], Response::HTTP_OK),
        ]);

        $monitor = Monitor::factory()
            ->has(
                Trigger::factory()
                    ->state([
                        'type' => TriggerType::HTTP_STATUS_CODE,
                        'value' => Response::HTTP_OK,
                        'operator' => Operator::NOT_EQUALS,
                    ])
            )->create([
                'type' => ActionType::HTTP,
                'method' => HttpMethod::GET,
                'user_id' => $this->user->id,
            ]);

        $result = PerformCheckAction::run(
            monitorPassableDTO: MonitorPassableDTO::make($monitor),
            next: fn (MonitorPassableDTO $dto) => PerformCheckValidation::run(
                monitorPassableDTO: $dto,
                next: fn (MonitorPassableDTO $dto) => $dto
            )
        );

        $this->assertFalse($result->failed());

        $this->assertDatabaseHas('checks', [
            'monitor_id' => $monitor->id,
            'status_code' => Response::HTTP_OK,
        ]);

        Http::assertSentCount(1);
    }

    /**
     * This test is to ensure that the trigger is evaluated correctly.
     * Which means no alert is triggered for given check and trigger.
     *
     * @throws ReflectionException
     */
    #[DataProvider('shouldNotTriggerAlertProvider')]
    public function test_evaluate_trigger_returns_false(TriggerFactory $triggerFactory, CheckFactory $checkFactory)
    {
        $class = new PerformCheckValidation;
        $reflection = new ReflectionClass($class);
        $method = $reflection->getMethod('evaluateTrigger');

        $this->assertFalse(
            // Call the protected method PerformCheckValidation::evaluateTrigger
            $method->invokeArgs($class, [$triggerFactory->make(), $checkFactory->make()])
        );
    }

    /**
     * @throws ReflectionException
     */
    #[DataProvider('shouldTriggerAlertProvider')]
    public function test_evaluate_trigger_passes(TriggerFactory $triggerFactory, CheckFactory $checkFactory)
    {
        $class = new PerformCheckValidation;
        $reflection = new ReflectionClass($class);
        $method = $reflection->getMethod('evaluateTrigger');

        $this->assertTrue(
            // Call the protected method PerformCheckValidation::evaluateTrigger
            $method->invokeArgs($class, [$triggerFactory->make(), $checkFactory->make()])
        );
    }

    /**
     * Examples which are not triggering the alert.
     *
     * @return array[]
     */
    public static function shouldNotTriggerAlertProvider(): array
    {
        return [
            'status code = 200, given 202' => [
                Trigger::factory()->state([
                    'type' => TriggerType::HTTP_STATUS_CODE,
                    'operator' => Operator::EQUALS,
                    'value' => Response::HTTP_OK,
                ]),
                Check::factory()->state([
                    'status_code' => Response::HTTP_ACCEPTED,
                ]),
            ],
            'status code >= 400, given 200' => [
                Trigger::factory()->state([
                    'type' => TriggerType::HTTP_STATUS_CODE,
                    'operator' => Operator::GREATER_THAN_OR_EQUALS,
                    'value' => Response::HTTP_BAD_REQUEST,
                ]),
                Check::factory()->state([
                    'status_code' => Response::HTTP_OK,
                ]),
            ],
            'status code != 500, given 500' => [
                Trigger::factory()->state([
                    'type' => TriggerType::HTTP_STATUS_CODE,
                    'operator' => Operator::NOT_EQUALS,
                    'value' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ]),
                Check::factory()->state([
                    'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ]),
            ],
            'status code <= 301, given 404' => [
                Trigger::factory()->state([
                    'type' => TriggerType::HTTP_STATUS_CODE,
                    'operator' => Operator::LESS_THAN_OR_EQUALS,
                    'value' => Response::HTTP_MOVED_PERMANENTLY,
                ]),
                Check::factory()->state([
                    'status_code' => Response::HTTP_NOT_FOUND,
                ]),
            ],
            'latency < 500, given 250' => [
                Trigger::factory()->state([
                    'type' => TriggerType::LATENCY,
                    'operator' => Operator::GREATER_THAN,
                    'value' => 500,
                ]),
                Check::factory()->state([
                    'response_time' => 250,
                ]),
            ],
        ];
    }

    public static function shouldTriggerAlertProvider(): array
    {
        return [
            'status code 200' => [
                Trigger::factory()->state([
                    'type' => TriggerType::HTTP_STATUS_CODE,
                    'operator' => Operator::EQUALS,
                    'value' => Response::HTTP_OK,
                ]),
                Check::factory()->state([
                    'status_code' => Response::HTTP_OK,
                ]),
            ],
            'status code < 500' => [
                Trigger::factory()->state([
                    'type' => TriggerType::HTTP_STATUS_CODE,
                    'operator' => Operator::LESS_THAN,
                    'value' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ]),
                Check::factory()->state([
                    'status_code' => Response::HTTP_NOT_FOUND,
                ]),
            ],
            'status code != 404' => [
                Trigger::factory()->state([
                    'type' => TriggerType::HTTP_STATUS_CODE,
                    'operator' => Operator::NOT_EQUALS,
                    'value' => Response::HTTP_NOT_FOUND,
                ]),
                Check::factory()->state([
                    'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ]),
            ],
            'status code >= 200' => [
                Trigger::factory()->state([
                    'type' => TriggerType::HTTP_STATUS_CODE,
                    'operator' => Operator::GREATER_THAN_OR_EQUALS,
                    'value' => Response::HTTP_OK,
                ]),
                Check::factory()->state([
                    'status_code' => Response::HTTP_OK,
                ]),
            ],
            'latency > 1000' => [
                Trigger::factory()->state([
                    'type' => TriggerType::LATENCY,
                    'operator' => Operator::GREATER_THAN,
                    'value' => 1000,
                ]),
                Check::factory()->state([
                    'response_time' => 2000,
                ]),
            ],
        ];
    }
}
