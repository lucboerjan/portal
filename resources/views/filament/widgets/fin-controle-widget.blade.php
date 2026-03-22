<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Controle rekeningsstanden {{ $huidigeMaand }}
        </x-slot>

        <div class="space-y-4">

            {{-- Status banner --}}
            @if($isOk)
                <div class="flex items-center gap-2 p-4 rounded-lg bg-success-50 dark:bg-success-950 text-success-700 dark:text-success-300">
                    <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6" />
                    <span class="font-semibold">Alles klopt! Geen afwijkingen gevonden.</span>
                </div>
            @else
                <div class="flex items-center gap-2 p-4 rounded-lg bg-danger-50 dark:bg-danger-950 text-danger-700 dark:text-danger-300">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-6 h-6" />
                    <span class="font-semibold">⚠️ Afwijking gedetecteerd! Controleer je transacties.</span>
                </div>
            @endif

            {{-- Berekening tabel --}}
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">

                    <tr class="py-2">
                        <td class="py-3 text-gray-500 dark:text-gray-400">
                            Stand einde {{ $vorigeMaand }}
                        </td>
                        <td class="py-3 text-right font-mono font-medium">
                            € {{ number_format($eindeVorigeMaand, 2, ',', '.') }}
                        </td>
                    </tr>

                    <tr>
                        <td class="py-3 text-gray-500 dark:text-gray-400">
                            + Transacties {{ $huidigeMaand }}
                        </td>
                        <td class="py-3 text-right font-mono font-medium
                            {{ $transactiesHuidigeMaand >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                            € {{ number_format($transactiesHuidigeMaand, 2, ',', '.') }}
                        </td>
                    </tr>

                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                        <td class="py-3 font-semibold">
                            = Verwacht saldo
                        </td>
                        <td class="py-3 text-right font-mono font-semibold">
                            € {{ number_format($verwacht, 2, ',', '.') }}
                        </td>
                    </tr>

                    <tr>
                        <td class="py-3 text-gray-500 dark:text-gray-400">
                            Actueel saldo (som rekeningen)
                        </td>
                        <td class="py-3 text-right font-mono font-medium">
                            € {{ number_format($actueleStand, 2, ',', '.') }}
                        </td>
                    </tr>

                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                        <td class="py-3 font-semibold">
                            Verschil
                        </td>
                        <td class="py-3 text-right font-mono font-bold
                            {{ $isOk ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                            € {{ number_format($verschil, 2, ',', '.') }}
                        </td>
                    </tr>

                </tbody>
            </table>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>