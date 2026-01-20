<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use Filament\Pages\Page;
use BackedEnum;

class InvoiceList extends Page
{
    protected static bool $shouldRegisterNavigation = true;


    protected string $view = 'filament.pages.invoice-list';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text'; // Replace 'heroicon-o-document-text' with your desired icon


    public static function getNavigationLabel(): string
    {
        return 'Invoice';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Billing';
    }

    public $invoices;
    public $invoice_no;

    public function mount(): void
    {
        $this->invoices = Invoice::get();
    }

    public function showInvoiceDetail($invoice_no)
    {
        return redirect()->route('filament.admin.pages.invoice-detail', ['invoice_no' => $invoice_no]);

    }


}
