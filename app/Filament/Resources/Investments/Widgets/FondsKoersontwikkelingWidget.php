<?php

namespace App\Filament\Resources\Investments\Widgets;

use App\Models\InvestmentFund as Fonds;
use Filament\Widgets\ChartWidget;

class FondsKoersontwikkelingWidget extends ChartWidget
{
    protected ?string $heading = 'Koersontwikkeling per Fonds';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected  string $view = 'filament.widgets.fonds-koers-chart-custom';
    public ?string $filter = null;

    protected function getData(): array
    {
        $fondsId = $this->filter;

        if (!$fondsId) {
            $fonds = Fonds::has('InvestmentPurchase')->first();
            if (!$fonds) {
                return ['datasets' => [], 'labels' => []];
            }
            $fondsId = $fonds->id;
        }

        $fonds = Fonds::find($fondsId);
        if (!$fonds) {
            return ['datasets' => [], 'labels' => []];
        }

        $dagkoersen = $fonds->InvestmentRate()
            ->where('datum', '>=', now()->subDays(90))
            ->orderBy('datum', 'asc')
            ->get();

        if ($dagkoersen->isEmpty()) {
            return ['datasets' => [], 'labels' => []];
        }

        $labels = [];
        $koersen = [];

        foreach ($dagkoersen as $dagkoers) {
            $labels[] = $dagkoers->datum->format('d-m-Y');
            $koersen[] = (float) $dagkoers->dagkoers;
        }

        return [
            'datasets' => [
                [
                    'label' => $fonds->naam,
                    'data' => $koersen,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                    'borderWidth' => 2,
                    'pointRadius' => 2,
                    'pointHoverRadius' => 5,
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
        return Fonds::has('InvestmentPurchase')
            ->orderBy('naam')
            ->pluck('naam', 'id')
            ->toArray();
    }

    protected function getOptions(): array
    {
        // GEEN RAWJS CALLBACKS - dit veroorzaakt de error!
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'display' => true,
                    'beginAtZero' => false,
                ],
                'x' => [
                    'display' => true,
                    'ticks' => [
                        'maxTicksLimit' => 10,
                        'maxRotation' => 45,
                        'minRotation' => 45,
                    ],
                ],
            ],
        ];
    }
}
