<?php

namespace Database\Factories;

use App\Enums\User\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'email_verified_at' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
            'phone_verified_at' => fake()->optional(0.7)->dateTimeBetween('-1 year', 'now'),
            'password' => static::$password ??= Hash::make('password'),
            'locale' => fake()->randomElement(['en', 'ar']),
            'timezone' => fake()->timezone(),
            'avatar' => fake()->optional(0.3)->imageUrl(300, 300, 'people'),
            'birth_date' => fake()->optional(0.6)->date('Y-m-d', '-18 years'),
            'gender' => fake()->randomElement(['male', 'female']),
            'user_type' => fake()->randomElement(UserTypeEnum::cases()),
            'two_factor_enabled' => fake()->boolean(20), // 20% chance
            'last_login_ip' => fake()->optional(0.9)->ipv4(),
            'two_factor_enabled' => fake()->boolean(20), // 20% chance
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'is_active' => fake()->boolean(95), // 95% chance of being active
            'added_by_id' => null,
            'deleted_at' => null,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (User $user) {
            // Set two factor recovery codes if two factor is enabled
            if ($user->two_factor_enabled) {
                $user->two_factor_secret = Str::random(32); // Simple random string instead of encrypted
                $user->two_factor_recovery_codes = json_encode([
                    Str::random(8).'-'.Str::random(8),
                    Str::random(8).'-'.Str::random(8),
                    Str::random(8).'-'.Str::random(8),
                ]);
            }
        });
    }

    /**
     * Indicate that the user's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'phone_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the user should be an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => UserTypeEnum::ADMIN,
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'locale' => 'en',
        ]);
    }

    /**
     * Indicate that the user should be a regular user.
     */
    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => UserTypeEnum::USER,
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the user should have Arabic locale.
     */
    public function arabic(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'ar',
            'timezone' => 'Asia/Dubai',
        ]);
    }

    /**
     * Indicate that the user should have two-factor authentication enabled.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_enabled' => true,
        ]);
    }

    /**
     * Indicate that the user should have a recent login.
     */
    public function recentlyActive(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_login_at' => fake()->dateTimeBetween('-1 day', 'now'),
            'last_login_ip' => fake()->ipv4(),
        ]);
    }

    /**
     * Indicate that the user should be a vendor (user who can create vendor business).
     */
    public function vendor(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => UserTypeEnum::USER,
            'is_active' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
    }
}
