<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactSubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('الاسم'),
                TextEntry::make('phone')
                    ->label('الهاتف'),
                TextEntry::make('email')
                    ->label('البريد الإلكتروني'),
                TextEntry::make('message')
                    ->label('الرسالة')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'جديد',
                        'in_progress' => 'قيد المعالجة',
                        'resolved' => 'تم التواصل',
                        default => $state,
                    }),
                TextEntry::make('admin_notes')
                    ->label('ملاحظات الإدارة')
                    ->placeholder('لا توجد ملاحظات')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime(),
            ]);
    }
}
