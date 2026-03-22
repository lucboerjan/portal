<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\Facades\Log;

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
        return $this->BelongsTo(FinRekening::class, 'rekening_id');
    }

    // Helper functions for calculations
    public function getTotalQuantityAttribute(): float
    {
        return $this->InvestmentPurchase->sum('aantal');
    }

    public function getPurchaseAmountAttribute(): float
    {
        return $this->InvestmentPurchase->sum(function ($purchase) {
            return $purchase->aantal * $purchase->aankoopprijs;
        });
    }

    public function getCurrentValueAttribute(): float
    {
        $latestRate = $this->rates()->latest('datum')->first();
        if (!$latestRate) {
            return 0;
        }
        return $this->total_quantity * $latestRate->dagkoers;
    }

    public function getMinValueAttribute(): float
    {
        $minRate = $this->rates()->min('dagkoers');
        if (!$minRate) {
            return 0;
        }
        return $this->total_quantity * $minRate;
    }

    public function getMaxValueAttribute(): float
    {
        $maxRate = $this->rates()->max('dagkoers');
        if (!$maxRate) {
            return 0;
        }
        return $this->total_quantity * $maxRate;
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

    public function getBreakevenAttribute(): float
    {
        if ($this->total_quantity == 0) {
            return 0;
        }
        return $this->purchase_amount / $this->total_quantity;
    }

    public function getDailyRateAttribute(): float
    {
        $latestRate = $this->rates()->latest('datum')->first();
        return $latestRate ? $latestRate->dagkoers : 0;
    }

    public function getLastUpdatedAttribute(): ?string
    {
        $latestRate = $this->rates()->latest('datum')->first();
        return $latestRate ? $latestRate->datum->format('d-m') : null;
    }

    // In je Fund model
    public function getHuidigeWaardeAttribute(): float
    {
        $latestRate = $this->InvestmentRate()->latest('datum')->first();
        if (!$latestRate) {
            return 0;
        }
        return $this->InvestmentPurchase()->sum('aantal') * $latestRate->dagkoers;
    }


    public function aandelenAankopen()
    {
        return $this->hasMany(InvestmentPurchase::class, 'fondsID');
    }

    // App\Models\InvestmentFund.php

    public function getLaatsteKoersAttribute()
    {
        return $this->investmentRate()
            ->orderBy('datum', 'desc')
            ->first();
    }


    public function getTotaleInvestering()
    {
        // Som van (aantal * prijs_per_aandeel) voor alle aankopen
        return $this->aandelenAankopen()
            ->get()
            ->sum(function ($aankoop) {
                return $aankoop->aantal * $aankoop->aankoopprijs;
            });
    }

    public function getRendementEuroAttribute()
    {
        return $this->getHuidigeWaardeAttribute() - $this->getTotaleInvestering();
    }

    public function getRendementPercentageAttribute()
    {
        $totaleInvestering = $this->getTotaleInvestering();

        if ($totaleInvestering > 0) {
            return ($this->getRendementEuroAttribute() / $totaleInvestering) * 100;
        }

        return 0;
    }


    public function getJaarverschilAttribute()
    {
        // 1. Laatste dagkoers nu
        $laatsteRateNu = $this->InvestmentRate
            ->sortByDesc('datum')
            ->first()?->dagkoers ?? 0;

        // 2. Einde vorig jaar
        $eindeVorigJaar = now()->subYear()->endOfYear();

        // 3. Laatste dagkoers vóór / op einde vorig jaar
        $laatsteRateVorigJaar = $this->InvestmentRate
            ->where('datum', '<=', $eindeVorigJaar)
            ->sortByDesc('datum')
            ->first()?->dagkoers ?? 0;

        // 4. Aandelen nu
        $aandelenNu = $this->InvestmentPurchase->sum('aantal');

        // 5. Aandelen op einde vorig jaar
        $aandelenVorigJaar = $this->InvestmentPurchase
            ->where('datum', '<=', $eindeVorigJaar)
            ->sum('aantal');

        // Debug
        //Log::info("dagkoersNu: $laatsteRateNu, dagkoersVorigJaar: $laatsteRateVorigJaar, aandelenNu: $aandelenNu, aandelenVorigJaar: $aandelenVorigJaar");

        // 6. Waarde nu en waarde einde vorig jaar
        $waardeNu = $aandelenNu * $laatsteRateNu;
        $waardeVorigJaar = $aandelenVorigJaar * $laatsteRateVorigJaar;

        return $waardeNu - $waardeVorigJaar;
    }


    public function getMaandverschilAttribute()
    {
        // 1. Laatste dagkoers nu
        $laatsteRateNu = $this->InvestmentRate
            ->sortByDesc('datum')
            ->first()?->dagkoers ?? 0;

        // 2. Einde vorige maand
        $eindeVorigeMaand = now()->subMonth()->endOfMonth();

        // 3. Laatste dagkoers vóór / op einde vorige maand
        $laatsteRateVorigeMaand = $this->InvestmentRate
            ->where('datum', '<=', $eindeVorigeMaand)
            ->sortByDesc('datum')
            ->first()?->dagkoers ?? 0;

        // 4. Aandelen nu
        $aandelenNu = $this->InvestmentPurchase->sum('aantal');

        // 5. Aandelen op einde vorig jaar
        $aandelenVorigeMaand = $this->InvestmentPurchase
            ->where('datum', '<=', $eindeVorigeMaand)
            ->sum('aantal');

        // Debug
        //Log::info("dagkoersNu: $laatsteRateNu, dagkoersVorigJaar: $laatsteRateVorigeMaand, aandelenNu: $aandelenNu, aandelenVorigeMaand: $aandelenVorigeMaand");

        // 6. Waarde nu en waarde einde vorig jaar
        $waardeNu = $aandelenNu * $laatsteRateNu;
        $waardeVorigeMaand = $aandelenVorigeMaand * $laatsteRateVorigeMaand;

        return $waardeNu - $waardeVorigeMaand;
    }

       public function getDagverschilAttribute()
{
    // 1. Haal de laatste 2 dagkoersen op
    $rates = $this->InvestmentRate
        ->sortByDesc('datum')
        ->take(2);

    $laatsteRateNu = $rates->first()?->dagkoers ?? 0;
    $laatsteRateGisteren = $rates->skip(1)->first()?->dagkoers ?? $laatsteRateNu;

    // 2. Aandelen vandaag
    $aandelenNu = $this->InvestmentPurchase->sum('aantal');

    // 3. Waarde vandaag en gisteren
    $waardeNu = $aandelenNu * $laatsteRateNu;
    $waardeGisteren = $aandelenNu * $laatsteRateGisteren;

    // Debug
    //Log::info("dagkoersNu: $laatsteRateNu, dagkoersGisteren: $laatsteRateGisteren, aandelenNu: $aandelenNu");

    return $waardeNu - $waardeGisteren;
}
}
