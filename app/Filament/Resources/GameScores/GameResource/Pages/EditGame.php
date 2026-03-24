<?php

namespace App\Filament\Resources\GameScores\GameResource\Pages;

use App\Filament\Resources\GameScores\GameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;    

class EditGame extends EditRecord
{
    protected static string $resource = GameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('scorebord')
                ->label('Scorebord')
                ->icon(Heroicon::OutlinedRocketLaunch)
                ->color('success')
                ->hidden(fn () => $this->getRecord()->status !== 'active')
                ->url(fn () => GameResource::getUrl('scorebord', ['record' => $this->getRecord()])),

            Actions\DeleteAction::make()
            ->icon(Heroicon::OutlinedScissors),
        ];
    }
}
