<?php

namespace Database\Seeders;

use App\Models\Communication\Message;
use App\Models\Communication\Review;
use App\Models\Content\FAQ;
use App\Models\Marketing\Banner;
use App\Models\Transaction\Payment;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleFeature;
use App\Models\Vendor\SubscriptionPlan;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorSubscription;
use Illuminate\Database\Seeder;

class AdditionalModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedVehicleFeatures();
        $this->seedFAQs();
        $this->seedVendorSubscriptions();
        $this->seedBanners();
        $this->seedMessages();
        $this->seedPayments();
        $this->seedReviews();
    }

    /**
     * Seed FAQs.
     */
    private function seedFAQs(): void
    {
        // Get admin user for created_by
        $adminUser = User::where('email', 'admin@qvex.com')->first();

        // Popular buying FAQs
        FAQ::factory()
            ->count(5)
            ->buying()
            ->active()
            ->popular()
            ->create(['added_by_id' => $adminUser?->id]);

        // Popular selling FAQs
        FAQ::factory()
            ->count(4)
            ->selling()
            ->active()
            ->popular()
            ->create(['added_by_id' => $adminUser?->id]);

        // Account management FAQs
        FAQ::factory()
            ->count(6)
            ->account()
            ->active()
            ->create(['added_by_id' => $adminUser?->id]);

        // General FAQs
        FAQ::factory()
            ->count(8)
            ->active()
            ->create(['added_by_id' => $adminUser?->id]);

        $this->command->info('Created FAQs successfully!');
    }

    /**
     * Seed vendor subscriptions.
     */
    private function seedVendorSubscriptions(): void
    {
        $vendors = Vendor::where('status', 'active')->get();
        $subscriptionPlans = SubscriptionPlan::where('is_active', true)->get();

        if ($vendors->isEmpty() || $subscriptionPlans->isEmpty()) {
            $this->command->warn('No vendors or subscription plans found. Skipping vendor subscriptions.');

            return;
        }

        // Active subscriptions for most vendors (80%)
        $activeVendorsCount = (int) ($vendors->count() * 0.8);
        $activeVendors = $vendors->take($activeVendorsCount);

        foreach ($activeVendors as $vendor) {
            $plan = $subscriptionPlans->random();

            VendorSubscription::factory()
                ->active()
                ->autoRenewal()
                ->create([
                    'vendor_id' => $vendor->id,
                    'subscription_plan_id' => $plan->id,
                    'amount_paid' => $plan->price,
                    'currency' => 'USD',
                ]);
        }

        // Some vendors with premium subscriptions
        $premiumCount = min(5, $vendors->count());
        $premiumPlans = $subscriptionPlans->where('name', 'Premium')->first()
                       ?? $subscriptionPlans->sortByDesc('price')->first();

        if ($premiumPlans) {
            VendorSubscription::factory()
                ->count($premiumCount)
                ->active()
                ->premiumPlan()
                ->heavilyUsed()
                ->create([
                    'vendor_id' => fn () => $vendors->random()->id,
                    'subscription_plan_id' => $premiumPlans->id,
                    'amount_paid' => $premiumPlans->price,
                ]);
        }

        // Some expired subscriptions
        VendorSubscription::factory()
            ->count(8)
            ->expired()
            ->create([
                'vendor_id' => fn () => $vendors->random()->id,
                'subscription_plan_id' => fn () => $subscriptionPlans->random()->id,
            ]);

        // Some cancelled subscriptions
        VendorSubscription::factory()
            ->count(5)
            ->cancelled()
            ->create([
                'vendor_id' => fn () => $vendors->random()->id,
                'subscription_plan_id' => fn () => $subscriptionPlans->random()->id,
            ]);

        $this->command->info('Created vendor subscriptions successfully!');
    }

    /**
     * Seed reviews.
     */
    private function seedReviews(): void
    {
        $customers = User::whereDoesntHave('vendor')->limit(30)->get();
        $vehicles = Vehicle::where('status', 'active')->limit(50)->get();
        $vendors = Vendor::where('status', 'active')->limit(20)->get();
        $transactions = Transaction::whereIn('status', ['completed', 'paid'])->limit(30)->get();

        if ($customers->isEmpty() || $vehicles->isEmpty() || $vendors->isEmpty()) {
            $this->command->warn('Insufficient data for creating reviews.');

            return;
        }

        // Positive vehicle reviews (40)
        Review::factory()
            ->count(40)
            ->forVehicle()
            ->positive()
            ->approved()
            ->verified()
            ->create($this->getReviewAttributes($customers, $vehicles, $transactions, Vehicle::class));

        // Mixed vehicle reviews (20)
        Review::factory()
            ->count(20)
            ->forVehicle()
            ->approved()
            ->create($this->getReviewAttributes($customers, $vehicles, $transactions, Vehicle::class));

        // Positive vendor reviews (30)
        Review::factory()
            ->count(30)
            ->forVendor()
            ->positive()
            ->approved()
            ->verified()
            ->create($this->getReviewAttributes($customers, $vendors, $transactions, Vendor::class));

        // Mixed vendor reviews (15)
        Review::factory()
            ->count(15)
            ->forVendor()
            ->approved()
            ->create($this->getReviewAttributes($customers, $vendors, $transactions, Vendor::class));

        // Some negative reviews (8)
        Review::factory()
            ->count(4)
            ->forVehicle()
            ->negative()
            ->approved()
            ->create($this->getReviewAttributes($customers, $vehicles, $transactions, Vehicle::class));

        Review::factory()
            ->count(4)
            ->forVendor()
            ->negative()
            ->approved()
            ->create($this->getReviewAttributes($customers, $vendors, $transactions, Vendor::class));

        // Pending reviews (6)
        Review::factory()
            ->count(6)
            ->state(['status' => 'pending', 'approved_at' => null])
            ->create($this->getReviewAttributes($customers, $vehicles, $transactions));

        $this->command->info('Created reviews successfully!');
    }

    /**
     * Get review attributes with realistic relationships.
     */
    private function getReviewAttributes($customers, $reviewables, $transactions, $reviewableType = null): array
    {
        $attributes = [];

        if ($customers->isNotEmpty()) {
            $attributes['reviewer_id'] = $customers->random()->id;
        }

        if ($reviewables->isNotEmpty()) {
            $reviewable = $reviewables->random();
            $attributes['reviewable_id'] = $reviewable->id;

            if ($reviewableType) {
                $attributes['reviewable_type'] = $reviewableType;
            }
        }

        if ($transactions->isNotEmpty()) {
            $attributes['transaction_id'] = $transactions->random()->id;
        }

        return $attributes;
    }

    /**
     * Seed vehicle features.
     */
    private function seedVehicleFeatures(): void
    {
        $adminUser = User::where('email', 'admin@qvex.com')->first();

        // Safety features
        VehicleFeature::factory()
            ->count(10)
            ->safety()
            ->active()
            ->create(['added_by_id' => $adminUser?->id]);

        // Comfort features
        VehicleFeature::factory()
            ->count(12)
            ->comfort()
            ->active()
            ->create(['added_by_id' => $adminUser?->id]);

        // Technology features
        VehicleFeature::factory()
            ->count(8)
            ->active()
            ->state(['category' => 'technology'])
            ->create(['added_by_id' => $adminUser?->id]);

        // Performance features
        VehicleFeature::factory()
            ->count(6)
            ->active()
            ->state(['category' => 'performance'])
            ->create(['added_by_id' => $adminUser?->id]);

        $this->command->info('Created vehicle features successfully!');
    }

    /**
     * Seed banners.
     */
    private function seedBanners(): void
    {
        $adminUser = User::where('email', 'admin@qvex.com')->first();

        // Header banners
        Banner::factory()
            ->count(3)
            ->header()
            ->active()
            ->create(['added_by_id' => $adminUser?->id]);

        // Sidebar banners
        Banner::factory()
            ->count(4)
            ->active()
            ->state(['position' => 'sidebar'])
            ->create(['added_by_id' => $adminUser?->id]);

        // Content banners
        Banner::factory()
            ->count(2)
            ->active()
            ->state(['position' => 'content'])
            ->create(['added_by_id' => $adminUser?->id]);

        $this->command->info('Created banners successfully!');
    }

    /**
     * Seed messages.
     */
    private function seedMessages(): void
    {
        $customers = User::whereDoesntHave('vendor')->limit(20)->get();
        $vendors = Vendor::with('user')->where('status', 'active')->limit(10)->get();
        $vehicles = Vehicle::where('status', 'active')->limit(30)->get();

        if ($customers->isEmpty() || $vendors->isEmpty() || $vehicles->isEmpty()) {
            $this->command->warn('Insufficient data for creating messages.');

            return;
        }

        // Customer inquiries
        Message::factory()
            ->count(50)
            ->inquiry()
            ->create($this->getMessageAttributes($customers, $vendors, $vehicles));

        // System messages
        Message::factory()
            ->count(15)
            ->systemMessage()
            ->create();

        // General messages (some read, some unread)
        Message::factory()
            ->count(30)
            ->read()
            ->create($this->getMessageAttributes($customers, $vendors, $vehicles));

        Message::factory()
            ->count(20)
            ->unread()
            ->create($this->getMessageAttributes($customers, $vendors, $vehicles));

        $this->command->info('Created messages successfully!');
    }

    /**
     * Seed payments.
     */
    private function seedPayments(): void
    {
        $completedTransactions = Transaction::where('status', 'completed')->limit(40)->get();
        $paidTransactions = Transaction::where('status', 'paid')->limit(20)->get();

        if ($completedTransactions->isEmpty() && $paidTransactions->isEmpty()) {
            $this->command->warn('No transactions found for creating payments.');

            return;
        }

        // Completed payments
        foreach ($completedTransactions as $transaction) {
            Payment::factory()
                ->completed()
                ->create([
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->total_amount,
                    'currency' => $transaction->currency,
                ]);
        }

        // Paid transaction payments
        foreach ($paidTransactions as $transaction) {
            Payment::factory()
                ->completed()
                ->create([
                    'transaction_id' => $transaction->id,
                    'amount' => $transaction->paid_amount,
                    'currency' => $transaction->currency,
                ]);
        }

        // Some failed payments
        Payment::factory()
            ->count(8)
            ->failed()
            ->create();

        $this->command->info('Created payments successfully!');
    }

    /**
     * Get message attributes with realistic relationships.
     */
    private function getMessageAttributes($customers, $vendors, $vehicles): array
    {
        $attributes = [];

        if ($customers->isNotEmpty()) {
            $attributes['sender_id'] = $customers->random()->id;
        }

        if ($vendors->isNotEmpty()) {
            $vendor = $vendors->random();
            $attributes['receiver_id'] = $vendor->user_id;
        }

        if ($vehicles->isNotEmpty()) {
            $attributes['vehicle_id'] = $vehicles->random()->id;
        }

        return $attributes;
    }
}
