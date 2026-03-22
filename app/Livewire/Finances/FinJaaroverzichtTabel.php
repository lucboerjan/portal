<?php

namespace App\Livewire\Finances;

use App\Models\FinCategorie;
use App\Models\FinTransactie;
use Illuminate\Support\Collection;
use Livewire\Component;

class FinJaaroverzichtTabel extends Component
{
    public int $jaar;

    public function mount(): void
    {
        $this->jaar = now()->year;
    }

    public function getJaren(): array
    {
        return FinTransactie::selectRaw('YEAR(datum) as jaar')
            ->distinct()
            ->orderByDesc('jaar')
            ->pluck('jaar')
            ->toArray();
    }

    public function getMaanden(): array
    {
        return [
            1 => '01',
            2 => '02',
            3 => '03',
            4 => '04',
            5 => '05',
            6 => '06',
            7 => '07',
            8 => '08',
            9 => '09',
            10 => '10',
            11 => '11',
            12 => '12',
        ];
    }

    public function getData(): array
    {
        // Alle transacties voor het jaar ophalen met categorie
        $transacties = FinTransactie::with(['categorieKoppelingen.categorie.parent'])
            ->whereYear('datum', $this->jaar)
            ->whereHas('categorieen', fn($q) => $q->where('exclude', false))
            ->get();

        $inkomsten = [];
        $uitgaven  = [];

        foreach ($transacties as $transactie) {
            $koppeling = $transactie->categorieKoppelingen->first();
            if (!$koppeling) continue;

            $categorie = $koppeling->categorie;
            if (!$categorie) continue;
            if ($categorie->exclude) continue; // ← extra check

            // Hoofdcategorie bepalen
            $hoofdcategorie = $categorie->parent ?? $categorie;
            $maand          = (int) $transactie->datum->format('n');
            $bedrag         = (float) $transactie->bedrag;

            if ($categorie->richting->value === 'inkomst') {
                $inkomsten[$hoofdcategorie->omschrijving][$maand] =
                    ($inkomsten[$hoofdcategorie->omschrijving][$maand] ?? 0) + $bedrag;
            } else {
                $uitgaven[$hoofdcategorie->omschrijving][$maand] =
                    ($uitgaven[$hoofdcategorie->omschrijving][$maand] ?? 0) + abs($bedrag);
            }
        }

        ksort($inkomsten);
        ksort($uitgaven);

        return [
            'inkomsten' => $inkomsten,
            'uitgaven'  => $uitgaven,
        ];
    }

    public function render()
    {
        return view('livewire.finances.fin-jaaroverzicht-tabel', [
            'jaren'   => $this->getJaren(),
            'maanden' => $this->getMaanden(),
            'data'    => $this->getData(),
            'jaar'    => $this->jaar,
        ]);
    }
}
