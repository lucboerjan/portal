<?php

namespace App\Filament\Resources\TvGids\ImdbRatings\Tables;

use App\Models\Imdbrating;
use App\Models\Vertoning;
use Dom\Text;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use App\Filament\Actions\MergeImdbRatingsAction;
use Filament\Notifications\Notification;

class ImdbRatingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn() => Imdbrating::query()->withCount('vertoningen'))
            ->columns([
                TextColumn::make('titel')
                    ->label('Titel van de film')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jaar')
                    ->label('Jaar uitgebracht')
                    ->sortable(),
                TextColumn::make('imdbrating')
                    ->label('IMDB Rating')
                    ->sortable(),
                TextColumn::make('imdburl')
                    ->label('IMDB URL')
                    ->url(fn(Imdbrating $r) => $r->imdburl)
                    ->openUrlInNewTab(),
                TextColumn::make('vertoningen_count')
                    ->label('# vertoningen')
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->button()->color('info'),
                    DeleteAction::make()->button()->color('warning'),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    MergeImdbRatingsAction::make()
                        ->label('Titels samenvoegen')
                        ->color('success')
                        ->icon('heroicon-o-link')

                        ->before(function (\Illuminate\Support\Collection $records, BulkAction $action): void {
                            \Illuminate\Support\Facades\Log::info('Before hook', ['count' => $records->count()]);

                            if ($records->count() < 2) {
                                Notification::make()
                                    ->title('Selecteer minimaal 2 films')
                                    ->warning()
                                    ->send();

                                $action->cancel();
                            }
                        }),

                ]),
            ]);
    }
}
