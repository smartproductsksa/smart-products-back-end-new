<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use Filament\Widgets\ChartWidget;

class ContactStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'حالة رسائل اتصل بنا';
    
    public static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 6;
    
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $newCount = ContactSubmission::where('status', 'new')->count();
        $inProgressCount = ContactSubmission::where('status', 'in_progress')->count();
        $resolvedCount = ContactSubmission::where('status', 'resolved')->count();

        return [
            'datasets' => [
                [
                    'label' => 'الرسائل',
                    'data' => [$newCount, $inProgressCount, $resolvedCount],
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.7)',    // Red for new
                        'rgba(251, 191, 36, 0.7)',   // Yellow for in progress
                        'rgba(34, 197, 94, 0.7)',    // Green for resolved
                    ],
                ],
            ],
            'labels' => ['جديد', 'قيد المعالجة', 'تم التواصل'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => true,
            'aspectRatio' => 2,
        ];
    }
}
