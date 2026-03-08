<?php

namespace App\Filament\Resources\Finances\FinTransactions\Pages;

use App\Filament\Resources\Finances\FinTransactions\FinTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFinTransaction extends CreateRecord
{
    protected static string $resource = FinTransactionResource::class;
}
