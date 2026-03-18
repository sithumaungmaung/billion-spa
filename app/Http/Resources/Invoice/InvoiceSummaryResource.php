<?php

namespace App\Http\Resources\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        // dump($this->);

        return [
            'Invoice_ID' => $this->invoice_no,
            'Date_Time' => $this->invoice_datetime,
            'Sub_Total' => $this->sub_total,
            'Grand_Total' => $this->grand_total,
        ];

    }
}