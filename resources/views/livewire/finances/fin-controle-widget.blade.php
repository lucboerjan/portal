<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Controle rekeningsstanden {{ $huidigeMaand }}
        </x-slot>

        <div class="space-y-4">

            {{-- Globale status --}}
            @if($allesOk)
                <div class="flex items-center gap-3 p-4 rounded-lg bg-success-50 dark:bg-success-950 border border-success-200 dark:border-success-800">
                    <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-success-500 shrink-0" />
                    <span class="font-semibold text-success-700 dark:text-success-300">Alles klopt! Geen afwijkingen gevonden.</span>
                </div>
            @else
                <div class="flex items-center gap-3 p-4 rounded-lg bg-danger-50 dark:bg-danger-950 border border-danger-200 dark:border-danger-800">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-6 h-6 text-danger-500 shrink-0" />
                    <span class="font-semibold text-danger-700 dark:text-danger-300">Afwijking gedetecteerd! Controleer je transacties.</span>
                </div>
            @endif

            {{-- Twee controles naast elkaar --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                {{-- Controle 1: Normale transacties --}}
                <div class="rounded-lg border {{ $controle1Ok ? 'border-success-200 dark:border-success-800' : 'border-danger-200 dark:border-danger-800' }} overflow-hidden">
                    
                    {{-- Header --}}
                    <div class="flex items-center gap-2 px-4 py-3 {{ $controle1Ok ? 'bg-success-50 dark:bg-success-950' : 'bg-danger-50 dark:bg-danger-950' }}">
                        <x-filament::icon 
                            icon="{{ $controle1Ok ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle' }}" 
                            class="w-5 h-5 {{ $controle1Ok ? 'text-success-500' : 'text-danger-500' }}" 
                        />
                        <span class="font-semibold text-sm {{ $controle1Ok ? 'text-success-700 dark:text-success-300' : 'text-danger-700 dark:text-danger-300' }}">
                            Normale transacties
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="p-4">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr>
                                    <td class="py-2 text-gray-500 dark:text-gray-400">Stand einde {{ $vorigeMaand }}</td>
                                    <td class="py-2 text-right font-mono font-medium">€ {{ number_format($eindeVorigeMaand, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-500 dark:text-gray-400">+ Transacties {{ $huidigeMaand }}</td>
                                    <td class="py-2 text-right font-mono font-medium {{ $transactiesNormaal >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                        € {{ number_format($transactiesNormaal, 2, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-t-2 border-gray-200 dark:border-gray-700">
                                    <td class="py-2 font-semibold">= Verwacht saldo</td>
                                    <td class="py-2 text-right font-mono font-semibold">€ {{ number_format($verwacht, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-500 dark:text-gray-400">Actueel saldo</td>
                                    <td class="py-2 text-right font-mono">€ {{ number_format($actueleStand, 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t-2 border-gray-200 dark:border-gray-700">
                                    <td class="py-2 font-semibold">Verschil</td>
                                    <td class="py-2 text-right font-mono font-bold {{ $controle1Ok ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                        € {{ number_format($verschil1, 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Controle 2: Transfers eigen rekeningen --}}
                <div class="rounded-lg border {{ $controle2Ok ? 'border-success-200 dark:border-success-800' : 'border-danger-200 dark:border-danger-800' }} overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center gap-2 px-4 py-3 {{ $controle2Ok ? 'bg-success-50 dark:bg-success-950' : 'bg-danger-50 dark:bg-danger-950' }}">
                        <x-filament::icon 
                            icon="{{ $controle2Ok ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle' }}" 
                            class="w-5 h-5 {{ $controle2Ok ? 'text-success-500' : 'text-danger-500' }}" 
                        />
                        <span class="font-semibold text-sm {{ $controle2Ok ? 'text-success-700 dark:text-success-300' : 'text-danger-700 dark:text-danger-300' }}">
                            Transfers eigen rekeningen
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="p-4">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <tr>
                                    <td class="py-2 text-gray-500 dark:text-gray-400">Som transfers {{ $huidigeMaand }}</td>
                                    <td class="py-2 text-right font-mono font-medium {{ $controle2Ok ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                        € {{ number_format($transactiesTransfer, 2, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-t-2 border-gray-200 dark:border-gray-700">
                                    <td class="py-2 font-semibold">Verwacht</td>
                                    <td class="py-2 text-right font-mono font-semibold">€ 0,00</td>
                                </tr>
                                <tr>
                                    <td class="py-2 font-semibold">Verschil</td>
                                    <td class="py-2 text-right font-mono font-bold {{ $controle2Ok ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                        € {{ number_format($transactiesTransfer, 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        @if(!$controle2Ok)
                            <div class="mt-3 flex items-start gap-2 text-sm text-danger-600 dark:text-danger-400">
                                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-4 h-4 mt-0.5 shrink-0" />
                                <span>Er ontbreekt een kant van een transfer! Controleer of alle transfers aan beide kanten ingevoerd zijn.</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>