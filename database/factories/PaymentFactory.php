<?php

namespace Database\Factories;

use App\Models\Transaction\Payment;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 100, 50000);
        $status = fake()->randomElement(['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded']);

        return [
            'transaction_id' => Transaction::factory(),
            'payment_method' => fake()->randomElement(['credit_card', 'debit_card', 'bank_transfer', 'paypal', 'stripe', 'cash']),
            'payment_reference' => 'PAY-'.fake()->unique()->regexify('[A-Z0-9]{12}'),
            'gateway_transaction_id' => fake()->optional(0.9)->regexify('[A-Z0-9]{16}'),
            'amount' => $amount,
            'currency' => fake()->randomElement(['USD', 'EUR', 'AED', 'SAR']),
            'status' => $status,
            'type' => fake()->randomElement(['payment', 'refund', 'chargeback']),
            'gateway_response' => $this->generateGatewayResponse($status),
            'metadata' => $this->generateMetadata(),
            'failure_reason' => $status === 'failed' ? fake()->sentence() : null,
            'gateway_fee' => $status === 'completed' ? fake()->randomFloat(2, $amount * 0.01, $amount * 0.05) : null,
            'processed_at' => in_array($status, ['completed', 'failed']) ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }

    private function generateGatewayResponse(string $status): array
    {
        $baseResponse = [
            'transaction_id' => fake()->regexify('[A-Z0-9]{16}'),
            'timestamp' => fake()->dateTimeThisMonth()->format('Y-m-d H:i:s'),
            'gateway' => fake()->randomElement(['stripe', 'paypal', 'square', 'authorize.net']),
        ];

        if ($status === 'completed') {
            $baseResponse['result'] = 'success';
            $baseResponse['approval_code'] = fake()->regexify('[A-Z0-9]{6}');
        } elseif ($status === 'failed') {
            $baseResponse['result'] = 'error';
            $baseResponse['error_code'] = fake()->randomElement(['card_declined', 'insufficient_funds', 'expired_card']);
        }

        return $baseResponse;
    }

    private function generateMetadata(): array
    {
        return [
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'device_type' => fake()->randomElement(['desktop', 'mobile', 'tablet']),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'processed_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'gateway_fee' => fake()->randomFloat(2, $attributes['amount'] * 0.01, $attributes['amount'] * 0.05),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'failure_reason' => fake()->randomElement(['Card declined', 'Insufficient funds', 'Invalid card number']),
            'processed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
