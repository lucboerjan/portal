<x-filament-widgets::widget>
    <x-filament::section>

        <div style="display: flex; align-items: center; gap: 1rem;">
            <img src="{{ asset('afbeelding/app/retirement_woman.png') }}"
                 class="logo-dashboard"
                 id="logo-retirement_man"
                 alt="retirement_man"
                 style="width: 64px; height: 64px;">

            <h1 style="font-size: 1.3rem">Te presteren dagen tot pensioen: {{ $werkdagenTotPensioen }} ({{ $kalenderDagen }})<br>
                Gekozen  pensioendatum : {{ $pensioendatum }}</h1>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>