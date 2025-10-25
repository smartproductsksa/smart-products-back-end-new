<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                                    ->disk('public')
                                    ->directory('pages/hero')
                                    ->visibility('public')
                                    ->imagePreviewHeight('250')
                                    ->downloadable(),
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
                            ->label('Image Gallery (Simple)')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Gallery Title'),
                                FileUpload::make('images')
                                    ->label('Images')
                                    ->image()
                                    ->multiple()
                                    ->disk('public')
                                    ->directory('pages/gallery')
                                    ->visibility('public')
                                    ->reorderable()
                                    ->imagePreviewHeight('150')
                                    ->panelLayout('grid')
                                    ->downloadable()
                                    ->required(),
                            ])
                            ->columns(1),
                        
                        Builder\Block::make('detailed_gallery')
                            ->label('Gallery with Details (Clients, Team, etc.)')
                            ->schema([
                                TextInput::make('section_title')
                                    ->label('Section Title')
                                    ->helperText('Optional main title for this gallery section'),
                                
                                Repeater::make('items')
                                    ->label('Gallery Items')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Item Title')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('e.g., Client Name, Team Member Name, etc.'),
                                        
                                        FileUpload::make('image')
                                            ->label('Image')
                                            ->image()
                                            ->disk('public')
                                            ->directory('pages/detailed-gallery')
                                            ->visibility('public')
                                            ->imagePreviewHeight('200')
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                '16:9',
                                                '4:3',
                                                '1:1',
                                            ])
                                            ->downloadable()
                                            ->required()
                                            ->columnSpanFull(),
                                        
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->maxLength(1000)
                                            ->helperText('Optional description or additional details')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->reorderable()
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New Item')
                                    ->addActionLabel('Add Item')
                                    ->minItems(1)
                                    ->defaultItems(1)
                                    ->columnSpanFull(),
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
                                    ->disk('public')
                                    ->directory('pages/sections')
                                    ->visibility('public')
                                    ->imagePreviewHeight('200')
                                    ->downloadable(),
                                Select::make('image_position')
                                    ->options([
                                        'left' => 'Left',
                                        'right' => 'Right',
                                    ])
                                    ->default('right'),
                            ])
                            ->columns(1),
                        
                        Builder\Block::make('faq')
                            ->label('FAQ Section')
                            ->schema([
                                TextInput::make('section_title')
                                    ->label('Section Title')
                                    ->default('Frequently Asked Questions')
                                    ->helperText('Main title for the FAQ section'),
                                
                                Textarea::make('section_description')
                                    ->label('Section Description')
                                    ->rows(2)
                                    ->helperText('Optional introduction text for the FAQ section')
                                    ->columnSpanFull(),
                                
                                Repeater::make('items')
                                    ->label('FAQ Items')
                                    ->schema([
                                        TextInput::make('question')
                                            ->label('Question')
                                            ->required()
                                            ->maxLength(500)
                                            ->helperText('The question users are asking')
                                            ->columnSpanFull(),
                                        
                                        RichEditor::make('answer')
                                            ->label('Answer')
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                            ])
                                            ->helperText('Detailed answer to the question')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1)
                                    ->reorderable()
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'New FAQ Item')
                                    ->addActionLabel('Add FAQ Item')
                                    ->minItems(1)
                                    ->defaultItems(1)
                                    ->columnSpanFull(),
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
