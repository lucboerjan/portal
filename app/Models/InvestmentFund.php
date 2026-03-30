<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentFund extends Model
{
    protected $table = 'aandelen_fondsen';

    protected $fillable = [
        'isin',
        'naam',
        'url',
        'fondsType',
        'rekening_id',
    ];

    protected $casts = [
        'fondsType' => 'string',
    ];

    // -------------------------------------------------------------------------
    // Relaties
    // -------------------------------------------------------------------------

    public function InvestmentPurchase(): HasMany
    {
        return $this->hasMany(InvestmentPurchase::class, 'fondsID');
    }

    public function InvestmentRate(): HasMany
    {
        return $this->hasMany(InvestmentRate::class, 'fondsID');
    }

    public function rekening(): BelongsTo
    {
        return $this->belongsTo(FinRekening::class, 'rekening_id');
    }

    public function aandelenAankopen(): HasMany
    {
        return $this->hasMany(InvestmentPurchase::class, 'fondsID');
    }

    // -------------------------------------------------------------------------
    // Huidige waarde & aantallen
    // -------------------------------------------------------------------------

    /**
     * Totaal aantal aandelen (som van alle aankopen).
     */
    public function getTotalQuantityAttribute(): float
    {
        return $this->InvestmentPurchase->sum('aantal');
    }

    /**
     * Huidige waarde = meest recente dagkoers × totaal aantal aandelen.
     */
    public function getHuidigeWaardeAttribute(): float
    {
        $latestRate = $this->InvestmentRate()->latest('datum')->first();
        if (! $latestRate) {
            return 0;
        }
        return $this->InvestmentPurchase()->sum('aantal') * $latestRate->dagkoers;
    }

    // -------------------------------------------------------------------------
    // Aankoopwaarde & rendement
    // -------------------------------------------------------------------------

    /**
     * Som van (aantal × aankoopprijs) voor alle aankopen.
     */
    public function getTotaleInvestering(): float
    {
        return $this->aandelenAankopen()
            ->get()
            ->sum(fn($aankoop) => $aankoop->aantal * $aankoop->aankoopprijs);
    }

    public function getPurchaseAmountAttribute(): float
    {
        return $this->InvestmentPurchase->sum(
            fn($purchase) => $purchase->aantal * $purchase->aankoopprijs
        );
    }

    public function getRendementEuroAttribute(): float
    {
        return $this->getHuidigeWaardeAttribute() - $this->getTotaleInvestering();
    }

    public function getRendementPercentageAttribute(): float
    {
        $totaleInvestering = $this->getTotaleInvestering();
        if ($totaleInvestering <= 0) {
            return 0;
        }
        return ($this->getRendementEuroAttribute() / $totaleInvestering) * 100;
    }

    public function getReturnEuroAttribute(): float
    {
        return $this->current_value - $this->purchase_amount;
    }

    public function getReturnPercentageAttribute(): float
    {
        if ($this->purchase_amount == 0) {
            return 0;
        }
        return (($this->current_value - $this->purchase_amount) / $this->purchase_amount) * 100;
    }

    // -------------------------------------------------------------------------
    // Min / max / breakeven
    // -------------------------------------------------------------------------

    public function getCurrentValueAttribute(): float
    {
        $latestRate = $this->InvestmentRate()->latest('datum')->first();
        if (! $latestRate) {
            return 0;
        }
        return $this->total_quantity * $latestRate->dagkoers;
    }

    public function getMinValueAttribute(): float
    {
        $minRate = $this->InvestmentRate()->min('dagkoers');
        return $minRate ? $this->total_quantity * $minRate : 0;
    }

    public function getMaxValueAttribute(): float
    {
        $maxRate = $this->InvestmentRate()->max('dagkoers');
        return $maxRate ? $this->total_quantity * $maxRate : 0;
    }

    public function getBreakevenAttribute(): float
    {
        if ($this->total_quantity == 0) {
            return 0;
        }
        return $this->purchase_amount / $this->total_quantity;
    }

    // -------------------------------------------------------------------------
    // Koers helpers
    // -------------------------------------------------------------------------

    public function getDailyRateAttribute(): float
    {
        $latestRate = $this->InvestmentRate()->latest('datum')->first();
        return $latestRate ? $latestRate->dagkoers : 0;
    }

    public function getLastUpdatedAttribute(): ?string
    {
        $latestRate = $this->InvestmentRate()->latest('datum')->first();
        return $latestRate ? $latestRate->datum->format('d-m') : null;
    }

    public function getLaatsteKoersAttribute(): ?InvestmentRate
    {
        return $this->InvestmentRate()
            ->orderBy('datum', 'desc')
            ->first();
    }

    // -------------------------------------------------------------------------
    // Verschil-attributen (dag / maand / jaar)
    // -------------------------------------------------------------------------

    /**
     * Waardeverschil tussen de laatste twee beschikbare dagkoersen.
     */
    public function getDagverschilAttribute(): float
    {
        $rates = $this->InvestmentRate
            ->sortByDesc('datum')
            ->take(2);

        $huidigeKoers  = $rates->first()?->dagkoers ?? 0;
        $vorigeKoers   = $rates->skip(1)->first()?->dagkoers ?? $huidigeKoers;
        $aandelenNu    = $this->InvestmentPurchase->sum('aantal');

        return ($aandelenNu * $huidigeKoers) - ($aandelenNu * $vorigeKoers);
    }

    /**
     * Waardeverschil t.o.v. de laatste bekende koers van de vorige kalendermaand.
     */
    public function getMaandverschilAttribute(): float
    {
        $huidigeKoers = $this->InvestmentRate
            ->sortByDesc('datum')
            ->first()?->dagkoers ?? 0;

        // Expliciet: laatste dag van vorige kalendermaand
        $eindeVorigeMaand = now()->startOfMonth()->subDay(); // bv. 28-02-2026

        $vorigeKoers = $this->InvestmentRate
            ->where('datum', '<=', $eindeVorigeMaand)
            ->sortByDesc('datum')
            ->first()?->dagkoers ?? 0;

        $aandelenNu          = $this->InvestmentPurchase->sum('aantal');
        $aandelenVorigeMaand = $this->InvestmentPurchase
            ->where('datum', '<=', $eindeVorigeMaand)
            ->sum('aantal');

        Log::info("Maandverschil debug voor fonds {$this->naam}", [
            'huidigeKoers'        => $huidigeKoers,
            'vorigeKoers'         => $vorigeKoers,
            'eindeVorigeMaand'    => $eindeVorigeMaand->format('d-m-Y'),
            'aandelenNu'          => $aandelenNu,
            'aandelenVorigeMaand' => $aandelenVorigeMaand,
            'resultaat'           => ($aandelenNu * $huidigeKoers) - ($aandelenVorigeMaand * $vorigeKoers),
        ]);

        return ($aandelenNu * $huidigeKoers) - ($aandelenVorigeMaand * $vorigeKoers);
    }

    /**
     * Waardeverschil t.o.v. de laatste bekende koers van het vorige kalenderjaar.
     */
    public function getJaarverschilAttribute(): float
    {
        $huidigeKoers = $this->InvestmentRate
            ->sortByDesc('datum')
            ->first()?->dagkoers ?? 0;

        $eindeVorigJaar = now()->subYear()->endOfYear();

        $vorigeKoers = $this->InvestmentRate
            ->where('datum', '<=', $eindeVorigJaar)
            ->sortByDesc('datum')
            ->first()?->dagkoers ?? 0;

        $aandelenNu         = $this->InvestmentPurchase->sum('aantal');
        $aandelenVorigJaar  = $this->InvestmentPurchase
            ->where('datum', '<=', $eindeVorigJaar)
            ->sum('aantal');

        return ($aandelenNu * $huidigeKoers) - ($aandelenVorigJaar * $vorigeKoers);
    }
}
