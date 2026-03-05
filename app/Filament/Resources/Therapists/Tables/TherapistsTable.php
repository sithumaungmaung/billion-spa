<?php

namespace App\Filament\Resources\Therapists\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class TherapistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

            //    ImageColumn::make('profile_photo_path')
            //     ->label('Profile'),

                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Phone number')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->searchable(),
                TextColumn::make('therapist_code')
                    ->label('Therapist Code')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
