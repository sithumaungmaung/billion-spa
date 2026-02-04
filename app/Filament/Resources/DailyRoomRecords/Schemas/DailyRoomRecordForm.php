<?php

namespace App\Filament\Resources\DailyRoomRecords\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DateTimePicker;

class DailyRoomRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('record_date')
                    ->default(now()->toDateString())
                    ->dehydrated() // This ensures the value is sent to the database
                    ->readOnly(),

                Select::make('room_id')
                    ->label('Room')
                    ->placeholder('Select Room')
                     ->relationship(
                        'room',
                        'name',
                        modifyQueryUsing: function ($query) {
                            $query->whereDoesntHave('dailyRoomRecords', function ($q) {
                                $q->availableRooms(); // 👈 now valid
                            });
                        }
                    )
                    ->required(),

                Select::make('therapist_id')
                    ->label('Therapist')
                    ->placeholder('Select Therapist')
                    ->relationship('therapist', 'name')
                    ->required(),

                Select::make('service_type')
                    ->label('Service Type')
                    ->placeholder('Select Service Type')
                    ->relationship('therapistType', 'title')
                    ->required(),

                TimePicker::make('start_time')
                    ->required()
                    ->seconds(true) // Optional: hides the seconds if you don't need them
                    ->displayFormat('H:i'),
                TimePicker::make('end_time')
                    ->required()
                    ->seconds(true) // Optional: hides the seconds if you don't need them
                    ->displayFormat('H:i'),

                // TextInput::make('room_price')
                //     ->required()
                //     ->numeric()
                //     ->default(0)
                //     ->prefix('$'),
                // TextInput::make('service_type_price')
                //     ->required()
                //     ->numeric()
                //     ->default(0)
                //     ->prefix('$'),

                // TextInput::make('invoice_id')
                //     ->numeric()
                //     ->default(null),
                // DateTimePicker::make('start_time'),
                // DateTimePicker::make('end_time'),
            ]);
    }
}