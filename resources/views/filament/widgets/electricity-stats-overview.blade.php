{{-- resources/views/filament/widgets/electricity-stats-overview.blade.php --}}
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            {{-- Grafiek bovenaan --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                <canvas id="consumption-chart-{{ $this->getId() }}" style="max-height: 400px;"></canvas>
            </div>

            {{-- Tabel onderaan --}}
            @php
                $tableData = $this->getTableData();
            @endphp

            @if (count($tableData['headers']) > 0)
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm border border-gray-300 dark:border-gray-600">
                        <thead>
                            {{-- Eerste rij: ID's --}}
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 border-r border-gray-300 dark:border-gray-600">
                                    Periode
                                </th>
                                @foreach ($tableData['headers'] as $utilityType)
                                    <th
                                        class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 border-r border-gray-300 dark:border-gray-600 last:border-r-0">
                                        ID: {{ $utilityType->id }}
                                    </th>
                                @endforeach
                            </tr>
                            {{-- Tweede rij: Namen --}}
                            <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-300 dark:border-gray-600">
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                    Maand
                                </th>
                                @foreach ($tableData['headers'] as $utilityType)
                                    <th
                                        class="px-3 py-2 text-right text-xs font-medium text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 last:border-r-0">
                                        {{ $utilityType->name }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach ($tableData['rows'] as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td
                                        class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap border-r border-gray-300 dark:border-gray-600">
                                        {{ $row['date']->format('M Y') }}
                                    </td>
                                    @foreach ($tableData['headers'] as $utilityType)
                                        <td
                                            class="px-3 py-2 text-sm text-right text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 last:border-r-0">
                                            @if (isset($row[$utilityType->id]) && $row[$utilityType->id] !== null)
                                                {{ number_format($row[$utilityType->id], 2, ',', '.') }}
                                                {{-- <span class="text-xs text-gray-500">{{ $utilityType->unit }}</span> --}}
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
                const ctx = document.getElementById('consumption-chart-{{ $this->getId() }}');
                let chart = null;

                function updateChart() {
                    const data = @js($this->getChartData());

                    if (chart) {
                        chart.destroy();
                    }

                    chart = new Chart(ctx, {
                        type: 'line',
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
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

                updateChart();
            </script>
        @endscript
    </x-filament::section>
</x-filament-widgets::widget>
