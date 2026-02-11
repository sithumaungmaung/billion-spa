<x-filament-panels::page>



    <x-filament::section>

        <div class="flex my-4 gap-2">
            <x-filament::input.wrapper>
                <x-filament::input wire:model.live="searchKeyword" placeholder="Search invoice number" />
            </x-filament::input.wrapper>

            <x-filament::input.wrapper>
                <x-filament::input wire:model.live="startDate" value="{{ $this->startDate }}" type="date" />
            </x-filament::input.wrapper>

            <x-filament::input.wrapper>
                <x-filament::input wire:model.live="endDate" value="{{ $this->endDate }}" type="date" />
            </x-filament::input.wrapper>

        </div>

        <table class="w-full text-sm text-left border-collapse
           border border-gray-200 dark:border-white/10">
            <thead class="bg-gray-100 text-gray-700
                   dark:bg-white/5 dark:text-gray-300">
                <tr class="border-b border-gray-200 dark:border-white/10">
                    <th class="px-4 py-3  text-center">#</th>
                    <th class="px-4 py-3">Invoice No</th>
                    <th class="px-4 py-3 text-right">Date & Time</th>
                    <th class="px-4 py-3 text-right">Note</th>
                    <th class="px-4 py-3 text-right">Sub Total</th>
                    <th class="px-4 py-3 text-right">Grand Total</th>
                    <th class="px-4 py-3 text-center">Detail</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100
                   dark:divide-white/10">

                @foreach ($this->getInvoices() as $key => $invoice)
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $this->getInvoices()->firstItem() + $key }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $invoice['invoice_no'] }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $invoice['invoice_datetime'] }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $invoice['note'] ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono">
                            {{ $invoice['sub_total'] }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono font-semibold ">
                            {{ $invoice['grand_total'] }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            <x-filament::button wire:click="showInvoiceDetail('{{ $invoice['invoice_no'] }}')">
                                Detail
                            </x-filament::button>
                        </td>
                    </tr>
                @endforeach

                <!-- bill for product -->
                {{-- @foreach ($this->billItems as $key => $product)
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $key + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $product->product->name }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $product->quantity }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono">
                            {{ number_format($product->unit_price) }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono font-semibold ">
                            {{ number_format($product->quantity * $product->unit_price) }}
                        </td>
                    </tr>
                @endforeach --}}
                <!-- bill for product -->
                {{-- <tr
                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                    <td class="px-4 py-3 text-right font-mono" colspan="4"> <b>Total</b> </td>
                    <td class="px-4 py-3 text-right font-mono font-semibold"> {{ number_format($this->total) }} </td>
                </tr> --}}
            </tbody>
        </table>

        <div class="mt-2">
            <x-filament::pagination :paginator="$this->getInvoices()" />
        </div>
    </x-filament::section>

</x-filament-panels::page>
