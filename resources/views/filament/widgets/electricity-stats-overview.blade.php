{{-- resources/views/filament/widgets/electricity-stats-overview.blade.php --}}
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6" x-data="{ chartInit: false }" x-init="$nextTick(() => { chartInit = true })">
            
            {{-- JAARLIJKSE SECTIE --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Jaaroverzicht</h3>
                
                {{-- Jaar Grafiek --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4" style="height: 450px;" wire:ignore>
                    <canvas id="consumption-chart-yearly-{{ $this->getId() }}"></canvas>
                </div>

                {{-- Jaar Tabel --}}
                @php
                    $yearlyTableData = $this->getYearlyTableData();
                @endphp

                @if(count($yearlyTableData['headers']) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm" style="border-collapse: collapse; border: 1px solid rgb(209 213 219);">
                            <thead>
                                {{-- Eerste rij: ID's --}}
                                <tr style="background-color: rgb(249 250 251);">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400" style="border: 1px solid rgb(209 213 219); width: 150px;">
                                        Periode
                                    </th>
                                    @foreach($yearlyTableData['headers'] as $utilityType)
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400" style="border: 1px solid rgb(209 213 219); width: 150px;">
                                            ID: {{ $utilityType->id }}
                                        </th>
                                    @endforeach
                                    {{-- Berekende kolommen --}}
                                    @foreach($yearlyTableData['calculated_columns'] as $calcCol)
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400" style="border: 1px solid rgb(209 213 219); background-color: rgb(243 244 246); width: 150px;">
                                            -
                                        </th>
                                    @endforeach
                                </tr>
                                {{-- Tweede rij: Namen --}}
                                <tr style="background-color: rgb(249 250 251);">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 dark:text-gray-300" style="border: 1px solid rgb(209 213 219);">
                                        Jaar
                                    </th>
                                    @foreach($yearlyTableData['headers'] as $utilityType)
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300" style="border: 1px solid rgb(209 213 219);">
                                            {{ $utilityType->name }}
                                        </th>
                                    @endforeach
                                    {{-- Berekende kolommen namen --}}
                                    @foreach($yearlyTableData['calculated_columns'] as $calcCol)
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300" style="border: 1px solid rgb(209 213 219); background-color: rgb(243 244 246);" title="{{ $calcCol['description'] }}">
                                            {{ $calcCol['name'] }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800">
                                @foreach($yearlyTableData['rows'] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap" style="border: 1px solid rgb(209 213 219);">
                                            {{ $row['period'] }}
                                        </td>
                                        @foreach($yearlyTableData['headers'] as $utilityType)
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
                                        @foreach($yearlyTableData['calculated_columns'] as $calcCol)
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
                        Geen jaardata beschikbaar
                    </div>
                @endif
            </div>

            {{-- SCHEIDINGSLIJN --}}
            <hr class="border-gray-300 dark:border-gray-600">

            {{-- MAANDELIJKSE SECTIE --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Maandoverzicht</h3>
                
                {{-- Maand Grafiek --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4" style="height: 450px;" wire:ignore>
                    <canvas id="consumption-chart-monthly-{{ $this->getId() }}"></canvas>
                </div>

                {{-- Maand Tabel --}}
                @php
                    $monthlyTableData = $this->getTableData();
                @endphp

                @if(count($monthlyTableData['headers']) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm" style="border-collapse: collapse; border: 1px solid rgb(209 213 219);">
                            <thead>
                                {{-- Eerste rij: ID's --}}
                                <tr style="background-color: rgb(249 250 251);">
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400" style="border: 1px solid rgb(209 213 219); width: 150px;">
                                        Periode
                                    </th>
                                    @foreach($monthlyTableData['headers'] as $utilityType)
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400" style="border: 1px solid rgb(209 213 219); width: 150px;">
                                            ID: {{ $utilityType->id }}
                                        </th>
                                    @endforeach
                                    {{-- Berekende kolommen --}}
                                    @foreach($monthlyTableData['calculated_columns'] as $calcCol)
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
                                    @foreach($monthlyTableData['headers'] as $utilityType)
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300" style="border: 1px solid rgb(209 213 219);">
                                            {{ $utilityType->name }}
                                        </th>
                                    @endforeach
                                    {{-- Berekende kolommen namen --}}
                                    @foreach($monthlyTableData['calculated_columns'] as $calcCol)
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300" style="border: 1px solid rgb(209 213 219); background-color: rgb(243 244 246);" title="{{ $calcCol['description'] }}">
                                            {{ $calcCol['name'] }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800">
                                @foreach($monthlyTableData['rows'] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap" style="border: 1px solid rgb(209 213 219);">
                                            {{ $row['date']->format('M Y') }}
                                        </td>
                                        @foreach($monthlyTableData['headers'] as $utilityType)
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
                                        @foreach($monthlyTableData['calculated_columns'] as $calcCol)
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
                        Geen maanddata beschikbaar
                    </div>
                @endif
            </div>
        </div>

        @script
        <script>
            (function() {
                setTimeout(function() {
                    const maxAttempts = 50;
                    let attempts = 0;
                    
                    function tryInitCharts() {
                        attempts++;
                        
                        if (typeof Chart === 'undefined') {
                            if (attempts < maxAttempts) {
                                setTimeout(tryInitCharts, 100);
                            }
                            return;
                        }
                        
                        // Initialiseer jaarlijkse chart
                        const yearlyCtx = document.getElementById('consumption-chart-yearly-{{ $this->getId() }}');
                        const monthlyCtx = document.getElementById('consumption-chart-monthly-{{ $this->getId() }}');
                        
                        if (!yearlyCtx || !monthlyCtx) {
                            console.error('Canvas elementen niet gevonden, poging:', attempts);
                            if (attempts < maxAttempts) {
                                setTimeout(tryInitCharts, 100);
                            }
                            return;
                        }
                        
                        const yearlyData = @js($this->getYearlyChartData());
                        const monthlyData = @js($this->getChartData());
                        
                        console.log('Charts gevonden!');
                        console.log('Yearly data:', yearlyData);
                        console.log('Monthly data:', monthlyData);
                        
                        // Jaarlijkse chart
                        if (yearlyData.datasets && yearlyData.datasets.length > 0) {
                            new Chart(yearlyCtx, {
                                type: 'line',
                                data: yearlyData,
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        },
                                        title: {
                                            display: true,
                                            text: 'Jaarlijks Elektriciteitsverbruik'
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
                                                text: 'Jaar'
                                            }
                                        }
                                    }
                                }
                            });
                        } else {
                            console.warn('Geen yearly datasets beschikbaar');
                        }
                        
                        // Maandelijkse chart
                        if (monthlyData.datasets && monthlyData.datasets.length > 0) {
                            new Chart(monthlyCtx, {
                                type: 'line',
                                data: monthlyData,
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
                        } else {
                            console.warn('Geen monthly datasets beschikbaar');
                        }
                    }
                    
                    tryInitCharts();
                }, 250);
            })();
        </script>
        @endscript
    </x-filament::section>
</x-filament-widgets::widget>