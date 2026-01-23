<?php

namespace App\Filament\Resources\TherapistTypes\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class TherapistTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
               ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->searchable(),

                TextColumn::make('branch.name')
                        ->label('Branch')
                        ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)
                        ->sortable()
                        ->toggleable(),

                TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}