<x-filament-widgets::widget
    :widget="$this"
    class="fonds-koersontwikkeling-chart"

>
    <x-filament::section>
        {{-- Header met filter --}}
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Koersontwikkeling per Fonds</h3>
            
            @if($this->getFilters())
            <div class="w-64">
                <select 
                    wire:model.live="filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800"
                >
                    @foreach($this->getFilters() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        {{-- Chart Container --}}
        <div style="position: relative; height: 400px; width: 100%;">
            <canvas 
                id="fondsKoersChart"
                wire:key="chart-{{ $filter }}"
            ></canvas>
        </div>

        {{-- Debug Info --}}
        <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-800 rounded text-xs font-mono" id="debug-info">
            <strong>Debug Info:</strong>
            <div id="chart-status">Initializing...</div>
        </div>
    </x-filament::section>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        let chartInstance = null;

        function updateDebugInfo(message) {
            const debugDiv = document.getElementById('chart-status');
            if (debugDiv) {
                const timestamp = new Date().toLocaleTimeString();
                debugDiv.innerHTML += `<br>[${timestamp}] ${message}`;
            }
        }

        function createChart() {
            console.log('🚀 createChart() called');
            updateDebugInfo('createChart() called');

            const canvas = document.getElementById('fondsKoersChart');
            if (!canvas) {
                console.error('❌ Canvas element not found!');
                updateDebugInfo('ERROR: Canvas not found');
                return;
            }

            console.log('✅ Canvas found:', canvas);
            updateDebugInfo('Canvas found');

            // Destroy previous chart
            if (chartInstance) {
                console.log('🗑️ Destroying previous chart');
                updateDebugInfo('Destroying previous chart');
                chartInstance.destroy();
            }

            // Get data from Livewire
            const chartData = @json($this->getCachedData());
            
            console.log('📦 Chart data received:', chartData);
            console.log('📊 Datasets:', chartData.datasets?.length || 0);
            console.log('🏷️ Labels:', chartData.labels?.length || 0);
            
            updateDebugInfo(`Data: ${chartData.datasets?.length || 0} datasets, ${chartData.labels?.length || 0} labels`);

            if (!chartData.datasets || chartData.datasets.length === 0) {
                console.warn('⚠️ No datasets found');
                updateDebugInfo('WARNING: No datasets');
                return;
            }

            if (!chartData.labels || chartData.labels.length === 0) {
                console.warn('⚠️ No labels found');
                updateDebugInfo('WARNING: No labels');
                return;
            }

            console.log('First 5 data points:', chartData.datasets[0]?.data?.slice(0, 5) || []);
            console.log('First 5 labels:', chartData.labels.slice(0, 5));

            // Create chart
            try {
                chartInstance = new Chart(canvas, {
                    type: 'line',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': €' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                display: true,
                                beginAtZero: false,
                                ticks: {
                                    callback: function(value) {
                                        return '€' + value.toFixed(2);
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Koers (€)'
                                }
                            },
                            x: {
                                display: true,
                                ticks: {
                                    maxTicksLimit: 10,
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                title: {
                                    display: true,
                                    text: 'Datum'
                                }
                            }
                        },
                        animation: {
                            onComplete: function() {
                                console.log('✅ Chart render COMPLETE');
                                updateDebugInfo('Chart rendered successfully!');
                            }
                        }
                    }
                });

                console.log('✅ Chart created successfully:', chartInstance);
                updateDebugInfo('Chart created');

            } catch (error) {
                console.error('❌ Error creating chart:', error);
                updateDebugInfo('ERROR: ' + error.message);
            }
        }

        // Initial render
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 DOM loaded');
            updateDebugInfo('DOM loaded');
            setTimeout(createChart, 100);
        });

        // Re-render on Livewire updates
        document.addEventListener('livewire:initialized', () => {
            console.log('⚡ Livewire initialized');
            updateDebugInfo('Livewire initialized');
            
            Livewire.on('refresh', () => {
                console.log('🔄 Livewire refresh event');
                updateDebugInfo('Refresh event received');
                setTimeout(createChart, 100);
            });
        });

        // Watch for filter changes
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            succeed(({ snapshot, effect }) => {
                console.log('🔄 Component updated');
                updateDebugInfo('Component updated');
                setTimeout(createChart, 200);
            });
        });
    </script>
    @endpush
</x-filament-widgets::widget>