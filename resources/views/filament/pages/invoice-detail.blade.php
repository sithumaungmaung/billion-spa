<x-filament-panels::page>
    <x-filament::section>

        <div class="flex items-end gap-4 mb-3">
            <div class="flex-1">
                <div class="h1 text-gray-500 ">
                    Invoice No:
                    <span class="text-gray-100">
                        {{ $invoiceDetail->invoice_no }}
                    </span>
                </div>
                <div class="h1 text-gray-500">
                    Confirm Date & Time: <span class="text-gray-100">{{ $invoiceDetail->invoice_datetime }}</span>
                </div>

                @if ($invoiceDetail->free > 0)
                    <div class="h1 text-gray-500">
                        Promotion : <span class="text-gray-100">
                            {{ $invoiceDetail->buy }} + {{ $invoiceDetail->free }}
                        </span>
                    </div>
                @endif

                <div class="h1 text-gray-500">
                    Confirm By: <span class="text-gray-100">
                        {{ $invoiceDetail->users->name ?? '-' }}
                    </span>
                </div>

                @if ($invoiceDetail->discount_percent > 0)
                    <div class="h1 text-gray-500">
                        Discount %: <span class="text-gray-100">
                            {{ $invoiceDetail->discount_percent }}
                        </span>
                    </div>
                    <div class="h1 text-gray-500">
                        Discount Amount: <span class="text-gray-100">
                            {{ $invoiceDetail->discount_amount }}
                        </span>
                    </div>
                @endif

                @if ($invoiceDetail->service_charge_percent > 0)
                    <div class="h1 text-gray-500">
                        Service Charge %:<span class="text-gray-100">
                            {{ $invoiceDetail->service_charge_percent }}
                        </span>
                    </div>
                    <div class="h1 text-gray-500">
                        Service Charge Amount: <span class="text-gray-100">
                            {{ $invoiceDetail->service_charge }}
                        </span>
                    </div>
                @endif
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
                    <th class="px-4 py-3 text-right">Unit Price</th>
                    <th class="px-4 py-3 text-right">Therapist Price</th>
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

                        <td class="px-4 py-3 text-right font-mono">
                            {{ $this->invoiceDetail->dailyRoomRecord->therapist->price }}
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

                        <td class="px-4 py-3 text-right font-mono">
                            -
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
                            {{ $service->quantity }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono">
                            {{ number_format($service->unit_price) }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono">
                            -
                        </td>

                        <td class="px-4 py-3 text-right font-mono font-semibold ">
                            {{ number_format($service->total_price) }}
                        </td>
                    </tr>
                @endforeach
                {{-- <hr> --}}
                {{-- <tr
                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                    <td class="px-4 py-3 text-center text-gray-500">
                        #
                    </td>
                    <td class="px-4 py-3 font-medium">
                        Discount Percent
                    </td>

                    <td class="px-4 py-3 font-medium">
                    </td>
                    <td class="px-4 py-3 font-medium">
                    </td>
                    <td class="px-4 py-3 font-medium">
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $invoiceDetail->discount_percent }} -> {{ $invoiceDetail->discount_amount }}
                    </td>

                </tr> --}}
                <!--  bill for Extra Service End-->
                <tr
                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                    <td class="px-4 py-3 text-right font-mono" colspan="5">
                        @if ($invoiceDetail->discount_percent || $invoiceDetail->service_charge_percent)
                            (Included Discount & Service Charge)
                        @endif
                        <b>Total</b>
                    </td>
                    <td class="px-4 py-3 text-right font-mono font-semibold">
                        {{ number_format($this->invoiceDetail->grand_total) }} </td>
                </tr>
            </tbody>
        </table>
        <div class="my-2">
            <x-filament::button wire:click="previewInvoice" target="_blank">
                Download Invoice
            </x-filament::button>
        </div>

    </x-filament::section>

    <!-- system over view -->
    <x-filament::section>
        <h3>Room Information</h3>
        <table
            class="w-full text-sm text-left border-collapse
           border border-gray-200 dark:border-white/10 mt-2">
            <thead class="bg-gray-100 text-gray-700
                   dark:bg-white/5 dark:text-gray-300">
                <tr class="border-b border-gray-200 dark:border-white/10">
                    <th class="px-4 py-3 w-12 text-center">#</th>
                    <th class="px-4 py-3">Room</th>
                    <th class="px-4 py-3 text-right">Time</th>
                    <th class="px-4 py-3 text-right">Therapist</th>
                    <th class="px-4 py-3 text-right">Therapist Price</th>
                    <th class="px-4 py-3 text-right">Service Type</th>
                    <th class="px-4 py-3 text-right">Service Type Price</th>
                    <th class="px-4 py-3 text-right">Room Price</th>
                    <th class="px-4 py-3 text-right">Total</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100
                   dark:divide-white/10">

                @foreach ($this->dailyRoomRecords as $key => $systemDailyRecord)
                    {{-- @dd($this->systemDailyRecords->toArray()) --}}
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $key + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $systemDailyRecord->room ? $systemDailyRecord->room->name : '' }}
                        </td>

                        <td class="px-4 py-3 font-medium text-end">
                            {{-- {{ $systemDailyRecord->timeslot ? \Carbon\Carbon::parse($systemDailyRecord->timeslot->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($systemDailyRecord->timeslot->end_time)->format('H:i') : '' }} --}}
                            {{ $systemDailyRecord->start_time }} - {{ $systemDailyRecord->end_time }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->therapist ? $systemDailyRecord->therapist->name : '' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->therapist ? $systemDailyRecord->therapist->price : '' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->therapistType ? $systemDailyRecord->therapistType->title : '' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->service_type_price }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->room_price }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->service_type_price + $systemDailyRecord->room_price + $systemDailyRecord->therapist->price }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-filament::section>

</x-filament-panels::page>

<script>
    window.addEventListener('invoice.detail.preview', event => {
        window.open(event.detail.url, '_blank');
    });
</script>
