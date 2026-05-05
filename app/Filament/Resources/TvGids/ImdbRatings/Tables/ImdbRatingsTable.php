<?php

namespace App\Filament\Resources\TvGids\ImdbRatings\Tables;

use App\Filament\Actions\MergeImdbRatingsAction;
use App\Models\Imdbrating;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;


class ImdbRatingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                    ->openUrlInNewTab()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('vertoningen_count')
                    ->label('# vertoningen')
                    ->counts('vertoningen')
                    ->sortable(),

                TextColumn::make('url_geldig')
                    ->label('URL OK?')
                    ->badge()
                    ->color(fn(?bool $state) => match ($state) {
                        true  => 'success',
                        false => 'danger',
                        null  => 'gray',
                    })
                    ->formatStateUsing(fn(?bool $state) => match ($state) {
                        true  => '✓ OK',
                        false => '✗ Fout',
                        null  => '— Niet gecontroleerd',
                    }),
            ])
            ->defaultSort('titel')
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->color('info'),
                    DeleteAction::make()->color('warning'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    MergeImdbRatingsAction::make()
                        ->label('Titels samenvoegen')
                        ->color('success')
                        ->icon(Heroicon::OutlinedLink)
                        ->before(function (Collection $records, BulkAction $action): void {
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
