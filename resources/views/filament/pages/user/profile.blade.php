<x-filament-panels::page>
    @php
        $components = \Joinapi\PoliticalFlow\PoliticalFlow::getProfileComponents();
    @endphp

    @foreach($components as $index => $component)
        @livewire($component)

        @if($loop->remaining)
            <x-political-flow::section-border />
        @endif
    @endforeach
</x-filament-panels::page>
