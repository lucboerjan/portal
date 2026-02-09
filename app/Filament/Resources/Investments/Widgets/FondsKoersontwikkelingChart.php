<?php

namespace App\Filament\Resources\Investments\Widgets;

use App\Models\InvestmentFund as Fund;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Log;

class FondsKoersontwikkelingChart extends ChartWidget
{
    protected  ?string $heading = 'Trend van de koersen';
    protected  ?string $maxHeight = '350px';
    protected  string $color = 'primary';
    public  ?string $filter = null;

    protected int|string|array $columnSpan = 'full';



    public function filters(): ?array
    {
        return Fund::pluck('name', 'id')->toArray();
    }

    protected function getData(): array
    {
        $fondsId = $this->filter;


        $fund = Fund::find($fondsId);

        if (! $fund) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $dagkoersen = $fund->InvestmentRate()
            ->orderBy('datum')
            ->get();


        $labels = $dagkoersen->pluck('datum')->map(fn($d) => $d->format('d-m-Y'));

        $filteredLabels = $labels/* ->filter(function ($date) {
            $month = (int) substr($date, 5, 2);
            $day   = (int) substr($date, 0, 2);

            // Toon enkel labels op de eerste dag van maand 1,5,9
            return $day === 1 && in_array($month, [1, 5, 9, 12]);
        })->values();  */
;
        return [
            'datasets' => [
                [
                    'label' => $fund->naam,
                    'data' => $dagkoersen->pluck('dagkoers'),
                    'borderColor' => '#3b82f6',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $filteredLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return Fund::has('InvestmentPurchase')
            ->orderBy('naam')
            ->pluck('naam', 'id')
            ->toArray();
    }

    public function mount(): void
    {
        $this->filter = Fund::has('InvestmentPurchase')
            ->orderBy('id')
            ->value('id');
    }
}
