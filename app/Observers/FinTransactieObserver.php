<?php

namespace App\Observers;

use App\Models\FinRekening;
use App\Models\FinTransactie;

class FinTransactieObserver
{
    public function created(FinTransactie $transactie): void
    {
        $this->herbereken($transactie->rekening_id);
    }

    public function updated(FinTransactie $transactie): void
    {
        $this->herbereken($transactie->rekening_id);

        // Als rekening gewijzigd werd, ook oude rekening herberekenen
        if ($transactie->wasChanged('rekening_id')) {
            $this->herbereken($transactie->getOriginal('rekening_id'));
        }
    }

    public function deleted(FinTransactie $transactie): void
    {
        $this->herbereken($transactie->rekening_id);
    }

    private function herbereken(int $rekeningId): void
    {
        $saldo = FinTransactie::where('rekening_id', $rekeningId)
            ->sum('bedrag');

        FinRekening::where('id', $rekeningId)
            ->update(['saldo' => $saldo]);
    }
}