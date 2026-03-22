@use('Illuminate\Support\Js')

<x-filament-panels::page>

    {{-- Subtitel --}}
    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
        {{ $this->record->gameType->name }} &mdash; {{ $this->record->played_at->format('d-m-Y') }}
        @if ($this->record->description)
            &mdash; {{ $this->record->description }}
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ══════════════════════════════════════════════
             LINKS: Speler kaarten
        ══════════════════════════════════════════════ --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="grid grid-cols-2 gap-3">
                @foreach ($this->getRanking() as $player)
                    @php
                        $style = match ($player->rank) {
                            1 => 'bg-yellow-400 text-yellow-900',
                            2 => 'bg-gray-300 text-gray-800',
                            3 => 'bg-amber-600 text-amber-50',
                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
                        };
                    @endphp
                    <div class="rounded-xl p-4 text-center {{ $style }} shadow">
                        <div class="text-base font-bold">{{ $player->name }}</div>
                        <div class="text-3xl font-extrabold mt-1">{{ $player->total_score }}</div>
                    </div>
                @endforeach
            </div>

            <div class="text-center">
                @if ($this->record->status === 'finished')
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-700">
                        ✅ Spel afgelopen
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-700">
                        🎮 Bezig...
                    </span>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             RECHTS: Rondes tabel
        ══════════════════════════════════════════════ --}}
        <div class="lg:col-span-2">

            <div class="mb-3">
                <span class="text-base font-semibold text-gray-700 dark:text-gray-200">
                    We spelen {{ $this->record->gameType->name }} met {{ $this->record->players->count() }} spelers
                </span>
            </div>

            <div
                class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Spel#</th>
                            @foreach ($this->getPlayers() as $player)
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">
                                    {{ $player->name }}
                                </th>
                            @endforeach
                            @if ($this->record->status === 'active')
                                <th class="px-4 py-3"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->getRounds() as $round)
                            <tr
                                class="border-b border-gray-100 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                                <td class="px-4 py-2 text-center font-medium text-gray-700 dark:text-gray-300">
                                    {{ $round->round_number }}
                                </td>

                                @foreach ($this->getPlayers() as $player)
                                    @php
                                        $score = $round->scores->firstWhere('game_player_id', $player->id);
                                        $value = $score ? $score->score : '—';
                                        $isLowest = $score && $score->score === $round->scores->min('score');
                                    @endphp
                                    <td
                                        class="px-4 py-2 text-center {{ $isLowest ? 'font-bold text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                                        {{ $value }}
                                    </td>
                                @endforeach

                                @if ($this->record->status === 'active')
                                    <td class="px-4 py-2">
                                        <div class="flex justify-end gap-1">
                                            <x-filament::icon-button icon="heroicon-m-pencil-square" color="primary"
                                                tooltip="Bewerken"
                                                x-on:click="$wire.mountAction('editRound', {{ json_encode(['roundId' => $round->id]) }})" />

                                            <x-filament::icon-button icon="heroicon-m-scissors" color="warning"
                                                tooltip="Verwijderen"
                                                x-on:click="$wire.mountAction('deleteRound', {{ json_encode(['roundId' => $round->id]) }})" />
                                        </div>
                                    </td>
                                @endif

                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $this->getPlayers()->count() + 2 }}"
                                    class="px-4 py-8 text-center text-gray-400">
                                    Nog geen rondes. Gebruik "Spelronde toevoegen" bovenaan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if ($this->getRounds()->count() > 0)
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800">
                                <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-200">Totaal</td>
                                @foreach ($this->getPlayers() as $player)
                                    @php
                                        $total = $this->getRanking()->firstWhere('id', $player->id)?->total_score ?? 0;
                                        $isLeader = $this->getRanking()->first()?->id === $player->id;
                                    @endphp
                                    <td
                                        class="px-4 py-3 text-center font-bold {{ $isLeader ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-700 dark:text-gray-200' }}">
                                        {{ $total }}
                                    </td>
                                @endforeach
                                @if ($this->record->status === 'active')
                                    <td></td>
                                @endif
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Filament v5: modals worden automatisch gerenderd via de actions --}}
    <x-filament-actions::modals />

</x-filament-panels::page>
