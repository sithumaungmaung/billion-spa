<?php

namespace App\Filament\Pages;

use App\Exports\Invoices\InvoiceExport;
use App\Jobs\InvoiceExportExcelJob;
use App\Models\ExcelExportList;
use App\Services\ExportService;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportPage extends Page
{
    protected string $view = 'filament.pages.excel-export-page';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-m-document-text';
    protected static ?int $navigationSort = 4;
    public static function getNavigationGroup(): ?string
    {
        return 'Billing';
    }

    // =============

    public $excelStartDate, $excelEndDate;

    public $excelList;


    public function mount(){
        $this->excelStartDate = now()->toDateString();
        $this->excelEndDate = now()->toDateString();


    }

    public function getExcelList()
    {
        return ExcelExportList::orderBy('created_at', 'desc')->paginate(20);
    }

    public function exportInvoice()
    {
        $exportService = new ExportService();
        $exportService->exportInvoice($this->excelStartDate, $this->excelEndDate);

        // dispatch(new InvoiceExportExcelJob($this->excelStartDate, $this->excelEndDate));
        // return redirect()->route('filament.admin.pages.excel-export-page');
    }

    public function downloadExcel($filename)
    {
        return Storage::disk('public')->download('excel-export/' . $filename);
    }


}
