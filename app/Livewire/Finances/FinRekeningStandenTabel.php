<?php

namespace App\Livewire\Finances;

use App\Models\FinRekening;
use App\Models\FinRekeningStand;
use Illuminate\Support\Collection;
use Livewire\Component;

class FinRekeningStandenTabel extends Component
{
    public function getMaanden(): Collection
    {
        // Alle unieke jaar/maand combinaties ophalen
        return FinRekeningStand::selectRaw('jaar, maand')
            ->distinct()
            ->orderByDesc('jaar')
            ->orderByDesc('maand')
            ->get()
            ->map(fn($r) => [
                'jaar'  => $r->jaar,
                'maand' => $r->maand,
                'label' => str_pad($r->maand, 2, '0', STR_PAD_LEFT) . '/' . $r->jaar,
            ]);
    }

    public function getRekeningen(): Collection
    {
        return FinRekening::where('actief', true)
            ->orderBy('order')
            ->get();
    }

    public function getSaldi(): Collection
    {
        // Alle standen ophalen in één query
        return FinRekeningStand::all()
            ->keyBy(fn($r) => $r->rekening_id . '_' . $r->jaar . '_' . $r->maand);
    }

    public function render()
    {
        return view('livewire.finances.fin-rekening-standen-tabel', [
            'maanden'   => $this->getMaanden(),
            'rekeningen' => $this->getRekeningen(),
            'saldi'     => $this->getSaldi(),
        ]);
    }
}