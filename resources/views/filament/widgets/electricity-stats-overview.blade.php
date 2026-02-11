{{-- resources/views/filament/widgets/electricity-stats-overview.blade.php --}}
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            {{-- Utility Type Selector --}}
            <div>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="selectedUtilityTypeId">
                        <option value="">Selecteer een type</option>
                        @foreach($this->getUtilityTypes() as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            @if($this->selectedUtilityTypeId)
                {{-- Tabel met maandelijks verbruik --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Jaar</th>
                                @foreach(['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'] as $month)
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">{{ $month }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->getTableData() as $row)
                                <tr>
                                    <td class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $row['year'] }}</td>
                                    @for($month = 1; $month <= 12; $month++)
                                        <td class="px-3 py-2 text-sm text-right text-gray-700 dark:text-gray-300">
                                            @if($row["month_$month"] !== null)
                                                {{ number_format($row["month_$month"], 2, ',', '.') }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Chart --}}
                <div>
                    <canvas id="consumption-chart-{{ $this->getId() }}" wire:ignore></canvas>
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
                                aspectRatio: 2.5,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Maandelijks Verbruik'
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
                                    }
                                }
                            }
                        });
                    }
                    
                    updateChart();
                    
                    Livewire.on('selectedUtilityTypeIdUpdated', () => {
                        setTimeout(() => updateChart(), 100);
                    });
                </script>
                @endscript
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>