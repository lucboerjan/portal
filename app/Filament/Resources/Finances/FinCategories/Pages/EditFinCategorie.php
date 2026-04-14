<?php

namespace App\Filament\Resources\Finances\FinCategories\Pages;

use App\Filament\Resources\Finances\FinCategories\FinCategorieResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\FinTransactie;

class EditFinCategorie extends EditRecord
{
    protected static string $resource = FinCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $data['totaal_bedrag'] = number_format(
            FinTransactie::whereHas('categorieen', fn($q) => 
                $q->where('fin_categorie.id', $record->id)
            )->sum('bedrag'),
            2, '.', ''
        );

        return $data;
    }
}
