<x-filament-panels::page>
    <x-filament::section>
        <x-filament::card>
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
                </tbody>
            </table>
        </x-filament::card>
    </x-filament::section>
</x-filament-panels::page>