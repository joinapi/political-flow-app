@php
    $modals = \Joinapi\PoliticalFlow\PoliticalFlow::getModals();
@endphp

<div>
    @if (Gate::check('addpoliticalEmployee', $political))
        <x-political-flow::section-border />

        <!-- Add political Employee -->
        <x-political-flow::grid-section md="2">
            <x-slot name="title">
                {{ __('political-flow::default.grid_section_titles.add_political_employee') }}
            </x-slot>

            <x-slot name="description">
                {{ __('political-flow::default.grid_section_descriptions.add_political_employee') }}
            </x-slot>

            <x-filament::section>
                <x-filament-panels::form wire:submit="addpoliticalEmployee">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('political-flow::default.subheadings.companies.political_employee_manager') }}
                    </p>

                    <!-- Employee Email -->
                    <x-filament-forms::field-wrapper id="email" statePath="email" required="required" label="{{ __('political-flow::default.fields.email') }}">
                        <x-filament::input.wrapper class="overflow-hidden">
                            <x-filament::input id="email" type="email" wire:model="addpoliticalEmployeeForm.email" />
                        </x-filament::input.wrapper>
                    </x-filament-forms::field-wrapper>

                    <!-- Role -->
                    @if (count($this->roles) > 0)
                        <x-filament-forms::field-wrapper id="role" statePath="role" required="required" label="{{ __('political-flow::default.labels.role') }}">
                            <div x-data="{ role: @entangle('addpoliticalEmployeeForm.role').live }" class="relative z-0 mt-1 cursor-pointer rounded-lg border border-gray-200 dark:border-gray-700">
                                @foreach ($this->roles as $index => $role)
                                    <button type="button"
                                            @click="role = '{{ $role->key }}'"
                                            @class([
                                                'relative inline-flex w-full rounded-lg px-4 py-3 transition focus:z-10 focus:outline-none focus:ring-2 focus:border-primary-500 focus:ring-primary-500 dark:focus:border-primary-600 dark:focus:ring-primary-600',
                                                'border-t border-gray-200 dark:border-gray-700 rounded-t-none' => ($index > 0),
                                                'rounded-b-none' => (! $loop->last),
                                            ])
                                    >
                                        <div :class="role === '{{ $role->key }}' || 'opacity-50'">
                                            <!-- Role Name -->
                                            <div class="flex items-center">
                                                <div class="text-sm text-gray-600 dark:text-gray-400" :class="{'font-semibold': role === '{{ $role->key }}'}">
                                                    {{ $role->name }}
                                                </div>

                                                <div x-cloak :class="{ 'hidden': role !== '{{ $role->key }}' }">
                                                    <x-heroicon-o-check-badge class="text-primary-500 ml-2 h-5 w-5" />
                                                </div>
                                            </div>

                                            <!-- Role Description -->
                                            <div class="mt-2 text-left text-sm text-gray-600 dark:text-gray-400">
                                                {{ $role->description }}
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </x-filament-forms::field-wrapper>
                    @endif

                    <div class="text-left">
                        <x-filament::button type="submit">
                            {{ __('political-flow::default.buttons.add') }}
                        </x-filament::button>
                    </div>
                </x-filament-panels::form>
            </x-filament::section>
        </x-political-flow::grid-section>
    @endif

    @if ($political->politicalInvitations->isNotEmpty() && Gate::check('addpoliticalEmployee', $political))
        <x-political-flow::section-border />

        <!-- Pending Employee Invitations -->
        <x-political-flow::grid-section md="2">
            <x-slot name="title">
                {{ __('political-flow::default.action_section_titles.pending_political_invitations') }}
            </x-slot>

            <x-slot name="description">
                {{ __('political-flow::default.action_section_descriptions.pending_political_invitations') }}
            </x-slot>

            <div class="overflow-x-auto space-y-2 bg-white rounded-xl shadow dark:border-gray-600 dark:bg-gray-800 col-span-2 mt-5 sm:col-span-1 md:col-start-2 md:mt-0">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th colspan="3" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('political-flow::default.fields.email') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($political->politicalInvitations as $invitation)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        {{ $invitation->email }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-right">
                                    <!-- Manage political Employee Role -->
                                    @if (Gate::check('removepoliticalEmployee', $political))
                                        <x-filament::button size="sm" color="danger" outlined="true" wire:click="cancelpoliticalInvitation({{ $invitation->id }})">
                                            {{ __('political-flow::default.buttons.cancel') }}
                                        </x-filament::button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </x-political-flow::grid-section>
    @endif

    @if ($political->users->isNotEmpty())
        <x-political-flow::section-border />

        <!-- Manage political Employees -->
        <x-political-flow::grid-section md="2">
            <x-slot name="title">
                {{ __('political-flow::default.action_section_titles.political_employees') }}
            </x-slot>

            <x-slot name="description">
                {{ __('political-flow::default.action_section_descriptions.political_employees') }}
            </x-slot>

            <!-- political Employee List -->
            <div class="overflow-x-auto space-y-2 bg-white rounded-xl shadow dark:border-gray-600 dark:bg-gray-800 col-span-2 mt-5 sm:col-span-1 md:col-start-2 md:mt-0">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-white dark:bg-gray-800">
                    <tr>
                        <th scope="col" colspan="3" class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('political-flow::default.fields.name') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($political->users->sortBy('name') as $user)
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-left whitespace-nowrap">
                                <div class="flex items-center text-sm">
                                    <div class="flex-shrink-0">
                                        <x-filament-panels::avatar.user :user="$user" size="lg" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900 dark:text-gray-200">{{ $user->name }}</div>
                                        <div class="text-gray-600 dark:text-gray-400 hidden sm:block">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td colspan="1" class="px-6 py-4 whitespace-nowrap">
                                <div class="space-x-2 text-right">
                                    <!-- Manage political Employee Role -->
                                    @if (Gate::check('updatepoliticalEmployee', $political) && Joinapi\PoliticalFlow\PoliticalFlow::hasRoles())
                                        <x-filament::button size="sm" outlined="true" color="primary" wire:click="manageRole('{{ $user->id }}')">
                                            {{ Joinapi\PoliticalFlow\PoliticalFlow::findRole($user->employeeship->role)->name }}
                                        </x-filament::button>
                                    @elseif (Joinapi\PoliticalFlow\PoliticalFlow::hasRoles())
                                        <x-filament::button size="sm" disabled="true" outlined="true" color="gray">
                                            {{ Joinapi\PoliticalFlow\PoliticalFlow::findRole($user->employeeship->role)->name }}
                                        </x-filament::button>
                                    @endif

                                    <!-- Leave political -->
                                    @if ($this->user->id === $user->id)
                                        <x-filament::button size="sm" color="danger" wire:click="confirmLeavingpolitical">
                                            {{ __('political-flow::default.buttons.leave') }}
                                        </x-filament::button>

                                        <!-- Remove political Employee -->
                                    @elseif (Gate::check('removepoliticalEmployee', $political))
                                        <x-filament::button size="sm" color="danger" wire:click="confirmpoliticalEmployeeRemoval('{{ $user->id }}')">
                                            {{ __('political-flow::default.buttons.remove') }}
                                        </x-filament::button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </x-political-flow::grid-section>
    @endif

    <!-- Role Management Modal -->
    <x-filament::modal id="currentlyManagingRole" icon="heroicon-o-shield-check" icon-color="primary" alignment="{{ $modals['alignment'] }}" footer-actions-alignment="{{ $modals['formActionsAlignment'] }}" width="{{ $modals['width'] }}">
        <x-slot name="heading">
            {{ __('political-flow::default.modal_titles.manage_role') }}
        </x-slot>

        <div x-data="{ role: @entangle('currentRole').live }"
             class="relative z-0 mt-1 cursor-pointer rounded-lg border border-gray-200 dark:border-gray-700">
            @foreach ($this->roles as $index => $role)
                <button type="button"
                        @click="role = '{{ $role->key }}'"
                        @class([
                            'relative inline-flex w-full rounded-lg px-4 py-3 transition focus:z-10 focus:outline-none focus:ring-2 focus:border-primary-500 focus:ring-primary-500 dark:focus:border-primary-600 dark:focus:ring-primary-600',
                            'border-t border-gray-200 dark:border-gray-700 rounded-t-none' => ($index > 0),
                            'rounded-b-none' => (! $loop->last),
                        ])
                >
                    <div :class="role === '{{ $role->key }}' || 'opacity-50'">
                        <!-- Role Name -->
                        <div class="flex items-center">
                            <div class="text-sm text-gray-600 dark:text-gray-100" :class="role === '{{ $role->key }}' ? 'font-semibold' : ''">
                                {{ $role->name }}
                            </div>

                            <div x-cloak :class="{ 'hidden': role !== '{{ $role->key }}' }">
                                <x-heroicon-o-check-badge class="text-primary-500 ml-2 h-5 w-5" />
                            </div>
                        </div>

                        <!-- Role Description -->
                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                            {{ $role->description }}
                        </div>
                    </div>
                </button>
            @endforeach
        </div>

        <x-slot name="footerActions">
            @if($modals['cancelButtonAction'])
                <x-filament::button color="gray" wire:click="stopManagingRole">
                    {{ __('political-flow::default.buttons.cancel') }}
                </x-filament::button>
            @endif

            <x-filament::button wire:click="updateRole">
                {{ __('political-flow::default.buttons.save') }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <!-- Leave political Confirmation Modal -->
    <x-filament::modal id="confirmingLeavingpolitical" icon="heroicon-o-exclamation-triangle" icon-color="danger" alignment="{{ $modals['alignment'] }}" footer-actions-alignment="{{ $modals['formActionsAlignment'] }}" width="{{ $modals['width'] }}">
        <x-slot name="heading">
            {{ __('political-flow::default.modal_titles.leave_political') }}
        </x-slot>

        <x-slot name="description">
            {{ __('political-flow::default.modal_descriptions.leave_political') }}
        </x-slot>

        <x-slot name="footerActions">
            @if($modals['cancelButtonAction'])
                <x-filament::button color="gray" wire:click="cancelLeavingpolitical">
                    {{ __('political-flow::default.buttons.cancel') }}
                </x-filament::button>
            @endif

            <x-filament::button color="danger" wire:click="leavepolitical">
                {{ __('political-flow::default.buttons.leave') }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <!-- Remove political Employee Confirmation Modal -->
    <x-filament::modal id="confirmingpoliticalEmployeeRemoval" icon="heroicon-o-exclamation-triangle" icon-color="danger" alignment="{{ $modals['alignment'] }}" footer-actions-alignment="{{ $modals['formActionsAlignment'] }}" width="{{ $modals['width'] }}">
        <x-slot name="heading">
            {{ __('political-flow::default.modal_titles.remove_political_employee') }}
        </x-slot>

        <x-slot name="description">
            {{ __('political-flow::default.modal_descriptions.remove_political_employee') }}
        </x-slot>

        <x-slot name="footerActions">
            @if($modals['cancelButtonAction'])
                <x-filament::button color="gray" wire:click="cancelpoliticalEmployeeRemoval">
                    {{ __('political-flow::default.buttons.cancel') }}
                </x-filament::button>
            @endif

            <x-filament::button color="danger" wire:click="removepoliticalEmployee">
                {{ __('political-flow::default.buttons.remove') }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</div>
