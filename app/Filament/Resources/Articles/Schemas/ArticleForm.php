<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, $state, Set $set) {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Select::make('category')
                        ->options([
                            'technology' => 'Technology',
                            'design' => 'Design',
                            'business' => 'Business',
                            'marketing' => 'Marketing',
                            'other' => 'Other',
                        ])
                        ->required(),

                    TagsInput::make('tags')
                        ->separator(',')
                        ->suggestions([
                            'laravel', 'filament', 'php', 'javascript', 'css', 'html',
                            'vue', 'react', 'angular', 'tailwind', 'alpine', 'livewire'
                        ]),

                    RichEditor::make('content')
                        ->required()
                        ->fileAttachmentsDisk('s3')
                        ->fileAttachmentsDirectory('article-attachments')
                        ->columnSpanFull(),

                    FileUpload::make('image')
                        ->image()
                        ->disk('s3')
                        ->directory('articles')
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->maxSize(2048),
                ])
                ->columns(1)
        ]);
    }
}
