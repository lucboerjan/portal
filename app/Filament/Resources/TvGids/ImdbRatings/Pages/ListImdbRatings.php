<?php

namespace App\Filament\Resources\TvGids\ImdbRatings\Pages;

use App\Filament\Resources\TvGids\ImdbRatings\ImdbRatingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use App\Models\Imdbrating;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;
use App\Jobs\CheckImdbUrls;

class ListImdbRatings extends ListRecords
{
    protected static string $resource = ImdbRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('check_duplicaten')
                ->label('Controleer duplicaten')
                ->icon(Heroicon::OutlinedMagnifyingGlass)
                ->color('warning')
                ->action(function () {
                    $duplicaten = Imdbrating::select('imdburl')
                        ->whereNotNull('imdburl')
                        ->where('imdburl', '!=', '')
                        ->groupBy('imdburl')
                        ->havingRaw('COUNT(*) > 1')
                        ->pluck('imdburl');

                    if ($duplicaten->isEmpty()) {
                        Notification::make()
                            ->title('Geen duplicaten gevonden')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title("{$duplicaten->count()} dubbele URL(s) gevonden")
                            ->body($duplicaten->join(', '))
                            ->warning()
                            ->persistent()
                            ->send();
                    }
                }),

            Action::make('check_urls')
                ->label('Controleer IMDB URLs')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->color('info')
                ->visible(Imdbrating::heeftUrlsNakijken())
                //->requiresConfirmation()
                ->modalDescription('Dit controleert alle IMDB URLs via HTTP. Dit kan enkele minuten duren en loopt op de achtergrond.')
                ->action(function () {
                    CheckImdbUrls::dispatch();

                    Notification::make()
                        ->title('URL check gestart')
                        ->body('De controle loopt op de achtergrond. Ververs de pagina later.')
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
