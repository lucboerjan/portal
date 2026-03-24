<x-filament-panels::page>
    <div class="flex gap-4">

        {{-- LINKER KOLOM: Tabel --}}
        <div class="flex-1 overflow-x-auto">

            {{-- Datumnavigatie --}}
            <div class="flex items-center gap-2 mb-4">
                <button wire:click="vorigeDag"
                    class="inline-flex items-center px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    ‹
                </button>

                <input type="date"
                    wire:model.live="datum"
                    class="border border-gray-300 rounded-lg px-3 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" />

                <button wire:click="volgendeDag"
                    class="inline-flex items-center px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    ›
                </button>
            </div>

            {{-- Tabel --}}
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2 px-3 font-semibold">Datum</th>
                        <th class="py-2 px-3 font-semibold">Zender</th>
                        <th class="py-2 px-3 font-semibold">Titel</th>
                        <th class="py-2 px-3 font-semibold">Jaar</th>
                        <th class="py-2 px-3 font-semibold">Rating</th>
                        <th class="py-2 px-3 font-semibold">###</th>
                        <th class="py-2 px-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->vertoningen as $v)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="py-2 px-3">
                                {{ \Carbon\Carbon::parse($v->datum)->format('d-m-Y') }}
                            </td>
                            <td class="py-2 px-3">{{ $v->tvzender->naam }}</td>
                            <td class="py-2 px-3">{{ $v->imdbrating->titel }}</td>
                            <td class="py-2 px-3">{{ $v->imdbrating->jaar }}</td>
                            <td class="py-2 px-3">{{ $v->imdbrating->imdbrating }}</td>
                            <td class="py-2 px-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $v->imdbrating->vertoningen()->count() }}
                                </span>
                            </td>
                            <td class="py-2 px-3">
                                <div class="flex gap-1">
                                    {{-- IMDB Link --}}
                                    @if($v->imdbrating->imdburl)
                                        <a href="{{ $v->imdbrating->imdburl }}" target="_blank"
                                            class="inline-flex items-center px-2 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                                            <x-heroicon-o-link class="w-3 h-3" />
                                        </a>
                                    @endif
                                    {{-- Bewerken --}}
                                    <button wire:click="bewerken({{ $v->id }})"
                                        class="inline-flex items-center px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                        <x-heroicon-o-pencil class="w-3 h-3" />
                                    </button>
                                    {{-- Info --}}
                                    <button
                                        class="inline-flex items-center px-2 py-1 bg-gray-500 text-white rounded text-xs hover:bg-gray-600">
                                        <x-heroicon-o-information-circle class="w-3 h-3" />
                                    </button>
                                    {{-- Verwijderen --}}
                                    <button wire:click="verwijderen({{ $v->id }})"
                                        wire:confirm="Ben je zeker dat je deze vertoning wil verwijderen?"
                                        class="inline-flex items-center px-2 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600">
                                        <x-heroicon-o-scissors class="w-3 h-3" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 px-3 text-center text-gray-400">
                                Geen vertoningen gevonden voor deze datum.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- RECHTER KOLOM: Formulier --}}
        <div class="w-80 shrink-0">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-4">

                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Kies een datum om de geprogrammeerde films te bekijken
                </p>

                {{-- Datum --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                        Datum vertoning:
                    </label>
                    <input type="date"
                        wire:model.live="form_datum"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
                </div>

                {{-- Titel zoeken --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                        Titel van de film:
                    </label>
                    <input type="text"
                        wire:model.live="zoekTitel"
                        placeholder="Zoek film..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
                </div>

                {{-- Film select --}}
                <div>
                    <select wire:model="imdbrating_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <option value="">-- Kies een film --</option>
                        @foreach($this->films as $id => $titel)
                            <option value="{{ $id }}">{{ $titel }}</option>
                        @endforeach
                    </select>
                    @error('imdbrating_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Zender --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                        Televisiezender:
                    </label>
                    <select wire:model="tvzender_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <option value="">-- Kies een zender --</option>
                        @foreach($this->zenders as $id => $naam)
                            <option value="{{ $id }}">{{ $naam }}</option>
                        @endforeach
                    </select>
                    @error('tvzender_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Knop: film niet gevonden --}}
                <div>
                    <button wire:click="toggleNieuweFilm"
                        class="w-full inline-flex items-center justify-center gap-1 px-3 py-2 border border-dashed border-gray-400 text-gray-600 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800">
                        <x-heroicon-o-plus-circle class="w-4 h-4" />
                        Film niet gevonden? Toevoegen
                    </button>
                </div>

                {{-- Nieuw film formulier --}}
                @if($showNieuweFilm)
                    <div class="border border-blue-200 dark:border-blue-800 rounded-xl p-3 space-y-3 bg-blue-50 dark:bg-blue-950">

                        <p class="text-xs font-semibold text-blue-700 dark:text-blue-300">
                            Nieuwe film toevoegen aan IMDB lijst
                        </p>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                Titel *
                            </label>
                            <input type="text"
                                wire:model="nieuweFilmTitel"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
                            @error('nieuweFilmTitel')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    Jaar
                                </label>
                                <input type="number"
                                    wire:model="nieuweFilmJaar"
                                    placeholder="2024"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                    IMDB Rating
                                </label>
                                <input type="number"
                                    wire:model="nieuweFilmRating"
                                    step="0.1" min="0" max="10"
                                    placeholder="7.5"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                IMDB URL
                            </label>
                            <input type="text"
                                wire:model="nieuweFilmUrl"
                                placeholder="https://www.imdb.com/title/tt..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
                            @error('nieuweFilmUrl')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <button wire:click="nieuweFilmBewaren"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                                Bewaren
                            </button>
                            <button wire:click="toggleNieuweFilm"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-gray-400 text-white rounded-lg text-sm hover:bg-gray-500">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                                Annuleren
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Bewaren / Leegmaken knoppen --}}
                <div class="flex gap-2">
                    <button wire:click="bewaren"
                        class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        {{ $editId ? 'Bijwerken' : 'Bewaren' }}
                    </button>
                    <button wire:click="leegmaken"
                        class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-yellow-500 text-white rounded-lg text-sm hover:bg-yellow-600">
                        <x-heroicon-o-trash class="w-4 h-4" />
                        Leegmaken
                    </button>
                </div>

                {{-- Zoeken knop --}}
                <a href="{{ $this->filmgidsUrl() }}" target="_blank"
                    class="w-full inline-flex items-center justify-center px-3 py-2 bg-cyan-500 text-white rounded-lg text-sm hover:bg-cyan-600">
                    Filmgids online
                </a>

                {{-- Edit indicator --}}
                @if($editId)
                    <div class="text-xs text-center text-orange-500 font-medium">
                        ✏️ Bezig met bewerken van vertoning #{{ $editId }}
                    </div>
                @endif

            </div>
        </div>

    </div>
</x-filament-panels::page>