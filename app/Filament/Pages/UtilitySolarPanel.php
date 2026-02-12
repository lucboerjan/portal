<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Section;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\UtilitySolarPanelReading;
use UnitEnum;


class UtilitySolarPanel extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedSun;

  // protected static ?string $navigationGroup = 'Utilities';
   protected static string | UnitEnum | null $navigationGroup = 'Utilities';
   protected static ?int $navigationSort = 100;

   
   protected string $view = 'filament.pages.solar-panel-counter';


    public ?array $data = [];

    public function mount(): void
    {
        $last =UtilitySolarPanelReading::orderBy('date', 'desc')->first();

        $this->form->fill([
            'date' => $last
                ? \Carbon\Carbon::parse($last->date)->addDay()
                : now()->toDateString(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make()
                    ->columns(2)
                    
                    ->schema([
                        DatePicker::make('date')
                            /*                             ->default(function () {
                                $last = \App\Models\SolarPanelCounter::orderBy('date', 'desc')->first();

                                return $last ? \Carbon\Carbon::parse($last->date)->addDay() : now()->toDateString();
                            }) */
                            ->required()


                            ->displayFormat('d/m/Y'),
                        TextInput::make('counter_reading')
                            ->required()
                            ,

                    ])                    
            ])
            ->statePath('data');
            
            
    }

    public function create(): void
    {
        UtilitySolarPanelReading::create($this->form->getState());

        // Nieuwe volgende datum berekenen
        $last = UtilitySolarPanelReading::orderBy('date', 'desc')->first();

        $this->form->fill([
            'date' => $last
                ? \Carbon\Carbon::parse($last->date)->addDay()
                : now()->toDateString(),
            'counter_reading' => null, // optioneel leegmaken
        ]);


        Notification::make()
            ->title('Counter Reading created')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(UtilitySolarPanelReading::query())
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->date('d/m/Y'),
                TextColumn::make('counter_reading'),

                TextColumn::make('dagopbrengst')
                    ->label('Dagopbrengst (kWh)')
                    ->state(function ($record) {
                        $previous = UtilitySolarPanelReading::where('date', '<', $record->date)
                            ->orderBy('date', 'desc')
                            ->first();

                        if (! $previous) {
                            return 0;
                        }

                        return $record->counter_reading - $previous->counter_reading;
                    })
                    ->numeric(2),

            ])
            ->paginated([10, 25, 50, 100])
            ->paginationPageOptions([10, 25, 50, 100])
            
            ->filters([
                // ...
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('sm')   // of 'xs', 'md', 'lg', 'xl', '2xl'

                    ->schema([
                        DatePicker::make('date')->required(),
                        TextInput::make('counter_reading')->required(),
                    ]),

                DeleteAction::make(),
            ]);
    }
}
