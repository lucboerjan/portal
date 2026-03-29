<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header --}}
        <x-slot name="heading">
            Toepassingen op subdomeinen
        </x-slot>

        {{-- App cards --}}
        <div style="display:flex; flex-direction:row; flex-wrap:wrap; gap:5px;">
            @foreach ($this->getApps() as $app)
                @php
                    $colors = [
                        'primary' => '#3b82f6',
                        'success' => '#22c55e',
                        'warning' => '#f59e0b',
                        'danger' => '#ef4444',
                        'info' => '#06b6d4',
                    ];
                    $btnColor = $colors[$app['color'] ?? 'primary'] ?? '#3b82f6';
                @endphp
                <a href="{{ $app['url'] }}" target="{{ $app['target'] ?? '_blank' }}" rel="noopener noreferrer"
                    style="display:flex; flex-direction:column; align-items:center; gap:0.75rem; padding:1rem; border:1px solid #e5e7eb; border-radius:0.75rem; background:#fff; text-decoration:none; box-shadow:0 1px 3px rgba(0,0,0,.08); width:10rem;">
                    {{-- Icon container 5x5rem --}}
                    <div
                        style="display:flex; align-items:center; justify-content:center; width:5rem; height:5rem; border-radius:50%; background:#f3f4f6;">
                        @if (!empty($app['emoji']))
                            <span style="font-size:2.5rem; line-height:1;">{{ $app['emoji'] }}</span>
                        @else
                            <img src="{{ URL::to('afbeelding/app/' . $app['icon'] . '.png') }}"
                                style="width:2.5rem;height:2.5rem;">
                        @endif
                    </div>

                    {{-- Label button --}}
                    <span
                        style="display:inline-flex; align-items:center; justify-content:center; border-radius:0.5rem; padding:0.375rem 0.75rem; font-size:0.875rem; font-weight:600; color:#fff; background:{{ $btnColor }};">
                        {{ $app['label'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
