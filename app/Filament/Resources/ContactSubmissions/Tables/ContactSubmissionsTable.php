<?php

namespace App\Filament\Resources\ContactSubmissions\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('message')
                    ->label('الرسالة')
                    ->limit(50)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('status')
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
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'new' => 'جديد',
                        'in_progress' => 'قيد المعالجة',
                        'resolved' => 'تم التواصل',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
