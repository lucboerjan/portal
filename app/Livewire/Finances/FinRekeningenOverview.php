<?php

namespace App\Livewire\Finances;

use App\Models\FinRekening;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinRekeningenOverview extends TableWidget
{
    protected static ?string $heading = 'Rekeningen saldi';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FinRekening::query()
                    ->where('actief', true)
                    ->orderBy('order')
            )
            ->columns([
                TextColumn::make('omschrijving')
                    ->label('Rekening')
                    ->searchable(),

                TextColumn::make('rekening_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => match($state->value) {
                        'zichtrekening'         => 'info',
                        'spaarrekening'         => 'success',
                        'kredietkaart'          => 'warning',
                        'cash'                  => 'gray',
                        'beleggingsrekening'    => 'primary',
                        'pensioenspaarrekening' => 'danger',
                    }),

                TextColumn::make('saldo')
                    ->label('Manueel saldo')
                    ->money('EUR')
                    ->alignEnd(),

                TextColumn::make('berekend_saldo')
                    ->label('Berekend saldo')
                    ->money('EUR')
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                    ->alignEnd(),

                TextColumn::make('verschil_saldo')
                    ->label('Verschil')
                    ->money('EUR')
                    ->color(fn($state) => abs($state) < 0.01 ? 'success' : 'warning')
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}