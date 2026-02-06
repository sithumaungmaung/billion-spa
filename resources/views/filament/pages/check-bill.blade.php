<x-filament-panels::page>
    <x-filament::section>
        <div class="flex items-end gap-4 mb-3">
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
            <div class="flex-1"></div>
        </div>

        @if ($this->free > 0)
            <div class="flex mt-1 mb-1">
                <br> Promotion - {{ $this->buy }} + {{ $this->free }}
            </div>
        @endif

        <table
            class="w-full text-sm text-left border-collapse
           border border-gray-200 dark:border-white/10 mt-2">
            <thead class="bg-gray-100 text-gray-700
                   dark:bg-white/5 dark:text-gray-300">
                <tr class="border-b border-gray-200 dark:border-white/10">
                    <th class="px-4 py-3 w-12 text-center">#</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Time</th>
                    <th class="px-4 py-3">Section</th>
                    <th class="px-4 py-3 text-right">Unit</th>
                    <th class="px-4 py-3 text-right">Price</th>
                    <th class="px-4 py-3 text-right">Total</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100
                   dark:divide-white/10">

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
                            {{ $billRoom['start_time'] }} - {{ $billRoom['end_time'] }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $billRoom['total_time'] ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $billRoom['quantity'] }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono">
                            {{ $billRoom['unit_price'] }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono font-semibold ">
                            {{ $billRoom['total_price'] }}
                        </td>
                    </tr>
                @endforeach

                <!-- bill for product -->
                @foreach ($this->billItems as $key => $product)
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $key + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $product->product->name }}
                        </td>

                        <td class="px-4 py-3 font-medium text-center">
                            -
                        </td>

                        <td class="px-4 py-3 font-medium text-center">
                            -
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
                <!-- bill for product End -->


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
                        <td class="px-4 py-3 text-center">
                            -
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
                    <td class="px-4 py-3 text-right font-mono font-semibold"> {{ number_format($this->total) }} </td>
                </tr>
            </tbody>
        </table>

        <div class="flex mt-3">
            <div class="col mr-1">
                <x-filament::button wire:click="printPreview" target="_blank">
                    Print Preview
                </x-filament::button>
            </div>
            <div class="col">
                <x-filament::button wire:click="confirmBill"
                    wire:confirm="Are you sure you want to generate this invoice?">
                    Confirm Bill
                </x-filament::button>


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
                            {{ $systemDailyRecord->therapistType ? $systemDailyRecord->therapistType->title : '' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->service_type_price }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->room_price }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->service_type_price + $systemDailyRecord->room_price }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-filament::section>
</x-filament-panels::page>


<script>
    window.addEventListener('open-new-tab', event => {
        window.open(event.detail.url, '_blank');
    });
</script>
