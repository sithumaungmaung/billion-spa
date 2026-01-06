<?php

namespace App\Filament\Resources\Rooms\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
// use Illuminate\Validation\Rules\Exists;
use Filament\Forms\Components\TextInput;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),

                TextInput::make('description')
                    ->label('Description'),

                TextInput::make('floor')
                    ->label('Floor')
                    ->numeric()
                    ->nullable(),

                TextInput::make('room_number')
                    ->label('Room Number')
                    ->numeric()
                    ->unique(
                        modifyRuleUsing: fn ($rule, $get) =>
                            $rule->where('branch_id', $get('branch_id')),
                            ignoreRecord: true,
                        )
                    ->required(),

                Select::make('branch_id')
                    ->label('Branch')
                    ->preload()
                    ->relationship('branch', 'name')
                    ->required(),
            ]);
    }
}