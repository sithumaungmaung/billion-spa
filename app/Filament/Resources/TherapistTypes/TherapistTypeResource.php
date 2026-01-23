<?php

namespace App\Filament\Resources\TherapistTypes;

use App\Filament\Resources\TherapistTypes\Pages\CreateTherapistType;
use App\Filament\Resources\TherapistTypes\Pages\EditTherapistType;
use App\Filament\Resources\TherapistTypes\Pages\ListTherapistTypes;
use App\Filament\Resources\TherapistTypes\Schemas\TherapistTypeForm;
use App\Filament\Resources\TherapistTypes\Tables\TherapistTypesTable;
use App\Models\TherapistType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TherapistTypeResource extends Resource
{
    protected static ?string $model = TherapistType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    protected static ?string $recordTitleAttribute = 'Therapist Type';



    protected static ?string $navigationLabel = "Therapist Types";

    protected static ?string $modelLabel = "Therapist Types";

    protected static string | UnitEnum | null $navigationGroup = 'Categories & Types';

    protected static ?int $navigationSort = 1;




    public static function form(Schema $schema): Schema
    {
        return TherapistTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TherapistTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTherapistTypes::route('/'),
            'create' => CreateTherapistType::route('/create'),
            'edit' => EditTherapistType::route('/{record}/edit'),
        ];
    }
}
