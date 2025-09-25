<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DashboardStatsWidget extends ChartWidget
{
    protected ?string $heading = 'Dashboard Stats Widget';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
