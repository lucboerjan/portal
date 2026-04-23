<?php

namespace App\Filament\Resources\Recipes\Recipes\Pages;

use App\Filament\Resources\Recipes\Recipes\RecipeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

class ViewRecipe extends ViewRecord
{
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            // ── Foto ─────────────────────────────────────────────────────────
            Section::make()
                ->schema([
                    ImageEntry::make('image')
                        ->hiddenLabel()
                        ->imageHeight(300)
                        ->columnSpanFull(),
                ])
                ->hidden(fn ($record) => ! $record->image)
                ->columnSpanFull(),

            // ── Basisinfo ─────────────────────────────────────────────────────
            Section::make('Basisinformatie')
                ->icon('Heroicon::OutlinedInformationCircle')
                ->schema([
                    TextEntry::make('title')
                        ->label('Titel')
                        ->weight(FontWeight::Bold)
                        ->columnSpanFull(),

                    TextEntry::make('category.name')
                        ->label('Categorie')
                        ->badge(),

                    TextEntry::make('cookingMethod.name')
                        ->label('Kookmethode')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('description')
                        ->label('Omschrijving')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            // ── Tijden & porties ──────────────────────────────────────────────
            Section::make('Tijden & porties')
                ->icon('Heroicon::OutlinedClock')
                ->schema([
                    TextEntry::make('prep_time')
                        ->label('Voorbereidingstijd')
                        ->suffix(' min')
                        ->placeholder('—'),

                    TextEntry::make('cook_time')
                        ->label('Bereidingstijd')
                        ->suffix(' min')
                        ->placeholder('—'),

                    TextEntry::make('total_time')
                        ->label('Totale tijd')
                        ->getStateUsing(fn ($record) => $record->total_time)
                        ->suffix(' min')
                        ->placeholder('—'),

                    TextEntry::make('servings')
                        ->label('Porties')
                        ->suffix(' pers.'),

                    TextEntry::make('difficulty')
                        ->label('Moeilijkheidsgraad')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'easy'   => 'Makkelijk',
                            'medium' => 'Gemiddeld',
                            'hard'   => 'Moeilijk',
                            default  => $state,
                        })
                        ->color(fn ($state) => match ($state) {
                            'easy'   => 'success',
                            'medium' => 'warning',
                            'hard'   => 'danger',
                            default  => 'gray',
                        }),
                ])
                ->columns(5)
                ->columnSpanFull(),

            // ── Ingrediënten ──────────────────────────────────────────────────
            Section::make('Ingrediënten')
                ->icon('Heroicon::OutlinedListBullet')
                ->schema([
                    RepeatableEntry::make('ingredients')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('quantity')
                                ->label('Hoeveelheid')
                                ->getStateUsing(fn ($record) => $record->pivot->quantity)
                                ->placeholder('—')
                                ->columnSpan(1),

                            TextEntry::make('unit')
                                ->label('Eenheid')
                                ->getStateUsing(fn ($record) => $record->pivot->unit)
                                ->placeholder('—')
                                ->columnSpan(1),

                            TextEntry::make('name')
                                ->label('Ingrediënt')
                                ->weight(FontWeight::Medium)
                                ->columnSpan(2),

                            TextEntry::make('pivot_notes')
                                ->label('Opmerking')
                                ->getStateUsing(fn ($record) => $record->pivot->notes)
                                ->placeholder('—')
                                ->color('gray')
                                ->columnSpan(2),
                        ])
                        ->columns(6)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            // ── Bereiding ─────────────────────────────────────────────────────
            Section::make('Bereiding')
                ->icon('Heroicon::OutlinedDocumentText')
                ->schema([
                    RepeatableEntry::make('instructions')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('step')
                                ->hiddenLabel()
                                ->columnSpanFull(),
                        ])
                        ->contained(false)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            // ── Bron ──────────────────────────────────────────────────────────
            Section::make('Bron')
                ->icon('Heroicon::OutlinedLink')
                ->schema([
                    TextEntry::make('source_type')
                        ->label('Type')
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'url'  => 'Website',
                            'scan' => 'Ingescand recept',
                            'boek' => 'Boek / tijdschrift',
                            default => $state,
                        })
                        ->placeholder('—'),

                    TextEntry::make('source_value')
                        ->label('Bron')
                        ->url(fn ($record) => $record->source_type === 'url' ? $record->source_value : null)
                        ->openUrlInNewTab()
                        ->placeholder('—'),
                ])
                ->columns(2)
                ->hidden(fn ($record) => ! $record->source_type)
                ->columnSpanFull(),

            // ── Tags & notities ───────────────────────────────────────────────
            Section::make('Tags & notities')
                ->icon('Heroicon::o-tag')
                ->schema([
                    TextEntry::make('tags.name')
                        ->label('Tags')
                        ->badge()
                        ->separator(','),

                    TextEntry::make('notes')
                        ->label('Notities')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}