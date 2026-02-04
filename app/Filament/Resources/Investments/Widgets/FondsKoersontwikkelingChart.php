<?php

namespace App\Filament\Resources\Investments\Widgets;

use App\Models\InvestmentFund as Fonds;
use Filament\Widgets\ChartWidget;



class FondsKoersontwikkelingChart extends ChartWidget
{
    protected ?string $heading = 'Koersontwikkeling per Fonds (90 dagen)';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = null;

    protected ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $fondsId = $this->filter;

        if (!$fondsId) {
            $fonds = Fonds::has('InvestmentPurchase')->first();
            if (!$fonds) {
                return [
                    'datasets' => [],
                    'labels' => [],
                ];
            }
            $fondsId = $fonds->id;
        }

        $fonds = Fonds::with('InvestmentRate')->find($fondsId);

        if (!$fonds) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $dagkoersen = $fonds->InvestmentRate()
            ->where('datum', '>=', now()->subDays(2900))
            ->orderBy('datum', 'asc')
            ->get();

        $labels = $dagkoersen->pluck('datum')->map(fn($datum) => $datum->format('d-m-Y'))->toArray();
        $koersen = $dagkoersen->pluck('dagkoers')->map(fn($koers) => (float) $koers)->toArray();

        // Bereken gemiddelde voor referentielijn
        $gemiddelde = !empty($koersen) ? array_sum($koersen) / count($koersen) : 0;
        $gemiddeldeData = array_fill(0, count($labels), $gemiddelde);

        return [
            'datasets' => [
                [
                    'label' => 'Koers',
                    'data' => $koersen,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                    'pointRadius' => 2,
                    'pointHoverRadius' => 5,
                ],
                [
                    'label' => 'Gemiddelde',
                    'data' => $gemiddeldeData,
                    'borderColor' => 'rgb(251, 146, 60)',
                    'borderDash' => [5, 5],
                    'fill' => false,
                    'pointRadius' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        $fondsen = Fonds::has('InvestmentPurchase')
            ->orderBy('naam')
            ->pluck('naam', 'id')
            ->toArray();

        return $fondsen;
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.dataset.label + ': €' + context.parsed.y.toFixed(4);
                        }",
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'ticks' => [
                        'callback' => "function(value) {
                            return '€' + value.toFixed(2);
                        }",
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'maxTicksLimit' => 10,
                        'maxRotation' => 45,
                        'minRotation' => 45,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
        ];
    }
}