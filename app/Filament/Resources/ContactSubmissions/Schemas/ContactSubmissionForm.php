<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->disabled()
                    ->columnSpan(1),

                TextInput::make('phone')
                    ->label('الهاتف')
                    ->required()
                    ->disabled()
                    ->columnSpan(1),

                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required()
                    ->disabled()
                    ->columnSpan(2),

                Textarea::make('message')
                    ->label('الرسالة')
                    ->required()
                    ->disabled()
                    ->rows(5)
                    ->columnSpan(2),

                Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'new' => 'جديد',
                        'in_progress' => 'قيد المعالجة',
                        'resolved' => 'تم التواصل',
                    ])
                    ->required()
                    ->columnSpan(2),

                Textarea::make('admin_notes')
                    ->label('ملاحظات الإدارة')
                    ->rows(4)
                    ->columnSpan(2),
            ]);
    }
}
