<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CategoriesChartWidget;
use App\Filament\Widgets\CommunicationChartWidget;
use App\Filament\Widgets\ContactStatusChartWidget;
use App\Filament\Widgets\ContentChartWidget;
use App\Filament\Widgets\ContentStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | array
    {
        return 12;
    }
    
    public function getWidgets(): array
    {
        return [
            ContentStatsWidget::class,
            ContentChartWidget::class,
            CategoriesChartWidget::class,
            CommunicationChartWidget::class,
            ContactStatusChartWidget::class,
        ];
    }
}
