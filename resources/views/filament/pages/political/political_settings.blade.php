<x-filament-panels::page>
    @livewire(\Joinapi\PoliticalFlow\Http\Livewire\UpdatePoliticalNameForm::class, compact('political'))

    @livewire(\Joinapi\PoliticalFlow\Http\Livewire\PoliticalEmployeeManager::class, compact('political'))

    @if (!$political->personal_portal && Gate::check('delete', $political))
        <x-political-flow::section-border />
        @livewire(\Joinapi\PoliticalFlow\Http\Livewire\DeletePoliticalForm::class, compact('political'))
    @endif
</x-filament-panels::page>
