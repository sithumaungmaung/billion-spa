<x-filament-panels::page>
    <x-filament::section>
        

            <div class="flex items-end gap-4 mb-3">
                <div class="flex-1">
                    <x-filament::input.wrapper label="Select Type">
                        <x-filament::input.select wire:model.live="selectedType">
                                <option value="1">Normal</option>
                                <option value="2">1+1</option>
                                <option value="2">1+2</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
                <div class="flex-1">
                    <x-filament::button wire:click="assign">
                        Apply
                    </x-filament::button>
                </div>
            </div>
            

            <table
                class="w-full text-sm text-left border-collapse
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
                                {{ $billRoom->room?$billRoom->room->name :"-" }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                1
                            </td>

                            <td class="px-4 py-3 text-right font-mono">
                                {{ $billRoom->price }}
                            </td>

                            <td class="px-4 py-3 text-right font-mono font-semibold ">
                               {{ $billRoom->price }}
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
                        <tr class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
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
                    <x-filament::button wire:click="assign">
                        Confirm Bill
                    </x-filament::button>
                </div>
            </div>
    </x-filament::section>
</x-filament-panels::page>