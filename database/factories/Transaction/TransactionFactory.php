<?php

namespace Database\Factories\Transaction;

use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'type' => $this->faker->randomElement(['sale', 'rental', 'lease']),
            'amount' => $this->faker->randomFloat(2, 100, 100000),
            'currency' => 'USD',
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled', 'paid', 'refunded', 'disputed']),
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'cash', 'paypal']),
            'notes' => $this->faker->sentence(),
            'metadata' => json_encode(['key' => 'value']),
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
        ];
    }

    /**
     * Indicate that the transaction is a sale.
     */
    public function sale(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sale',
            'amount' => $this->faker->randomFloat(2, 5000, 150000),
        ]);
    }

    /**
     * Indicate that the transaction is a rental.
     */
    public function rental(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'rental',
            'amount' => $this->faker->randomFloat(2, 50, 1000),
        ]);
    }

    /**
     * Indicate that the transaction is a lease.
     */
    public function lease(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'lease',
            'amount' => $this->faker->randomFloat(2, 1000, 5000),
        ]);
    }

    /**
     * Indicate that the transaction is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the transaction is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the transaction is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the transaction is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }

    /**
     * Indicate that the transaction is disputed.
     */
    public function disputed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'disputed',
        ]);
    }

    /**
     * Indicate a high-value transaction.
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $this->faker->randomFloat(2, 50000, 500000),
        ]);
    }

    /**
     * Indicate a recent transaction.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}
