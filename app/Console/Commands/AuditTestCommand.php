<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Observers\ReviewObserver;
use App\Observers\TransactionObserver;
use App\Observers\UserObserver;
use App\Observers\VehicleObserver;
use App\Traits\AuditableTrait;
use App\Traits\BaseAuditObserver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class AuditTestCommand extends Command
{
    protected $signature = 'audit:test';

    protected $description = 'Test audit system components';

    public function handle()
    {
        $this->info('Testing Audit System Components...');
        $this->newLine();

        $this->testAuditConfiguration();
        $this->testAuditModels();
        $this->testObservers();
        $this->testTraits();

        $this->newLine();
        $this->info('✅ Audit system test completed successfully!');

        return Command::SUCCESS;
    }

    protected function testAuditConfiguration()
    {
        $this->info('🔧 Testing Audit Configuration:');

        // Test config loading
        $enabled = Config::get('audit.enabled', false);
        $this->line('   - Audit enabled: '.($enabled ? '✅ Yes' : '❌ No'));

        $events = Config::get('audit.events', []);
        $this->line('   - Configured events: '.implode(', ', $events));

        $excludeFields = Config::get('audit.exclude_fields', []);
        $this->line('   - Excluded fields: '.implode(', ', $excludeFields));

        $models = Config::get('audit.models', []);
        $this->line('   - Configured models: '.count($models));

        $this->newLine();
    }

    protected function testAuditModels()
    {
        $this->info('📊 Testing Audit Models:');

        // Test AuditLog model
        if (class_exists(AuditLog::class)) {
            $this->line('   - AuditLog model: ✅ Exists');

            // Test model constants
            $constants = [
                'EVENT_CREATED' => AuditLog::EVENT_CREATED,
                'EVENT_UPDATED' => AuditLog::EVENT_UPDATED,
                'EVENT_DELETED' => AuditLog::EVENT_DELETED,
                'EVENT_RESTORED' => AuditLog::EVENT_RESTORED,
            ];

            foreach ($constants as $name => $value) {
                $this->line("     - Constant {$name}: {$value}");
            }

            // Test key methods exist
            $methods = [
                'createEntry',
                'forModel',
                'getChanges',
                'getStatsForModel',
            ];

            foreach ($methods as $method) {
                $exists = method_exists(AuditLog::class, $method);
                $this->line("     - Method {$method}: ".($exists ? '✅' : '❌'));
            }
        } else {
            $this->line('   - AuditLog model: ❌ Missing');
        }

        $this->newLine();
    }

    protected function testObservers()
    {
        $this->info('👀 Testing Observers:');

        $observers = [
            'UserObserver' => UserObserver::class,
            'VehicleObserver' => VehicleObserver::class,
            'TransactionObserver' => TransactionObserver::class,
            'ReviewObserver' => ReviewObserver::class,
        ];

        foreach ($observers as $name => $class) {
            if (class_exists($class)) {
                $this->line("   - {$name}: ✅ Exists");

                // Test observer uses trait
                $traits = class_uses($class);
                $usesBaseTrait = in_array(BaseAuditObserver::class, $traits);
                $this->line('     - Uses BaseAuditObserver: '.($usesBaseTrait ? '✅' : '❌'));

                // Test key methods exist
                $methods = ['created', 'updated', 'deleted'];
                foreach ($methods as $method) {
                    $exists = method_exists($class, $method);
                    $this->line("     - Method {$method}: ".($exists ? '✅' : '❌'));
                }
            } else {
                $this->line("   - {$name}: ❌ Missing");
            }
        }

        $this->newLine();
    }

    protected function testTraits()
    {
        $this->info('🔄 Testing Traits:');

        $traits = [
            'AuditableTrait' => AuditableTrait::class,
            'BaseAuditObserver' => BaseAuditObserver::class,
        ];

        foreach ($traits as $name => $class) {
            if (trait_exists($class)) {
                $this->line("   - {$name}: ✅ Exists");

                // Test key methods exist
                if ($name === 'AuditableTrait') {
                    $methods = [
                        'audit',
                        'auditLogs',
                        'getAuditStats',
                        'auditCustomEvent',
                    ];
                } else {
                    $methods = [
                        'logAuditEvent',
                        'getOldValues',
                        'getNewValues',
                        'filterAuditData',
                    ];
                }

                foreach ($methods as $method) {
                    $reflection = new \ReflectionClass($class);
                    $exists = $reflection->hasMethod($method);
                    $this->line("     - Method {$method}: ".($exists ? '✅' : '❌'));
                }
            } else {
                $this->line("   - {$name}: ❌ Missing");
            }
        }

        $this->newLine();
    }
}
