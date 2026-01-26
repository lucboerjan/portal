<x-filament-widgets::widget>
    <x-filament::section>

        <div style="display: flex; align-items: center; gap: 1rem;">
            <img src="{{ asset('afbeelding/app/retirement_man.png') }}"
                 class="logo-dashboard"
                 id="logo-retirement_man"
                 alt="retirement_man"
                 style="width: 64px; height: auto;">

            <h1 style="font-size: 1.5rem">Te presteren dagen tot pensioen: {{ $werkdagenTotPensioen }}</h1>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
