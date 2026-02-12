{{-- resources/views/filament/widgets/electricity-stats-overview.blade.php --}}
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6" x-data="{ chartInit: false }" x-init="$nextTick(() => { chartInit = true })">
            {{-- Grafiek bovenaan --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4" style="height: 450px;" wire:ignore>
                <canvas id="consumption-chart-{{ $this->getId() }}"></canvas>
            </div>

            {{-- Tabel onderaan --}}
            @php
                $tableData = $this->getTableData();
            @endphp

            @if(count($tableData['headers']) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm" style="border-collapse: collapse; border: 1px solid rgb(209 213 219);">
                        <thead>
                            {{-- Eerste rij: ID's --}}
                            <tr style="background-color: rgb(249 250 251);">
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400" style="border: 1px solid rgb(209 213 219); width: 150px;">
                                    Periode
                                </th>
                                @foreach($tableData['headers'] as $utilityType)
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400" style="border: 1px solid rgb(209 213 219); width: 150px;">
                                        ID: {{ $utilityType->id }}
                                    </th>
                                @endforeach
                                {{-- Berekende kolommen --}}
                                @foreach($tableData['calculated_columns'] as $calcCol)
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400" style="border: 1px solid rgb(209 213 219); background-color: rgb(243 244 246); width: 150px;">
                                        -
                                    </th>
                                @endforeach
                            </tr>
                            {{-- Tweede rij: Namen --}}
                            <tr style="background-color: rgb(249 250 251);">
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 dark:text-gray-300" style="border: 1px solid rgb(209 213 219);">
                                    Maand
                                </th>
                                @foreach($tableData['headers'] as $utilityType)
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300" style="border: 1px solid rgb(209 213 219);">
                                        {{ $utilityType->name }}
                                    </th>
                                @endforeach
                                {{-- Berekende kolommen namen --}}
                                @foreach($tableData['calculated_columns'] as $calcCol)
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300" style="border: 1px solid rgb(209 213 219); background-color: rgb(243 244 246);" title="{{ $calcCol['description'] }}">
                                        {{ $calcCol['name'] }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800">
                            @foreach($tableData['rows'] as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap" style="border: 1px solid rgb(209 213 219);">
                                        {{ $row['date']->format('M Y') }}
                                    </td>
                                    @foreach($tableData['headers'] as $utilityType)
                                        <td class="px-3 py-2 text-sm text-right text-gray-700 dark:text-gray-300" style="border: 1px solid rgb(209 213 219);">
                                            @if(isset($row[$utilityType->id]) && $row[$utilityType->id] !== null)
                                                {{ number_format($row[$utilityType->id], 2, ',', '.') }}
                                                <span class="text-xs text-gray-500">{{ $utilityType->unit }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    {{-- Berekende kolommen waarden --}}
                                    @foreach($tableData['calculated_columns'] as $calcCol)
                                        <td class="px-3 py-2 text-sm text-right font-semibold text-gray-900 dark:text-gray-100" style="border: 1px solid rgb(209 213 219); background-color: rgb(249 250 251);">
                                            @if(isset($row[$calcCol['id']]) && $row[$calcCol['id']] !== null)
                                                {{ number_format($row[$calcCol['id']], 2, ',', '.') }}
                                                <span class="text-xs text-gray-500">{{ $calcCol['unit'] }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    Geen data beschikbaar
                </div>
            @endif
        </div>

        @script
        <script>
            (function() {
                setTimeout(function() {
                    const maxAttempts = 50;
                    let attempts = 0;
                    
                    function tryInitChart() {
                        attempts++;
                        
                        if (typeof Chart === 'undefined') {
                            if (attempts < maxAttempts) {
                                setTimeout(tryInitChart, 100);
                            }
                            return;
                        }
                        
                        const ctx = document.getElementById('consumption-chart-{{ $this->getId() }}');
                        
                        if (!ctx) {
                            console.error('Canvas element niet gevonden, poging:', attempts);
                            if (attempts < maxAttempts) {
                                setTimeout(tryInitChart, 100);
                            }
                            return;
                        }
                        
                        const data = @js($this->getChartData());
                        
                        console.log('Chart gevonden! Data:', data);
                        
                        if (!data.datasets || data.datasets.length === 0) {
                            console.warn('Geen datasets beschikbaar');
                            return;
                        }
                        
                        new Chart(ctx, {
                            type: 'line',
                            data: data,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Maandelijks Elektriciteitsverbruik'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.parsed.y !== null) {
                                                    label += context.parsed.y.toFixed(2) + ' kWh';
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Verbruik (kWh)'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Periode'
                                        }
                                    }
                                }
                            }
                        });
                    }
                    
                    tryInitChart();
                }, 250);
            })();
        </script>
        @endscript
    </x-filament::section>
</x-filament-widgets::widget>