<?php

namespace App\Filament\Pages\TvGids;

use App\Models\Imdbrating;
use App\Models\Vertoning;
use App\Models\Tvzender;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\Width;

class TvGids extends Page
{

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;
    protected static ?string $navigationLabel = 'TV Gids Invoer';
    protected static ?int $navigationSort = 0;
    protected string $view = 'filament.pages.tv-gids.tv-gids';


    // Nieuwe film velden
    public bool $showNieuweFilm = false;
    public string $nieuweFilmTitel = '';
    public ?int $nieuweFilmJaar = null;
    public string $nieuweFilmUrl = '';
    public float $nieuweFilmRating = 0.0;

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'TV Gids';
    }
    // Datum navigatie
    #[Url]
    public string $datum = '';

    // Formulier velden
    public string $form_datum = '';
    public ?int $tvzender_id = null;
    public ?int $imdbrating_id = null;
    public string $zoekTitel = '';

    // Edit mode
    public ?int $editId = null;

    public function mount(): void
    {
        $this->datum = now()->format('Y-m-d');
        $this->form_datum = $this->datum;
    }

    // Datumnavigatie
    public function vorigeDag(): void
    {
        $this->datum = Carbon::parse($this->datum)->subDay()->format('Y-m-d');
        $this->form_datum = $this->datum;
    }

    public function volgendeDag(): void
    {
        $this->datum = Carbon::parse($this->datum)->addDay()->format('Y-m-d');
        $this->form_datum = $this->datum;
    }

    public function updatedDatum(): void
    {
        $this->form_datum = $this->datum;
    }

    // Tabel data
    public function getVertoningenProperty()
    {
        return vertoning::with(['tvzender', 'imdbrating'])
            ->where('datum', $this->datum)
            ->orderBy('tvzender_id')
            ->get();
    }

    // Select opties
    public function getZendersProperty()
    {
        return Tvzender::orderBy('volgnummer')->pluck('naam', 'id');
    }

    public function getFilmsProperty()
    {
        return Imdbrating::orderBy('titel')
            ->whereNotNull('titel')
            ->when($this->zoekTitel, fn($q) => $q->where('titel', 'like', "%{$this->zoekTitel}%"))
            ->get()
            ->mapWithKeys(fn($film) => [
                $film->id => "{$film->titel} ({$film->jaar}) ★ {$film->imdbrating}"
            ]);
    }

    // Bewaren
    public function bewaren(): void
    {
        $this->validate([
            'form_datum'   => 'required|date',
            'tvzender_id'   => 'required|exists:tvzender,id',
            'imdbrating_id' => 'required|exists:imdbrating,id',
        ]);

        if ($this->editId) {
            vertoning::find($this->editId)->update([
                'datum'        => $this->form_datum,
                'tvzender_id'   => $this->tvzender_id,
                'imdbrating_id' => $this->imdbrating_id,
            ]);

            Notification::make()
                ->title('Vertoning bijgewerkt')
                ->success()
                ->send();
        } else {
            vertoning::create([
                'datum'        => $this->form_datum,
                'tvzender_id'   => $this->tvzender_id,
                'imdbrating_id' => $this->imdbrating_id,
            ]);

            Notification::make()
                ->title('Vertoning bewaard')
                ->success()
                ->send();
        }

        $this->leegmaken();
        $this->datum = $this->form_datum;
    }

    // Leegmaken


    // Bewerken
    public function bewerken(int $id): void
    {
        $vertoning = vertoning::find($id);

        $this->editId       = $id;
        $this->form_datum   = $vertoning->datum;
        $this->tvzender_id   = $vertoning->tvzender_id;
        $this->imdbrating_id = $vertoning->imdbrating_id;
    }

    // Verwijderen
    public function verwijderen(int $id): void
    {
        vertoning::find($id)->delete();

        Notification::make()
            ->title('Vertoning verwijderd')
            ->warning()
            ->send();
    }

    // Filmgids online
    public function filmgidsUrl(): string
    {
        $datum = Carbon::parse($this->datum)->format('Y-m-d');
        return "https://www.filmoptv.be/Broadcasts?onlyHighlights=false";
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    // Toggle nieuw film formulier
    public function toggleNieuweFilm(): void
    {
        $this->showNieuweFilm = !$this->showNieuweFilm;
        $this->nieuweFilmTitel = $this->zoekTitel; // prefill met zoekterm
        $this->nieuweFilmJaar = null;
        $this->nieuweFilmUrl = '';
        $this->nieuweFilmRating = 0.0;
    }

    // Nieuwe film bewaren
    public function nieuweFilmBewaren(): void
    {
        $this->validate([
            'nieuweFilmTitel'  => 'required|string|max:80',
            'nieuweFilmJaar'   => 'nullable|integer|min:1900|max:2099',
            'nieuweFilmUrl'    => 'nullable|url|max:80',
            'nieuweFilmRating' => 'nullable|numeric|min:0|max:10',
        ]);

        $film = Imdbrating::create([
            'titel'      => $this->nieuweFilmTitel,
            'jaar'       => $this->nieuweFilmJaar,
            'imdburl'    => $this->nieuweFilmUrl,
            'imdbrating' => $this->nieuweFilmRating,
        ]);

        // Selecteer de nieuwe film meteen
        $this->imdbrating_id = $film->id;
        $this->zoekTitel = $film->titel;
        $this->showNieuweFilm = false;

        Notification::make()
            ->title("Film '{$film->titel}' toegevoegd")
            ->success()
            ->send();
    }

    // Leegmaken uitbreiden
    public function leegmaken(): void
    {
        $this->tvzender_id      = null;
        $this->imdbrating_id    = null;
        $this->zoekTitel       = '';
        $this->editId          = null;
        $this->showNieuweFilm  = false;
        $this->nieuweFilmTitel = '';
        $this->nieuweFilmJaar  = null;
        $this->nieuweFilmUrl   = '';
        $this->nieuweFilmRating = 0.0;
    }
}
