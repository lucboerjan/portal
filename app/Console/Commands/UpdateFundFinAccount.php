<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Investment\FundEndofMonthResult;
use Illuminate\Support\Facades\Log;

class UpdateFundFinAccount extends Command
{

    protected $signature = 'app:update-fund-fin-account';
    protected $description = 'Bereken het saldoverschil voor de rekenignen gekoppeld aan de beleggingsfondsen';



    public function handle(FundEndofMonthResult $fundTracker)
    {
        $fondsen = \App\Models\InvestmentFund::all();
        foreach ($fondsen as $fund) {
            $result = $fundTracker->readAndStore($fund);
            Log::info($fund->naam . ": " . $result);
        }
    }
}
