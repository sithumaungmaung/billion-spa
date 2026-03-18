<?php

namespace App\Exports\Invoices;

use App\Http\Resources\Invoice\InvoiceSummaryResource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InvoiceSummaryExport implements FromCollection, WithTitle, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoices;


    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }


    public function collection()
    {
        // dd($this->invoices);
        $data = InvoiceSummaryResource::collection($this->invoices);
        // dd($data->toArray());
        return $data;
    }

    public function title(): string
    {
        return 'INVOICE SUMMARY';
    }

    public function headings(): array
    {
        return [
            'Invoice_ID',
            'Date_Time',
            'Sub_Total',
            'Grand_Total',
        ];
    }


}