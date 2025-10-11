<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                
                TextInput::make('slug')
                    ->label('الرابط')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                    ])
                    ->default('draft')
                    ->required(),
                
                TextInput::make('order')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0)
                    ->required(),
                
                Builder::make('content')
                    ->label('أقسام الصفحة')
                    ->blocks([
                        Builder\Block::make('hero')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Section Title')
                                    ->required(),
                                RichEditor::make('text')
                                    ->label('Section Text')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'link',
                                    ]),
                                FileUpload::make('image')
                                    ->label('Hero Image')
                                    ->image()
                                    ->disk('s3')
                                    ->directory('pages/hero'),
                            ])
                            ->columns(1),
                        
                        Builder\Block::make('text_section')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Section Title'),
                                RichEditor::make('text')
                                    ->label('Section Text')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                    ]),
                            ])
                            ->columns(1),
                        
                        Builder\Block::make('image_gallery')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Gallery Title'),
                                FileUpload::make('images')
                                    ->label('Images')
                                    ->image()
                                    ->multiple()
                                    ->disk('s3')
                                    ->directory('pages/gallery')
                                    ->reorderable()
                                    ->required(),
                            ])
                            ->columns(1),
                        
                        Builder\Block::make('text_with_image')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Section Title'),
                                RichEditor::make('text')
                                    ->label('Section Text')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                    ]),
                                FileUpload::make('image')
                                    ->label('Section Image')
                                    ->image()
                                    ->disk('s3')
                                    ->directory('pages/sections'),
                                Select::make('image_position')
                                    ->options([
                                        'left' => 'Left',
                                        'right' => 'Right',
                                    ])
                                    ->default('right'),
                            ])
                            ->columns(1),
                        
                        Builder\Block::make('model_list')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Section Title'),
                                Select::make('model')
                                    ->label('Select Model')
                                    ->options([
                                        'articles' => 'Articles',
                                        'news' => 'News',
                                        'categories' => 'Categories',
                                    ])
                                    ->required(),
                                TextInput::make('limit')
                                    ->label('Number of Items')
                                    ->numeric()
                                    ->default(4)
                                    ->minValue(1)
                                    ->maxValue(50)
                                    ->required(),
                                Select::make('order_by')
                                    ->label('Order By')
                                    ->options([
                                        'created_at_desc' => 'Newest First',
                                        'created_at_asc' => 'Oldest First',
                                        'title_asc' => 'Title A-Z',
                                        'title_desc' => 'Title Z-A',
                                    ])
                                    ->default('created_at_desc'),
                            ])
                            ->columns(2),
                    ])
                    ->collapsible()
                    ->cloneable()
                    ->reorderable()
                    ->columnSpanFull(),
            ]);
    }
}
