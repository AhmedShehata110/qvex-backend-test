<?php

namespace Database\Factories;

use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction\Transaction>
 */
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
        $type = fake()->randomElement(['sale', 'rental', 'lease']);
        $subtotal = fake()->randomFloat(2, 5000, 100000);
        $taxAmount = $subtotal * fake()->randomFloat(2, 0.05, 0.15); // 5-15% tax
        $commissionAmount = $subtotal * fake()->randomFloat(2, 0.03, 0.08); // 3-8% commission
        $totalAmount = $subtotal + $taxAmount + $commissionAmount;

        $status = fake()->randomElement([
            'pending', 'confirmed', 'payment_pending', 'paid',
            'in_progress', 'completed', 'cancelled', 'refunded', 'disputed',
        ]);

        $paidAmount = $this->calculatePaidAmount($status, $totalAmount);
        $refundedAmount = $status === 'refunded' ? fake()->randomFloat(2, 0, $paidAmount) : 0;

        return [
            'transaction_number' => $this->generateTransactionNumber(),
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'type' => $type,
            'status' => $status,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'commission_amount' => $commissionAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'refunded_amount' => $refundedAmount,
            'currency' => fake()->randomElement(['USD', 'EUR', 'AED', 'SAR']),
            'transaction_data' => $this->generateTransactionData($type),
            'notes' => fake()->optional(0.4)->paragraph(2),
            'cancellation_reason' => $status === 'cancelled' ? fake()->sentence(8) : null,
            'confirmed_at' => in_array($status, ['confirmed', 'paid', 'completed']) ?
                fake()->dateTimeBetween('-6 months', 'now') : null,
            'completed_at' => $status === 'completed' ?
                fake()->dateTimeBetween('-3 months', 'now') : null,
            'cancelled_at' => $status === 'cancelled' ?
                fake()->dateTimeBetween('-2 months', 'now') : null,
            'is_active' => fake()->boolean(95),
            'added_by_id' => null,
            'deleted_at' => null,
        ];
    }

    /**
     * Generate a unique transaction number.
     */
    private function generateTransactionNumber(): string
    {
        return 'TXN-'.date('Y').'-'.fake()->unique()->numerify('######');
    }

    /**
     * Calculate paid amount based on status.
     */
    private function calculatePaidAmount(string $status, float $totalAmount): float
    {
        switch ($status) {
            case 'paid':
            case 'completed':
            case 'refunded':
                return $totalAmount;
            case 'payment_pending':
            case 'in_progress':
                return fake()->randomFloat(2, $totalAmount * 0.1, $totalAmount * 0.5); // Partial payment
            case 'pending':
            case 'confirmed':
            case 'cancelled':
            case 'disputed':
            default:
                return 0.00;
        }
    }

    /**
     * Generate transaction data JSON based on type.
     */
    private function generateTransactionData(string $type): array
    {
        $data = [
            'payment_method' => fake()->randomElement(['credit_card', 'bank_transfer', 'cash', 'financing']),
            'insurance_required' => fake()->boolean(70),
        ];

        switch ($type) {
            case 'rental':
                $data = array_merge($data, [
                    'rental_period' => [
                        'start_date' => fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
                        'end_date' => fake()->dateTimeBetween('+1 month', '+3 months')->format('Y-m-d'),
                        'duration_days' => fake()->numberBetween(1, 30),
                    ],
                    'pickup_location' => fake()->address(),
                    'return_location' => fake()->address(),
                    'driver_license_verified' => fake()->boolean(90),
                    'security_deposit_held' => fake()->randomFloat(2, 500, 5000),
                ]);
                break;

            case 'lease':
                $data = array_merge($data, [
                    'lease_term_months' => fake()->numberBetween(24, 48),
                    'monthly_payment' => fake()->randomFloat(2, 200, 1500),
                    'down_payment' => fake()->randomFloat(2, 1000, 10000),
                    'residual_value' => fake()->randomFloat(2, 10000, 50000),
                    'mileage_limit_per_year' => fake()->numberBetween(10000, 25000),
                ]);
                break;

            case 'sale':
            default:
                $data = array_merge($data, [
                    'financing_approved' => fake()->boolean(60),
                    'trade_in_value' => fake()->optional(0.3)->randomFloat(2, 2000, 30000),
                    'title_transfer_required' => fake()->boolean(90),
                    'inspection_completed' => fake()->boolean(85),
                    'warranty_transferred' => fake()->boolean(70),
                ]);
                break;
        }

        return $data;
    }

    /**
     * Indicate that the transaction should be completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'paid_amount' => $attributes['total_amount'],
            'confirmed_at' => fake()->dateTimeBetween('-3 months', '-1 week'),
            'completed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Indicate that the transaction should be pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_amount' => 0.00,
            'confirmed_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
        ]);
    }

    /**
     * Indicate that the transaction should be paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_amount' => $attributes['total_amount'],
            'confirmed_at' => fake()->dateTimeBetween('-2 months', '-1 week'),
        ]);
    }

    /**
     * Indicate that the transaction should be cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'paid_amount' => 0.00,
            'cancellation_reason' => fake()->randomElement([
                'Buyer changed mind',
                'Vehicle no longer available',
                'Financing fell through',
                'Inspection failed',
                'Price negotiation failed',
                'Better offer found elsewhere',
            ]),
            'cancelled_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the transaction should be for a sale.
     */
    public function sale(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sale',
        ]);
    }

    /**
     * Indicate that the transaction should be for a rental.
     */
    public function rental(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'rental',
            'subtotal' => fake()->randomFloat(2, 200, 5000), // Lower amounts for rentals
        ]);
    }

    /**
     * Indicate that the transaction should be for a lease.
     */
    public function lease(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'lease',
        ]);
    }

    /**
     * Indicate that the transaction should be high-value.
     */
    public function highValue(): static
    {
        return $this->state(function (array $attributes) {
            $subtotal = fake()->randomFloat(2, 50000, 300000);
            $taxAmount = $subtotal * fake()->randomFloat(2, 0.05, 0.15);
            $commissionAmount = $subtotal * fake()->randomFloat(2, 0.03, 0.08);
            $totalAmount = $subtotal + $taxAmount + $commissionAmount;

            return [
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $this->calculatePaidAmount($attributes['status'] ?? 'pending', $totalAmount),
            ];
        });
    }

    /**
     * Indicate that the transaction should be recent.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'confirmed_at' => fake()->optional(0.7)->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the transaction should be disputed.
     */
    public function disputed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'disputed',
            'notes' => 'Customer disputes charges. Investigation in progress.',
        ]);
    }
}
