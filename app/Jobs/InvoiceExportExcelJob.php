<?php

namespace App\Jobs;

use App\Services\ExportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
use Log;

class InvoiceExportExcelJob implements ShouldQueue
{
    use Queueable;
    // use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $startDate, $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $exportService = new ExportService();
        $exportService->exportInvoice($this->startDate, $this->endDate);
    }
}