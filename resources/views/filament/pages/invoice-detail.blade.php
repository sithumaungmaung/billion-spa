<x-filament-panels::page>
    <x-filament::section>


        <div class="flex items-end gap-4 mb-3">
            <div class="flex-1">
                <div class="h1 ">
                    Invoice No:
                    <span class="text-gray-500">
                        {{ $invoiceDetail->invoice_no }}
                    </span>
                </div>
                <div class="h1">
                    Confirm Date & Time: <span class="text-gray-500">{{ $invoiceDetail->invoice_datetime }}</span>
                </div>
                <div class="h1">
                    Confirm By: <span class="text-gray-500">
                        {{ $invoiceDetail->users->name ?? '-' }}
                    </span>
                </div>
            </div>

        </div>

        <table class="w-full text-sm text-left border-collapse
           border border-gray-200 dark:border-white/10">
            <thead class="bg-gray-100 text-gray-700
                   dark:bg-white/5 dark:text-gray-300">
                <tr class="border-b border-gray-200 dark:border-white/10">
                    <th class="px-4 py-3 w-12 text-center">#</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3 text-right">Unit</th>
                    <th class="px-4 py-3 text-right">Price</th>
                    <th class="px-4 py-3 text-right">Total</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100
                   dark:divide-white/10">

                @foreach ($this->invoiceDetail->invoiceRooms as $key => $invoiceRoom)
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $key + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $invoiceRoom['room_name'] }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $invoiceRoom['quantity'] }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono">
                            {{ $invoiceRoom['unit_price'] }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono font-semibold ">
                            {{ $invoiceRoom['total_price'] }}
                        </td>
                    </tr>
                @endforeach

                <!-- bill for product -->
                @foreach ($this->invoiceDetail->invoiceProducts as $key => $product)
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
                @endforeach
                <!-- bill for product -->

                <!-- bill for Extra Service -->

                @foreach ($this->invoiceDetail->invoiceExtraServices as $service)
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $key + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $service->extraService->title }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            1
                        </td>

                        <td class="px-4 py-3 text-right font-mono">
                            {{ number_format($service->unit_price) }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono font-semibold ">
                            {{ number_format($service->unit_price) }}
                        </td>
                    </tr>
                @endforeach
                <!--  bill for Extra Service End-->
                <tr
                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                    <td class="px-4 py-3 text-right font-mono" colspan="4"> <b>Total</b> </td>
                    <td class="px-4 py-3 text-right font-mono font-semibold">
                        {{ number_format($this->invoiceDetail->grand_total) }} </td>
                </tr>
            </tbody>
        </table>
        <div class="my-2">
            {{-- <x-filament::button wire:click="printPreview" target="_blank">
                Print Preview
            </x-filament::button> --}}
        </div>

    </x-filament::section>
</x-filament-panels::page>
