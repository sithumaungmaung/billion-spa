<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;

class Reports extends Page
{
    protected string $view = 'filament.pages.reports';


    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Reports';

    // protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-check';
}