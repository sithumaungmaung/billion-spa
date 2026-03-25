<x-filament-panels::page>
    <div class="w-full space-y-6">
        <form wire:submit.prevent="createCustomer">
            <x-filament::section>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <x-filament-forms::field-wrapper id="name" label="Name" required>
                        <x-filament::input.wrapper :valid="!$errors->has('name')">
                            <x-filament::input wire:model.defer="name" id="name" placeholder="Full Name" />
                        </x-filament::input.wrapper>
                    </x-filament-forms::field-wrapper>

                    <x-filament-forms::field-wrapper id="phone" label="Phone" required>
                        <x-filament::input.wrapper :valid="!$errors->has('phone')">
                            <x-filament::input wire:model.defer="phone" id="phone" placeholder="09..." />
                        </x-filament::input.wrapper>
                    </x-filament-forms::field-wrapper>

                    <x-filament-forms::field-wrapper id="dob" label="Date of Birth">
                        <x-filament::input.wrapper :valid="!$errors->has('date_of_birth')">
                            <x-filament::input wire:model.defer="date_of_birth" id="dob" type="date" />
                        </x-filament::input.wrapper>
                    </x-filament-forms::field-wrapper>

                    <x-filament-forms::field-wrapper id="branch" label="Branch" required>
                        <x-filament::input.wrapper :valid="!$errors->has('selectedBranch')">
                            <x-filament::input.select wire:model.defer="selectedBranch" id="branch">
                                <option value="">Select a Branch</option>
                                @foreach ($branches as $id => $branch)
                                    <option value="{{ $branch->id }}">{{ $branch['name'] }}</option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </x-filament-forms::field-wrapper>

                </div>

                <x-slot name="footer">
                    <x-filament::button type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>Register Customer</span>
                        <span wire:loading>Registering...</span>
                    </x-filament::button>
                </x-slot>
            </x-filament::section>
        </form>
    </div>

    {{--  --}}
    <x-filament::section class="mt-8">
        <x-slot name="heading">Recent Customers</x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left divide-y divide-gray-200 dark:divide-white/5">
                <thead>
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th class="px-4 py-3 font-semibold">Name</th>
                        <th class="px-4 py-3 font-semibold">Phone</th>
                        <th class="px-4 py-3 font-semibold">Date of Birth</th>
                        <th class="px-4 py-3 font-semibold">Branch</th>
                        <th class="px-4 py-3 font-semibold text-right">Prepaid Amount</th>
                        <th class="px-4 py-3 font-semibold text-right">Current Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    @foreach ($this->getCustomerList() as $customer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                            <td class="px-4 py-3">{{ $customer->name }}</td>
                            <td class="px-4 py-3">{{ $customer->customerInfo?->phone }}</td>
                            <td class="px-4 py-3">{{ $customer->customerInfo?->date_of_birth ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <x-filament::badge color="info">
                                    {{ $customer->customerInfo?->branch->name }}
                                </x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                {{ $customer->customerInfo?->prepaid_amount ?? 'Not Paid' }}</td>
                            <td class="px-4 py-3 text-right">
                                {{ $customer->customerInfo?->current_amount ?? 'N/A' }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        <x-filament::pagination :paginator="$this->getCustomerList()" />
    </x-filament::section>

</x-filament-panels::page>
