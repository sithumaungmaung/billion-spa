<?php

namespace App\Filament\Resources\DailyRoomRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DailyRoomRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('record_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('room_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('time_slot_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('therapist_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('room_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('service_type_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('service_type')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('invoice_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
