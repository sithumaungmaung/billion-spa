<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use Filament\Pages\Page;
use App\Models\DailyRoomRecord;

class InvoiceDetail extends Page
{
    protected string $view = 'filament.pages.invoice-detail';
    protected static bool $shouldRegisterNavigation = false;

    public ?string $invoice_no = null;
    public $invoiceDetail;
    public $invoiceRooms;
    public array $roomIds = [];

    protected array $queryString = [
        'invoice_no',
    ];

    public function mount(): void
    {

        $this->invoiceDetail = Invoice::where('invoice_no', $this->invoice_no)
        ->with('invoiceRooms', 'invoiceProducts', 'invoiceExtraServices','users')->first();

        // $this->invoiceRooms = DailyRoomRecord::where('invoice_id', $this->invoiceDetail->id)->get();

    }


    // public function printPreview()
    // {

    //     $url = route('invoice.preview', [
    //         'ids' => implode(',', $this->roomIds),
    //         'onePlusOne' => $this->onePlusone
    //     ]);

    //     $this->dispatch('open-new-tab', url: $url);

    //     // return redirect()->away()->route('invoice.preview', ['ids' => implode(',', $this->roomIds), 'onePlusOne' => $this->onePlusone]);
    // }

}
