<?php

namespace App\Filament\Resources\News;

use App\Filament\Resources\News\Pages\CreateNews;
use App\Filament\Resources\News\Pages\EditNews;
use App\Filament\Resources\News\Pages\ListNews;
use App\Filament\Resources\News\Pages\ViewNews;
use App\Filament\Resources\News\Schemas\NewsForm;
use App\Filament\Resources\News\Schemas\NewsInfolist;
use App\Filament\Resources\News\Tables\NewsTable;
use App\Models\News;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $recordTitleAttribute = 'title';
    
    protected static ?string $modelLabel = 'خبر';
    
    protected static ?string $pluralModelLabel = 'الأخبار';
    
    protected static ?string $navigationLabel = 'الأخبار';

    public static function form(Schema $schema): Schema
    {
        return NewsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return NewsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'إدارة المحتوى';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNews::route('/'),
            'create' => CreateNews::route('/create'),
            'view' => ViewNews::route('/{record}'),
            'edit' => EditNews::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
