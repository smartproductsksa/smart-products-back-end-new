<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\ContactSubmission;
use App\Models\MailingList;
use App\Models\News;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentStatsWidget extends BaseWidget
{
    public static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $newContactsCount = ContactSubmission::where('status', 'new')->count();
        
        return [
            Stat::make('إجمالي المقالات', Article::count())
                ->description('جميع المقالات المنشورة')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
            
            Stat::make('إجمالي الأخبار', News::count())
                ->description('جميع الأخبار')
                ->descriptionIcon('heroicon-o-newspaper')
                ->color('info')
                ->chart([3, 5, 4, 7, 8, 5, 6]),
            
            Stat::make('رسائل اتصل بنا', ContactSubmission::count())
                ->description($newContactsCount > 0 ? "{$newContactsCount} رسالة جديدة" : 'جميع الرسائل')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color($newContactsCount > 0 ? 'danger' : 'success')
                ->chart([2, 5, 3, 8, 4, 6, 7]),
            
            Stat::make('المشتركين بالقائمة البريدية', MailingList::count())
                ->description('إجمالي المشتركين')
                ->descriptionIcon('heroicon-o-envelope')
                ->color('warning')
                ->chart([1, 2, 3, 5, 8, 12, 15]),
        ];
    }
}
