<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Branch;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;



class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                DateTimePicker::make('email_verified_at')
                        ->disabledOn('edit')
                        ->default(now()->format('Y-m-d H:i:s'))
                        ->required(),
                        //    ->default(fn () => now());

               Select::make('adminBranches')
                    ->label('Branch')
                    ->relationship(
                        'adminBranches',
                        'name'
                    )
                    ->multiple()
                    ->preload()
                    ->required(),

            ]);
    }
}
