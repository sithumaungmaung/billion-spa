<?php

namespace App\Http\Resources\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

       return [
            'Invoice_ID'        => $this['invoice_no'], // Use array syntax []
            'Title'             => $this['title'],
            'Unit_Price'        => $this['unit_price'],
            'Unit'              => $this['unit'],
            'Discount_per_unit' => $this['discount_per_unit'] ?? '-',
            'Therapist_Price'   => $this['therapist_price'] ?? '-',
            'Therapist'         => $this['therapist'] ?? 'N/A',
            'Total'             => $this['total'],
            'Start_time'        => $this['start_time'] ?? '-',
            'End_time'          => $this['end_time'] ?? '-',
            'Transaction_date'  => $this['transaction_date'] ?? '-',

        ];
    }
}