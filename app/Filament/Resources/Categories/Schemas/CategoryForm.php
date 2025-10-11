<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, $set) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    })
                    ->columnSpan(2),
                
                TextInput::make('slug')
                    ->label('الرابط')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->columnSpan(2),
                
                Textarea::make('description')
                    ->label('الوصف')
                    ->rows(3)
                    ->columnSpan(2),
            ]);
    }
}
