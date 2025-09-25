<?php

namespace Database\Factories;

use App\Models\System\Analytics;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\System\Analytics>
 */
class AnalyticsFactory extends Factory
{
    protected $model = Analytics::class;

    public function definition(): array
    {
        $entityTypes = ['vehicle', 'user', 'transaction', 'page_view', 'search'];
        $entityType = fake()->randomElement($entityTypes);

        return [
            'type' => fake()->randomElement(['page_views', 'unique_visitors', 'conversions', 'revenue', 'engagement']),
            'entity_type' => $entityType,
            'entity_id' => fake()->numberBetween(1, 1000),
            'metric' => fake()->randomElement(['count', 'sum', 'average', 'percentage']),
            'value' => fake()->randomFloat(2, 0, 10000),
            'date' => fake()->dateTimeBetween('-1 year', 'now'),
            'metadata' => [
                'source' => fake()->randomElement(['website', 'mobile_app', 'api']),
                'user_agent' => fake()->userAgent(),
                'ip_address' => fake()->ipv4(),
                'session_id' => fake()->uuid(),
            ],
            'is_active' => fake()->boolean(95),
            'added_by_id' => null,
        ];
    }

    public function pageViews(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'page_views',
            'metric' => 'count',
            'entity_type' => 'page_view',
        ]);
    }

    public function revenue(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'revenue',
            'metric' => 'sum',
            'value' => fake()->randomFloat(2, 10, 10000),
        ]);
    }
}