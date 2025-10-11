<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label('العنوان')
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

                TagsInput::make('tags')
                    ->label('الوسوم')
                    ->separator(',')
                    ->suggestions([
                        'breaking', 'trending', 'featured', 'local', 'international',
                        'politics', 'sports', 'entertainment', 'technology', 'health'
                    ])
                    ->columnSpan(2),

                RichEditor::make('content')
                    ->label('المحتوى')
                    ->required()
                    ->fileAttachmentsDisk('s3')
                    ->fileAttachmentsDirectory('news-attachments')
                    ->columnSpan(2),

                FileUpload::make('image')
                    ->label('الصورة')
                    ->image()
                    ->disk('s3')
                    ->directory('news')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->maxSize(2048)
                    ->columnSpan(2),
            ]);
    }
}
