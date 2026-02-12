<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use Filament\Pages\Page;
use App\Models\InvoiceRoom;
use App\Models\DailyRoomRecord;

class InvoiceDetail extends Page
{
    protected string $view = 'filament.pages.invoice-detail';
    protected static bool $shouldRegisterNavigation = false;

    public ?string $invoice_no = null;
    public $invoiceDetail;
    public $dailyRoomRecords = [];
    public array $roomIds = [];

    protected array $queryString = [
        'invoice_no',
    ];

    public function mount(): void
    {

        $this->invoiceDetail = Invoice::where('invoice_no', $this->invoice_no)
        ->with('invoiceRooms', 'invoiceProducts', 'invoiceExtraServices', 'users', 'dailyRoomRecord.therapist')->first();

        $this->dailyRoomRecords = DailyRoomRecord::where('invoice_id', $this->invoiceDetail->id)->with('room', 'therapist', 'therapistType')->get();
    }

    public function previewInvoice() {

        $url = route('invoice.detail.pdf', [
            'invoice_id' => $this->invoiceDetail->id
        ]);
        $this->dispatch('invoice.detail.preview', url: $url);
    }

}
