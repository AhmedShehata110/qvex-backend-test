<?php

namespace Database\Factories;

use App\Models\System\FailedJob;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\System\FailedJob>
 */
class FailedJobFactory extends Factory
{
    protected $model = FailedJob::class;

    public function definition(): array
    {
        $failedAt = fake()->dateTimeBetween('-30 days', 'now');
        $retryCount = fake()->numberBetween(0, 5);

        return [
            'uuid' => (string) Str::uuid(),
            'connection' => fake()->randomElement(['database', 'redis', 'sqs', 'sync']),
            'queue' => fake()->randomElement(['default', 'emails', 'notifications', 'reports', 'high', 'low']),
            'payload' => [
                'uuid' => (string) Str::uuid(),
                'displayName' => fake()->randomElement([
                    'App\\Jobs\\SendWelcomeEmail',
                    'App\\Jobs\\ProcessPayment',
                    'App\\Jobs\\GenerateReport',
                    'App\\Jobs\\SendNotification',
                    'App\\Jobs\\UpdateInventory',
                ]),
                'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
                'maxTries' => fake()->numberBetween(1, 5),
                'maxExceptions' => fake()->numberBetween(1, 3),
                'failOnTimeout' => fake()->boolean(),
                'backoff' => fake()->optional(0.5)->numberBetween(0, 300),
                'timeout' => fake()->optional(0.5)->numberBetween(30, 3600),
                'data' => [
                    'commandName' => fake()->randomElement([
                        'App\\Jobs\\SendWelcomeEmail',
                        'App\\Jobs\\ProcessPayment',
                        'App\\Jobs\\GenerateReport',
                    ]),
                    'command' => base64_encode(serialize([
                        'user_id' => fake()->numberBetween(1, 1000),
                        'email' => fake()->email(),
                    ])),
                ],
            ],
            'exception' => fake()->randomElement([
                'Illuminate\\Database\\QueryException: SQLSTATE[23000]: Integrity constraint violation',
                'Swift_TransportException: Expected response code 250 but got code "550"',
                'GuzzleHttp\\Exception\\ConnectException: cURL error 28: Operation timed out',
                'Illuminate\\Contracts\\Filesystem\\FileNotFoundException',
                'ErrorException: Undefined variable $user',
            ]),
            'failed_at' => $failedAt,
            'retried_at' => fake()->optional(0.3)->dateTimeBetween($failedAt, 'now'),
            'retry_count' => $retryCount,
            'max_retries' => fake()->numberBetween($retryCount, 5),
            'is_active' => fake()->boolean(95),
            'added_by_id' => null,
        ];
    }

    public function databaseConnection(): static
    {
        return $this->state(fn (array $attributes) => [
            'connection' => 'database',
            'exception' => 'Illuminate\\Database\\QueryException: SQLSTATE[23000]: Integrity constraint violation',
        ]);
    }

    public function emailQueue(): static
    {
        return $this->state(fn (array $attributes) => [
            'queue' => 'emails',
            'payload' => array_merge($attributes['payload'], [
                'displayName' => 'App\\Jobs\\SendWelcomeEmail',
            ]),
        ]);
    }

    public function retried(): static
    {
        return $this->state(fn (array $attributes) => [
            'retried_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'retry_count' => fake()->numberBetween(1, 3),
        ]);
    }
}
