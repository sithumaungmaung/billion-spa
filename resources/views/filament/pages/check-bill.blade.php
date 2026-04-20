<x-filament-panels::page>
    <x-filament::section>

        <x-filament::section compact class="p-0 gap-4">

            <x-filament::section.heading class="mb-2">
                Prepid Deduction
            </x-filament::section.heading>

            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <div class="mb-2">Customer</div>
                    <x-filament::input.wrapper label="Select Type">
                        {{ $this->customer_form }}
                    </x-filament::input.wrapper>
                </div>

                <div class="flex-1">
                    <div class="grid grid-cols-2 gap-x-4">
                        {{-- Available Amount (Readonly) --}}
                        <x-filament-forms::field-wrapper id="available_amount_wrapper" label="Available Amount">
                            <x-filament::input.wrapper class="bg-gray-50 dark:bg-white/5">
                                <x-filament::input type="number" wire:model="availableAmount" readonly
                                    class="cursor-not-allowed font-medium text-primary-600" />
                                <x-slot name="prefix">
                                    <x-filament::icon icon="heroicon-m-wallet" class="h-5 w-5 text-gray-400" />
                                </x-slot>
                            </x-filament::input.wrapper>
                        </x-filament-forms::field-wrapper>

                        {{-- Deduction Amount (Input) --}}
                        @if (!$this->outstandingAmount)
                            <x-filament-forms::field-wrapper id="deduction_amount_wrapper" label="Deduction Amount">
                                <x-filament::input.wrapper :disabled="!$selectedCustomer">
                                    <x-filament::input id="deduction_amount" type="number" min="0" readonly
                                        class="cursor-not-allowed font-medium text-primary-600"
                                        max="{{ $this->total }}" step="500" wire:model.live="deductionAmount"
                                        :disabled="!$selectedCustomer" />

                                    {{-- <x-slot name="suffix">
                                        <div class="flex items-center pe-2">
                                            <x-filament::button size="xs" color="info"
                                                wire:click="deductionWithPrepaidForTotal" :disabled="!$selectedCustomer">
                                                Total Bill
                                            </x-filament::button>
                                        </div>
                                    </x-slot> --}}
                                </x-filament::input.wrapper>
                            </x-filament-forms::field-wrapper>
                        @endif

                        @if ($this->outstandingAmount)
                            <x-filament-forms::field-wrapper id="outstanding_amount_wrapper" label="Outstanding Amount">
                                <x-filament::input.wrapper :disabled="!$selectedCustomer">
                                    <x-filament::input id="oustanding_amount" type="number" min="0"
                                        step="500" wire:model.live="outstandingAmount" placeholder="0.00"
                                        :disabled="true" />

                                    {{-- <x-slot name="suffix">
                                        <div class="flex items-center pe-2">
                                            <x-filament::button size="xs" color="info"
                                                wire:click="deductionWithPrepaidForTotal" :disabled="!$selectedCustomer">
                                                Total Bill
                                            </x-filament::button>
                                        </div>
                                    </x-slot> --}}
                                </x-filament::input.wrapper>
                            </x-filament-forms::field-wrapper>
                        @endif

                    </div>
                </div>
                {{-- <div class="flex-1"></div> --}}
            </div>
            @if ($this->free > 0)
                <div class="mt-3 text-sm">
                    <span class="border border-gray-200 dark:border-white/10 p-2 rounded-lg ">
                        {{-- <br> --}}
                        Promotion - {{ $this->buy }} + {{ $this->free }}
                    </span>
                </div>
            @endif
        </x-filament::section>

        {{-- // --}}
        <x-filament::section compact class="p-0">

            <x-filament::section.heading class="mb-2">
                Room Promotion
            </x-filament::section.heading>

            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <x-filament::input.wrapper label="Select Type">
                        <x-filament::input.select wire:model.live="buy" wire:change="applyPromotion">
                            <option value="1">Buy 1</option>
                            <option value="2">Buy 2</option>
                            <option value="3">Buy 3</option>
                            <option value="4">Buy 4</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                <div class="flex-1">
                    <x-filament::input.wrapper label="Select Type">
                        <x-filament::input.select wire:model.live="free" wire:change="applyPromotion">
                            <option value="0">Get 0</option>
                            <option value="1">Get 1</option>
                            <!-- <option value="2">Get 2</option> -->
                            <!-- <option value="3">Get 3</option> -->
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                {{-- <div class="flex-1"></div> --}}
            </div>
            @if ($this->free > 0)
                <div class="mt-3 text-sm">
                    <span class="border border-gray-200 dark:border-white/10 p-2 rounded-lg ">
                        {{-- <br> --}}
                        Promotion - {{ $this->buy }} + {{ $this->free }}
                    </span>
                </div>
            @endif
        </x-filament::section>

        <div class="flex items-end gap-4">
            <div class="flex-1">
                <x-filament::section compact class="mt-2">
                    <x-filament-forms::field-wrapper label="Discount Percent">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="discountPercentage">
                                <option value="0">Choose Discount %</option>
                                @foreach ([5, 10, 15, 20, 25, 30] as $percent)
                                    <option value="{{ $percent }}">{{ $percent }} % </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </x-filament-forms::field-wrapper>
                </x-filament::section>
            </div>
            <div class="flex-1">
                <x-filament::section compact class="mt-2">
                    <x-filament-forms::field-wrapper label="Service Charge Percent">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model.live="serviceChargePercentage">
                                <option value="0">Choose service Charge %</option>
                                @foreach ([5, 10, 15, 20, 25, 30] as $percent)
                                    <option value="{{ $percent }}">{{ $percent }} % </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </x-filament-forms::field-wrapper>
                </x-filament::section>
            </div>
            {{-- <div class="flex-1"></div> --}}
        </div>





        <table
            class="w-full text-sm text-left border-collapse
           border border-gray-200 dark:border-white/10 mt-2">
            <thead class="bg-gray-100 text-gray-700
                   dark:bg-white/5 dark:text-gray-300">
                <tr class="border-b border-gray-200 dark:border-white/10">
                    <th class="px-4 py-3 w-12 text-center">#</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Date & Time</th>
                    {{-- <th class="px-4 py-3">Section</th> --}}
                    <th class="px-4 py-3 text-right">Unit Price</th>
                    <th class="px-4 py-3 text-right">Unit</th>
                    <th class="px-4 py-3 text-right">Discount</th>
                    <th class="px-4 py-3 text-right">Therapist Price</th>
                    <th class="px-4 py-3 text-right">Total</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100
                   dark:divide-white/10">

                {{-- Room --}}
                @foreach ($this->billRooms as $key => $billRoom)
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $key + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $billRoom['room_name'] }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ \Carbon\Carbon::parse($billRoom['start_time'])->isoFormat('DD/MM/YYYY') }}
                            <span class="text-gray-500 dark:text-gray-400"> -
                                {{ \Carbon\Carbon::parse($billRoom['start_time'])->isoFormat('hh:mm A') }}
                            </span>
                            <br>
                            {{ \Carbon\Carbon::parse($billRoom['end_time'])->isoFormat('DD/MM/YYYY') }}
                            <span class="text-gray-500 dark:text-gray-400"> -
                                {{ \Carbon\Carbon::parse($billRoom['end_time'])->isoFormat('hh:mm A') }}
                            </span>
                        </td>

                        {{-- <td class="px-4 py-3 font-medium">
                            {{ $billRoom['total_time'] ?? '-' }}
                        </td> --}}

                        <td class="px-4 py-3 text-right font-mono">
                            {{ $billRoom['unit_price'] }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $billRoom['quantity'] }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            -
                        </td>

                        <td class="px-4 py-3 text-right font-mono">
                            {{ $billRoom['therapist_price'] }}
                        </td>


                        <td class="px-4 py-3 text-right font-mono font-semibold ">
                            {{ $billRoom['total_price'] }}
                        </td>
                    </tr>
                @endforeach

                <!-- bill for product items -->
                @foreach ($this->billItems as $key => $productSale)
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $key + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $productSale->product->name }}
                        </td>

                        <td class="px-4 py-3 font-medium text-center">
                            -
                        </td>

                        {{-- <td class="px-4 py-3 font-medium text-center">
                            -
                        </td> --}}

                        <td class="px-4 py-3 text-right font-mono">
                            {{ number_format($productSale->unit_price) }}
                        </td>


                        <td class="px-4 py-3 text-right">
                            {{ $productSale->quantity }}
                        </td>

                        <td class="px-1 py-1 font-medium w-[150px]">
                            <div class="flex items-center gap-2">

                                <x-filament::input.wrapper class="flex-1 !rounded-none !ring-0 !shadow-none !border-0">
                                    <x-filament::input type="number" min="0" step="500"
                                        x-on:input="if ($event.target.value > {{ $productSale->unit_price }}) $event.target.value = {{ $productSale->unit_price }}"
                                        type="number" max="{{ $productSale->unit_price }}" placeholder="disc/unit"
                                        wire:model.lazy="applyProductDiscount.{{ $productSale->id }}"
                                        class="w-full min-w-0 !rounded-none !ring-0 !border-0 focus:!ring-0 focus:!border-0" />
                                </x-filament::input.wrapper>

                            </div>
                        </td>

                        <td class="px-4 py-3 font-medium text-center">
                            -
                        </td>

                        <td class="px-4 py-3 text-right font-mono font-semibold ">

                            @php
                                $discount = (int) ($this->applyProductDiscount[$productSale->id] ?? 0);
                                $total = $productSale->quantity * ((int) $productSale->unit_price - $discount);
                            @endphp
                            {{ number_format($total) }}

                            {{-- @if ($discount >= $productSale->unit_price)
                                Error
                            @else
                                {{ number_format($total) }}
                            @endif --}}
                            {{-- {{ number_format(
                                $productSale->quantity *
                                    ((int) $productSale->unit_price - (int) ($this->applyProductDiscount[$productSale->id] ?? 0)),
                            ) }} --}}
                        </td>
                    </tr>
                @endforeach


                <!-- bill for Extra Service -->
                @foreach ($this->billExtraServices as $service)
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $key + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $service->extraService->title }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            -
                        </td>

                        {{-- <td class="px-4 py-3 text-center">
                            -
                        </td> --}}

                        <td class="px-4 py-3 text-right font-mono">
                            {{ number_format($service->unit_price) }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $service->quantity }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            -
                        </td>


                        <td class="px-4 py-3 text-center">
                            -
                        </td>

                        <td class="px-4 py-3 text-right font-mono font-semibold ">
                            {{ number_format($service->total_price) }}
                        </td>
                    </tr>
                @endforeach


                <tr
                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                    <td class="px-4 py-3 text-right font-mono" colspan="7"> <b>Sub Total</b> </td>
                    <td class="px-4 py-3 text-right font-mono font-semibold"> {{ number_format($this->subTotal) }}
                    </td>
                </tr>

                {{-- <tr
                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                    <td class="px-4 py-2 text-right font-mono" colspan="7"> <b>Product Discounts</b> </td>
                    <td class="px-4 py-2 text-right font-mono font-semibold">
                        {{ number_format($this->totalProductDiscount) }}
                    </td>

                </tr> --}}

                <tr
                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                    <td class="px-4 py-2 text-right font-mono" colspan="7"> <b>Invoice Discount</b> </td>
                    <td class="px-4 py-2 text-right font-mono font-semibold">
                        {{ number_format($this->discountAmountByPercentage) }}
                    </td>
                </tr>

                {{-- <tr
                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                    <td class="px-4 py-2 text-right font-mono" colspan="7"> <b>Total Discount</b> </td>
                    <td class="px-4 py-2 text-right font-mono font-semibold">
                        {{ number_format($this->totalDiscountAmount) }}
                    </td>
                </tr> --}}

                <tr
                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                    <td class="px-4 py-2 text-right font-mono" colspan="7"> <b>Service Charge</b> </td>
                    <td class="px-4 py-2 text-right font-mono font-semibold">
                        {{ number_format($this->serviceChargeAmount) }}
                    </td>
                </tr>

                <tr
                    class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                    <td class="px-4 py-3 text-right font-mono" colspan="7"> <b>Grand Total</b> </td>
                    <td class="px-4 py-3 text-right font-mono font-semibold">
                        {{-- {{ number_format($this->total - $this->totalDiscountAmount + $this->serviceChargeAmount) }} --}}
                        {{ number_format($this->total) }}

                    </td>
                </tr>

            </tbody>
        </table>

        <div class="flex items-center justify-between mt-3 ">
            <div class="flex mr-1 gap-2">
                <x-filament::button wire:click="printPreview" target="_blank">
                    Print Preview
                </x-filament::button>

                <x-filament::button wire:click="confirmBill"
                    wire:confirm="Are you sure you want to generate this invoice?">
                    Confirm Bill
                </x-filament::button>
                {{--  --}}
                {{-- <x-filament::button color="primary" class="bg-blue-700 hover:bg-blue-500 text-white"
                    wire:click="payWithPrepaid" wire:confirm="Are you sure you want to use Prepaid?"
                    :disabled="!$selectedCustomer || (!$deductionAmount || $this->availableAmount == 0)">
                    Use Prepaid
                </x-filament::button> --}}
            </div>
            <div class="text-right">
                <span>
                    Invoice By {{ $this->user->name }} .
                </span>
            </div>
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

                @foreach ($this->systemDailyRecords as $key => $systemDailyRecord)
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
    window.addEventListener('invoice.preview', event => {
        window.open(event.detail.url, '_blank');
    });

    window.addEventListener('prepaid-payment', event => {
        window.open(event.detail.url, '_blank');
    });
</script>


{{-- <script></script> --}}
