<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Models\Invoice;
use Filament\Pages\Page;
use Livewire\WithPagination;

class InvoiceList extends Page
{
    protected static bool $shouldRegisterNavigation = true;


    protected string $view = 'filament.pages.invoice-list';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text'; // Replace 'heroicon-o-document-text' with your desired icon

    use WithPagination;

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
    public $searchKeyword;
    public $startDate;
    public $endDate;

    // public function mount(): void
    // {
    //     $this->invoices = Invoice::orderBy('id', 'desc')->paginate(10);
    // }


    public function getInvoices()
    {
        $query = Invoice::query();

        $query = $query->when($this->searchKeyword, function ($query){

            $query->where(function ($q) {
                $keywords = "%$this->searchKeyword%";

                $q->where('invoice_no', 'like', $keywords)
                    ->orWhere('invoice_datetime', 'like', $keywords)
                    ->orWhere('sub_total', 'like', $keywords)
                    ->orWhere('grand_total', 'like', $keywords);
            });
        });

        $query = $query->when($this->startDate && $this->endDate, function ($q) {
                 $q->whereBetween('invoice_datetime', [$this->startDate, $this->endDate  ." 23:59:59"]);
        });


        return $query->orderBy('id', 'desc')->paginate(10);

    }


    public function showInvoiceDetail($invoice_no)
    {
        return redirect()->route('filament.admin.pages.invoice-detail', ['invoice_no' => $invoice_no]);

    }




}
