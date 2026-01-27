<x-filament-panels::page>
    <x-filament::section>
        <div class="flex items-end gap-4 mb-3">
            <div class="flex-1">
                <x-filament::input.wrapper label="Select Type">
                    <x-filament::input.select wire:model.live="onePlusone">
                        <option value="1">Normal</option>
                        <option value="2">1+1</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
            <div class="flex-1">
                <x-filament::button wire:click="applyOnePlusOne">
                    Apply
                </x-filament::button>
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
                <x-filament::button wire:click="assign">
                    Print Preview
                </x-filament::button>
            </div>
            <div class="col">
                <x-filament::button wire:click="confirmBill">
                    Confirm Bill
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>

    <!-- system over view -->
    <x-filament::section>
        <h3>Room Information</h3>
        <table class="w-full text-sm text-left border-collapse
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
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $key + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $systemDailyRecord->room ? $systemDailyRecord->room->name : "" }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $systemDailyRecord->timeslot ? \Carbon\Carbon::parse($systemDailyRecord->timeslot->start_time)->format('H:i') . " - " . \Carbon\Carbon::parse($systemDailyRecord->timeslot->end_time)->format('H:i')  : "" }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->therapist ? $systemDailyRecord->therapist->name : "" }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->serviceType ? $systemDailyRecord->serviceType->title : "" }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->service_type_price }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->price }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $systemDailyRecord->service_type_price + $systemDailyRecord->price }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-filament::section>
</x-filament-panels::page>
