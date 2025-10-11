<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use App\Models\MailingList;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CommunicationChartWidget extends ChartWidget
{
    protected ?string $heading = 'التواصل خلال الأشهر الماضية';
    
    public static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 6;
    
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $contactData = Trend::model(ContactSubmission::class)
            ->between(
                start: now()->subMonths(6),
                end: now(),
            )
            ->perMonth()
            ->count();

        $mailingData = Trend::model(MailingList::class)
            ->between(
                start: now()->subMonths(6),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'رسائل اتصل بنا',
                    'data' => $contactData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                ],
                [
                    'label' => 'اشتراكات القائمة البريدية',
                    'data' => $mailingData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(251, 191, 36, 0.5)',
                    'borderColor' => 'rgb(251, 191, 36)',
                ],
            ],
            'labels' => $contactData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
