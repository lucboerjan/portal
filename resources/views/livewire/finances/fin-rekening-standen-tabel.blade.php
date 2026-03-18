<div>
    <x-filament::section>
        <x-slot name="heading">
            Rekeningsstanden per maand
        </x-slot>

        <div style="overflow-x: auto;">
            <table style="border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb;">
                        <th style="position: sticky; left: 0; z-index: 10; background: white; padding: 10px 16px; text-align: left; font-weight: 600; white-space: nowrap; min-width: 200px; border-right: 2px solid #e5e7eb;">
                            Rekening
                        </th>
                        @foreach($maanden as $maand)
                            <th style="padding: 10px 16px; text-align: right; font-weight: 600; white-space: nowrap; min-width: 130px;">
                                {{ $maand['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekeningen as $i => $rekening)
                        <tr style="border-bottom: 1px solid #f3f4f6; background-color: {{ $i % 2 === 0 ? 'white' : '#f9fafb' }};">
                            <td style="position: sticky; left: 0; z-index: 10; background-color: {{ $i % 2 === 0 ? 'white' : '#f9fafb' }}; padding: 10px 16px; font-weight: 500; white-space: nowrap; border-right: 2px solid #e5e7eb;">
                                {{ $rekening->omschrijving }}
                            </td>
                            @foreach($maanden as $maand)
                                @php
                                    $key   = $rekening->id . '_' . $maand['jaar'] . '_' . $maand['maand'];
                                    $saldo = $saldi->get($key)?->saldo;
                                @endphp
                                <td style="padding: 10px 16px; text-align: right; font-family: monospace; white-space: nowrap; color: {{ $saldo === null ? '#d1d5db' : ($saldo < 0 ? '#dc2626' : '#111827') }};">
                                    {{ $saldo !== null ? '€ ' . number_format($saldo, 2, ',', '.') : '—' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    {{-- Totaalrij --}}
                    <tr style="border-top: 2px solid #374151; background-color: #f3f4f6;">
                        <td style="position: sticky; left: 0; z-index: 10; background-color: #f3f4f6; padding: 10px 16px; font-weight: 700; white-space: nowrap; border-right: 2px solid #e5e7eb;">
                            Totaal
                        </td>
                        @foreach($maanden as $maand)
                            @php
                                $totaal = $rekeningen->sum(function($rekening) use ($saldi, $maand) {
                                    $key = $rekening->id . '_' . $maand['jaar'] . '_' . $maand['maand'];
                                    return $saldi->get($key)?->saldo ?? 0;
                                });
                            @endphp
                            <td style="padding: 10px 16px; text-align: right; font-family: monospace; font-weight: 700; white-space: nowrap; color: {{ $totaal >= 0 ? '#16a34a' : '#dc2626' }};">
                                € {{ number_format($totaal, 2, ',', '.') }}
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::section>
</div>