<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class CategoriesChartWidget extends ChartWidget
{
    protected ?string $heading = 'توزيع المقالات حسب التصنيف';
    
    public static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 6;
    
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $categories = Category::withCount('articles')
            ->get()
            ->filter(fn ($category) => $category->articles_count > 0)
            ->sortByDesc('articles_count')
            ->take(10);

        return [
            'datasets' => [
                [
                    'label' => 'عدد المقالات',
                    'data' => $categories->pluck('articles_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)',
                        'rgba(83, 102, 255, 0.7)',
                        'rgba(255, 99, 255, 0.7)',
                        'rgba(99, 255, 132, 0.7)',
                    ],
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
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
