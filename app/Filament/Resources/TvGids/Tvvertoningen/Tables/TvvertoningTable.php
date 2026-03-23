<?php

namespace App\Filament\Resources\TvGids\Tvvertoningen\Tables;

use App\Models\Vertoning;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;


class TvvertoningTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('datum')
                    ->label('Datum')
                    ->sortable(),
                TextColumn::make('tvzender.naam')
                    ->label('Zender')
                    ->sortable(),
                TextColumn::make('imdbrating.titel')
                    ->label('Titel')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('imdbrating.jaar')
                    ->label('Jaar'),
                TextColumn::make('imdbrating.imdbrating')
                    ->label('Rating'),
                TextColumn::make('vertoningen_count')
                    ->label('###')
                    ->getStateUsing(fn (vertoning $r) =>
                        $r->imdbrating?->vertoningen()->count() ?? 0
                    )
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('datum', 'desc')
            ->filters([
                Filter::make('datum')
                    ->form([
                        DatePicker::make('datum')
                            ->label('Filter op datum')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(fn ($query, array $data) =>
                        $query->when(
                            $data['datum'] ?? null,
                            fn ($q, $v) => $q->where('datum', $v)
                        )
                    ),
            ])
            ->recordActions([
                Action::make('imdb_link')
                    ->label('')
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->url(fn (vertoning $r) => $r->imdbrating?->imdburl ?? '#')
                    ->openUrlInNewTab(),
                EditAction::make()->button()->color('info'),
                DeleteAction::make()->button()->color('warning'),
            ]);
    }
}