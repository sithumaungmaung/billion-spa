<?php

namespace App\Filament\Resources\ExtraServices\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class ExtraServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Title')
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
