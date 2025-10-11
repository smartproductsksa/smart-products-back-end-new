<?php

namespace App\Filament\Resources\MailingList;

use App\Filament\Resources\MailingList\Pages\ListMailingList;
use App\Filament\Resources\MailingList\Tables\MailingListTable;
use App\Models\MailingList;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class MailingListResource extends Resource
{
    protected static ?string $model = MailingList::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $recordTitleAttribute = 'email';
    
    protected static ?string $modelLabel = 'مشترك';
    
    protected static ?string $pluralModelLabel = 'القائمة البريدية';
    
    protected static ?string $navigationLabel = 'القائمة البريدية';

    public static function table(Table $table): Table
    {
        return MailingListTable::configure($table);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'إدارة التواصل';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }


    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailingList::route('/'),
        ];
    }
}
