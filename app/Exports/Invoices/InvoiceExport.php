<?php

namespace App\Exports\Invoices;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InvoiceExport implements WithMultipleSheets
{

    protected $summary, $details;

    public function __construct($summary, $details)
    {
        $this->summary  = $summary;
        $this->details = $details;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets['INVOICE_SUMMARY'] = new InvoiceSummaryExport($this->summary);
        $sheets['INVOICE_DETAIL'] = new InvoiceDetailExport($this->details);

        return $sheets;
    }


}
