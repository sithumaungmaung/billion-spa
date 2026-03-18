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
            'Total'             => $this['total']
        ];
    }
}
