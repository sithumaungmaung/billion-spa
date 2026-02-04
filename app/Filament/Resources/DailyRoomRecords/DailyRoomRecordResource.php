<?php

namespace App\Filament\Resources\DailyRoomRecords;

use App\Filament\Resources\DailyRoomRecords\Pages\CreateDailyRoomRecord;
use App\Filament\Resources\DailyRoomRecords\Pages\EditDailyRoomRecord;
use App\Filament\Resources\DailyRoomRecords\Pages\ListDailyRoomRecords;
use App\Filament\Resources\DailyRoomRecords\Schemas\DailyRoomRecordForm;
use App\Filament\Resources\DailyRoomRecords\Tables\DailyRoomRecordsTable;
use App\Models\DailyRoomRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DailyRoomRecordResource extends Resource
{
    protected static ?string $model = DailyRoomRecord::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Room Assign';

    // protected static ?string $navigationLabel = 'Daily Entries';
    protected static ?string $navigationLabel = 'Room Assign';

    public static function form(Schema $schema): Schema
    {
        return DailyRoomRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyRoomRecordsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => CreateDailyRoomRecord::route('/'),
            // 'index' => ListDailyRoomRecords::route('/'),
            // 'create' => CreateDailyRoomRecord::route('/create'),
            // 'edit' => EditDailyRoomRecord::route('/{record}/edit'),
        ];
    }
}
