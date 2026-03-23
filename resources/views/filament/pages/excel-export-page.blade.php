<x-filament-panels::page>


    <x-filament::section>
        <div class="flex flex-wrap items-center justify-between py-4">
            {{-- <div class="flex items-center gap-2 my-4">
                <x-filament::input.wrapper>
                    <x-filament::input wire:model.live="searchKeyword" placeholder="Search invoice number" />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input wire:model.live="startDate" type="date" />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input wire:model.live="endDate" type="date" />
                </x-filament::input.wrapper>
            </div> --}}

            <div class="flex items-center gap-2 my-4">
                <x-filament::input.wrapper>
                    <x-filament::input wire:model.live="excelStartDate" type="date" />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input wire:model.live="excelEndDate" type="date" />
                </x-filament::input.wrapper>

                <x-filament::button wire:click="exportInvoice" icon="heroicon-m-arrow-down-tray">
                    Export
                </x-filament::button>
            </div>
        </div>


        <table class="w-full text-sm text-left border-collapse
           border border-gray-200 dark:border-white/10">
            <thead class="bg-gray-100 text-gray-700
                   dark:bg-white/5 dark:text-gray-300">
                <tr class="border-b border-gray-200 dark:border-white/10">
                    <th class="px-4 py-3  text-center">#</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3 text-right">File Name</th>
                    <th class="px-4 py-3 text-right">Remark</th>
                    <th class="px-4 py-3 text-right">Start Date</th>
                    <th class="px-4 py-3 text-right">End Date</th>
                    <th class="px-4 py-3 text-center">Export By</th>
                    <th class="px-4 py-3 text-right">Export At</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100
                   dark:divide-white/10">

                @foreach ($this->getExcelList() as $key => $excel)
                    <tr
                        class="hover:bg-gray-50 divide-x divide-gray-100 dark:divide-white/10 divide-y
                            dark:hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ $this->getExcelList()->firstItem() + $key }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $excel->title }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $excel->file_name }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ $excel->remark ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ \Carbon\Carbon::parse($excel->start_date)->format('Y-m-d') }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ \Carbon\Carbon::parse($excel->end_date)->format('Y-m-d') }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            {{ $excel->user->name }}
                        </td>

                        <td class="px-4 py-3 text-right font-mono">
                            {{ $excel->created_at }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            <x-filament::button wire:click="downloadExcel('{{ $excel->file_name }}')"
                                icon="heroicon-m-arrow-down-tray">
                                Download
                            </x-filament::button>
                        </td>
                    </tr>
                @endforeach


            </tbody>
        </table>

        <div class="mt-2">
            <x-filament::pagination :paginator="$this->getExcelList()" />
        </div>
    </x-filament::section>


</x-filament-panels::page>
