<?php

namespace App\Filament\Resources\MailingList\Pages;

use App\Filament\Resources\MailingList\MailingListResource;
use App\Models\MailingList;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListMailingList extends ListRecords
{
    protected static string $resource = MailingListResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('تصدير Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $subscribers = MailingList::all();
                    
                    return response()->streamDownload(function () use ($subscribers) {
                        $file = fopen('php://output', 'w');
                        
                        // Add BOM for Excel UTF-8 support
                        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                        
                        // Add header
                        fputcsv($file, ['ID', 'Email', 'Subscribed At']);
                        
                        // Add data
                        foreach ($subscribers as $subscriber) {
                            fputcsv($file, [
                                $subscriber->id,
                                $subscriber->email,
                                $subscriber->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                        
                        fclose($file);
                    }, 'mailing-list-' . date('Y-m-d-His') . '.csv');
                }),
        ];
    }
}
