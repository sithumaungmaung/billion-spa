<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;

return [
    AppServiceProvider::class,
    // App\Providers\Filament\DozerPanelProvider::class,
    AdminPanelProvider::class,
];