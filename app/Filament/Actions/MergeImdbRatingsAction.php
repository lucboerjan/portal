<?php
// app/Filament/Actions/MergeImdbRatingsAction.php

namespace App\Filament\Actions;

use App\Models\Imdbrating;
use App\Models\Vertoning;
use Filament\Forms\Components\Radio;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Actions\BulkAction;
class MergeImdbRatingsAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'merge';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Films samenvoegen')
            ->icon('Heroicon::OutlinedArrowsPointingIn')
            ->color('warning')
            ->requiresConfirmation(false) // We tonen eigen modal
            ->schema(fn (Collection $records) => [
                Radio::make('master_id')
                    ->label('Welke film is de master record?')
                    ->helperText('Alle vertoningen worden hieraan gekoppeld. De andere records worden verwijderd.')
                    ->options(
                        $records->mapWithKeys(fn (ImdbRating $record) => [
                            $record->id => "{$record->titel} ({$record->jaar}) — ID: {$record->id} — Vertoningen: {$record->vertoningen_count}",
                        ])
                    )
                    ->required(),
            ])
            ->action(function (Collection $records, array $data): void {
                $masterId = (int) $data['master_id'];
                $duplicateIds = $records
                    ->pluck('id')
                    ->filter(fn ($id) => $id !== $masterId)
                    ->values();

                DB::transaction(function () use ($masterId, $duplicateIds): void {
                    // Herlink alle vertoningen naar de master
                    Vertoning::whereIn('imdbrating_id', $duplicateIds)
                        ->update(['imdbrating_id' => $masterId]);

                    // Verwijder de duplicaten
                    ImdbRating::whereIn('id', $duplicateIds)->delete();
                });
            })
            ->successNotificationTitle('Films succesvol samengevoegd')
            ->deselectRecordsAfterCompletion();
    }
}