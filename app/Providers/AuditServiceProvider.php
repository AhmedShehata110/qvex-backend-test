<?php

namespace App\Providers;

use App\Models\Communication\Review;
use App\Models\Content\FAQ;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use App\Models\Vendor\Vendor;
use App\Models\Vendor\VendorSubscription;
use App\Observers\ReviewObserver;
use App\Observers\TransactionObserver;
use App\Observers\UserObserver;
use App\Observers\VehicleObserver;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    /**
     * Model to Observer mapping
     */
    protected array $modelObservers = [
        User::class => UserObserver::class,
        Vehicle::class => VehicleObserver::class,
        Transaction::class => TransactionObserver::class,
        Review::class => ReviewObserver::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Register audit configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../config/audit.php',
            'audit'
        );

        // Register audit commands
        // Commands will be registered when they are created
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only register observers if auditing is enabled
        if (! Config::get('audit.enabled', true)) {
            return;
        }

        $this->registerModelObservers();
        $this->registerAuditableModels();
        $this->scheduleAuditTasks();
        $this->publishAuditAssets();
    }

    /**
     * Register model observers for audit logging
     */
    protected function registerModelObservers(): void
    {
        foreach ($this->modelObservers as $model => $observer) {
            // Check if the model should be audited
            if ($this->shouldAuditModel($model)) {
                $model::observe($observer);
            }
        }
    }

    /**
     * Register auditable models that use the AuditableTrait
     */
    protected function registerAuditableModels(): void
    {
        $auditableModels = [
            VendorSubscription::class,
            FAQ::class,
            Vendor::class,
            // Add other models that use AuditableTrait here
        ];

        foreach ($auditableModels as $model) {
            if ($this->shouldAuditModel($model) && $this->usesAuditableTrait($model)) {
                // The AuditableTrait will handle the boot method automatically
                // No need to manually register observers
            }
        }
    }

    /**
     * Check if a model should be audited based on configuration
     */
    protected function shouldAuditModel(string $model): bool
    {
        $modelConfig = Config::get("audit.models.{$model}", []);

        return $modelConfig['enabled'] ?? true;
    }

    /**
     * Check if a model uses the AuditableTrait
     */
    protected function usesAuditableTrait(string $model): bool
    {
        if (! class_exists($model)) {
            return false;
        }

        $traits = class_uses_recursive($model);

        return in_array('App\\Traits\\AuditableTrait', $traits) ||
               in_array('App\\Traits\\AuditableTrait', array_keys($traits));
    }

    /**
     * Schedule audit-related tasks
     */
    protected function scheduleAuditTasks(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            // Schedule audit log cleanup
            if (Config::get('audit.retention.cleanup_enabled', true)) {
                $schedule->command('audit:cleanup')
                    ->cron(Config::get('audit.retention.cleanup_schedule', '0 2 * * *'))
                    ->description('Clean up old audit logs');
            }

            // Schedule audit reports
            if (Config::get('audit.reporting.daily_summary', true)) {
                $schedule->command('audit:daily-report')
                    ->dailyAt('09:00')
                    ->description('Generate daily audit summary');
            }

            if (Config::get('audit.reporting.weekly_summary', true)) {
                $schedule->command('audit:weekly-report')
                    ->weeklyOn(1, '09:00') // Monday at 9 AM
                    ->description('Generate weekly audit summary');
            }
        });
    }

    /**
     * Publish audit configuration and assets
     */
    protected function publishAuditAssets(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Publish audit configuration
        $this->publishes([
            __DIR__.'/../../config/audit.php' => config_path('audit.php'),
        ], 'audit-config');

        // Publish audit migrations
        $this->publishes([
            __DIR__.'/../../database/migrations/2025_09_24_074707_create_audit_logs_table.php' => database_path('migrations/2025_09_24_074707_create_audit_logs_table.php'),
        ], 'audit-migrations');
    }

    /**
     * Get audit statistics for monitoring
     */
    public function getAuditStats(): array
    {
        if (! Config::get('audit.enabled', true)) {
            return ['status' => 'disabled'];
        }

        try {
            $auditLogModel = 'App\\Models\\AuditLog';

            if (! class_exists($auditLogModel)) {
                return ['status' => 'model_not_found'];
            }

            $stats = [
                'status' => 'active',
                'total_logs' => $auditLogModel::count(),
                'logs_today' => $auditLogModel::whereDate('created_at', today())->count(),
                'logs_this_week' => $auditLogModel::where('created_at', '>=', now()->startOfWeek())->count(),
                'registered_observers' => count($this->modelObservers),
                'auditable_models' => $this->getAuditableModelsList(),
            ];

            return $stats;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get list of models that are being audited
     */
    protected function getAuditableModelsList(): array
    {
        $models = [];

        foreach ($this->modelObservers as $model => $observer) {
            if ($this->shouldAuditModel($model)) {
                $models[] = class_basename($model);
            }
        }

        return $models;
    }

    /**
     * Health check for audit system
     */
    public function healthCheck(): array
    {
        $health = [
            'audit_enabled' => Config::get('audit.enabled', true),
            'observers_registered' => count($this->modelObservers),
            'database_accessible' => false,
            'recent_activity' => false,
        ];

        try {
            // Test database connectivity
            $auditLogModel = 'App\\Models\\AuditLog';
            if (class_exists($auditLogModel)) {
                $recentCount = $auditLogModel::where('created_at', '>=', now()->subHours(24))->count();
                $health['database_accessible'] = true;
                $health['recent_activity'] = $recentCount > 0;
                $health['logs_last_24h'] = $recentCount;
            }
        } catch (\Exception $e) {
            $health['database_error'] = $e->getMessage();
        }

        $health['status'] = $health['audit_enabled'] && $health['database_accessible'] ? 'healthy' : 'unhealthy';

        return $health;
    }
}
