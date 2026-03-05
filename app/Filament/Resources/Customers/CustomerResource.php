<?php

namespace App\Filament\Resources\Customers;

use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\Schemas\CustomerForm;
use App\Filament\Resources\Customers\Tables\CustomersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'Customer';

    protected static ?string $modelLabel = "Customer";

    protected static string | UnitEnum | null $navigationGroup = 'Admin, Therapists & Customers ';

     protected static ?int $navigationSort = 2;


    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
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
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     // Performance ကောင်းအောင် Query ကို Optimize လုပ်ထားပါတယ်
    //     return parent::getEloquentQuery()
    //         ->whereHas('roles', function ($query) {
    //             $query->where('name', 'customer');
    //         });
    // }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->role('customer');
    }


    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }




}