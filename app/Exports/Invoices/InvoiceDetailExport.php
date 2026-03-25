<?php

namespace App\Exports\Invoices;

use App\Http\Resources\Invoice\InvoiceDetailResource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InvoiceDetailExport implements FromCollection, WithHeadings, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }


    public function collection()
    {
        return InvoiceDetailResource::collection($this->details);
    }

    public function title(): string
    {
        return 'INVOICE DETAILS';
    }

    public function headings(): array
    {
        return [
            'Invoice_ID',
            'Title',
            'Unit_Price',
            'Unit',
            'Discount_Per_Unit',
            'Therapist_Price',
            'Therapist',
            'Total'
        ];
    }
}
