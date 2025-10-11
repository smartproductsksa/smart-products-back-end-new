<?php

namespace App\Filament\Resources\News\Schemas;

use App\Models\News;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class NewsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->label('العنوان'),
                TextEntry::make('slug')
                    ->label('الرابط'),
                TextEntry::make('tags')
                    ->label('الوسوم')
                    ->badge()
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('content')
                    ->label('المحتوى')
                    ->html()
                    ->columnSpanFull(),
                ImageEntry::make('image')
                    ->label('الصورة')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->label('تاريخ الحذف')
                    ->dateTime()
                    ->visible(fn (News $record): bool => $record->trashed()),
            ]);
    }
}
