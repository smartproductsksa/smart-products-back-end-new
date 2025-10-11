<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\News;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ContentChartWidget extends ChartWidget
{
    protected ?string $heading = 'المحتوى خلال الأشهر الماضية';
    
    public static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 6;
    
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $articlesData = Trend::model(Article::class)
            ->between(
                start: now()->subMonths(6),
                end: now(),
            )
            ->perMonth()
            ->count();

        $newsData = Trend::model(News::class)
            ->between(
                start: now()->subMonths(6),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'المقالات',
                    'data' => $articlesData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'الأخبار',
                    'data' => $newsData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
                ],
            ],
            'labels' => $articlesData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
