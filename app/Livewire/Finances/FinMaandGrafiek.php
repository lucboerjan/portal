<?php

namespace App\Livewire\Finances;

use App\Models\FinTransactie;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FinMaandGrafiek extends ChartWidget
{
    protected ?string $heading = 'Inkomsten vs Uitgaven per maand';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        $jaren = FinTransactie::selectRaw('YEAR(datum) as jaar')
            ->distinct()
            ->orderByDesc('jaar')
            ->pluck('jaar', 'jaar')
            ->toArray();

        return $jaren;
    }

    protected function getData(): array
    {
        $jaar = $this->filter ?? now()->year;

        $maanden = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Dec',
        ];

        $inkomsten = FinTransactie::selectRaw('MONTH(datum) as maand, SUM(bedrag) as totaal')
            ->whereYear('datum', $jaar)
            ->whereHas('categorieen', function ($q) {
                $q->where('richting', 'inkomst')
                    ->where('exclude', false);
            })
            ->groupByRaw('MONTH(datum)')
            ->get()
            ->keyBy('maand');

        $uitgaven = FinTransactie::selectRaw('MONTH(datum) as maand, SUM(ABS(bedrag)) as totaal')
            ->whereYear('datum', $jaar)
            ->whereHas('categorieen', function ($q) {
                $q->where('richting', 'uitgave')
                    ->where('exclude', false);
            })
            ->groupByRaw('MONTH(datum)')
            ->get()
            ->keyBy('maand');

        return [
            'datasets' => [
                [
                    'label'           => 'Inkomsten',
                    'data'            => collect($maanden)->keys()->map(
                        fn($m) =>
                        round($inkomsten->get($m)?->totaal ?? 0, 2)
                    )->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor'     => 'rgb(34, 197, 94)',
                    'borderWidth'     => 2,
                    'className'       => 'privacy-sensitive',
                ],
                [
                    'label'           => 'Uitgaven',
                    'data'            => collect($maanden)->keys()->map(
                        fn($m) =>
                        round($uitgaven->get($m)?->totaal ?? 0, 2)
                    )->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'borderColor'     => 'rgb(239, 68, 68)',
                    'borderWidth'     => 2,
                    'className'       => 'privacy-sensitive',
                ],
            ],
            'labels' => array_values($maanden),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getExtraAttributes(): array
{
    return [
        'class' => 'sensitive-chart',
    ];
}
}
