<?php

namespace App\Filament\Resources\Therapists\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class TherapistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone')
                    ->label('Phone number')
                    ->numeric()
                    ->required(),
                TextInput::make('therapist_code')
                    ->label('Therapist Code')
                    ->unique()
                    ->required(),
                TextInput::make('price')
                    ->label('Price')

                    ->numeric()
                    ->required(),
                Select::make('branch_id')
                    ->label('Branch')
                    ->preload()
                    ->relationship('branch', 'name')
                    ->required(),

            ]);
    }
}
