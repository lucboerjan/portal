<x-filament-panels::page>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ══════════════════════════════════════════════
             LINKS: Gespeelde spellen overzicht
        ══════════════════════════════════════════════ --}}
        <div class="lg:col-span-2">
            <x-filament::section heading="Overzicht van de gespeelde spelletjes">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Datum</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Omschrijving</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Speler 1</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Speler 2</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Speler 3</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Speler 4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->getGameOverview() as $row)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $row['date'] }}</td>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $row['type'] }}</td>

                                    @for ($i = 0; $i < 4; $i++)
                                        @php $p = $row['players'][$i] ?? null @endphp
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">
                                            @if ($p)
                                                {{ $p->name }} ({{ $p->total_score }})
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-400">
                                        Nog geen afgesloten spellen.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        </div>

        {{-- ══════════════════════════════════════════════
             RECHTS: Medaillestand
        ══════════════════════════════════════════════ --}}
        <div class="lg:col-span-1">
            <x-filament::section heading="Medaillestand">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Naam</th>
                                <th class="px-3 py-2 text-center font-semibold text-yellow-500">🥇</th>
                                <th class="px-3 py-2 text-center font-semibold text-gray-400">🥈</th>
                                <th class="px-3 py-2 text-center font-semibold text-amber-600">🥉</th>
                                <th class="px-3 py-2 text-center font-semibold text-gray-600 dark:text-gray-300">Deeln.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->getStandings() as $standing)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                                    <td class="px-3 py-2 font-medium text-gray-700 dark:text-gray-300">
                                        {{ $standing['name'] }}
                                    </td>
                                    <td class="px-3 py-2 text-center font-bold text-yellow-500">{{ $standing['gold'] }}</td>
                                    <td class="px-3 py-2 text-center font-bold text-gray-400">{{ $standing['silver'] }}</td>
                                    <td class="px-3 py-2 text-center font-bold text-amber-600">{{ $standing['bronze'] }}</td>
                                    <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $standing['participations'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-400">
                                        Nog geen gegevens.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        </div>

    </div>

</x-filament-panels::page>