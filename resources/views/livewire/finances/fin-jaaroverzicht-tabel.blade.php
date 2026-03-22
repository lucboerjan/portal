<div>
    <x-filament::section>

        {{-- Jaar selector --}}
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
            <span style="font-weight: 600;">Selecteer het jaar voor de samenvatting</span>
            <select wire:model.live="jaar" style="border: 1px solid #d1d5db; border-radius: 6px; padding: 4px 12px; font-size: 0.875rem;">
                @foreach($jaren as $j)
                    <option value="{{ $j }}" {{ $j == $jaar ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
        </div>

        @php
            $maandLabels = [
                1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',
                7=>'07',8=>'08',9=>'09',10=>'10',11=>'11',12=>'12'
            ];

            // Totalen per maand berekenen
            $totaalInkomstenPerMaand = [];
            $totaalUitgavenPerMaand  = [];

            foreach(range(1,12) as $m) {
                $totaalInkomstenPerMaand[$m] = collect($data['inkomsten'])->sum(fn($v) => $v[$m] ?? 0);
                $totaalUitgavenPerMaand[$m]  = collect($data['uitgaven'])->sum(fn($v) => $v[$m] ?? 0);
            }
        @endphp

        {{-- INKOMSTEN --}}
        <table style="border-collapse: collapse; width: 100%; font-size: 0.8rem; margin-bottom: 24px;">
            <thead>
                <tr style="background-color: #0bd146; color: white;">
                    <th style="padding: 8px 12px; text-align: left; font-weight: 700; min-width: 160px;">Inkomsten</th>
                    @foreach($maandLabels as $m => $label)
                        <th style="padding: 8px 12px; text-align: right; white-space: nowrap;">{{ $label }}-{{ $jaar }}</th>
                    @endforeach
                    <th style="padding: 8px 12px; text-align: right; white-space: nowrap;">TOTAAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['inkomsten'] as $categorie => $maandBedragen)
                    @php $totaal = array_sum($maandBedragen); @endphp
                    <tr style="border-bottom: 1px solid #f3f4f6; background-color: {{ $loop->even ? '#f9fafb' : 'white' }};">
                        <td style="padding: 7px 12px; font-weight: 500;">{{ $categorie }}</td>
                        @foreach($maandLabels as $m => $label)
                            <td style="padding: 7px 12px; text-align: right; font-family: monospace; white-space: nowrap;">
                                {{ isset($maandBedragen[$m]) ? number_format($maandBedragen[$m], 2, ',', '.') : '' }}
                            </td>
                        @endforeach
                        <td style="padding: 7px 12px; text-align: right; font-family: monospace; font-weight: 600; white-space: nowrap;">
                            {{ number_format($totaal, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                {{-- Totaalrij inkomsten --}}
                <tr style="background-color: #1e3a5f; color: white; font-weight: 700;">
                    <td style="padding: 8px 12px;">TOTAAL</td>
                    @foreach($maandLabels as $m => $label)
                        <td style="padding: 8px 12px; text-align: right; font-family: monospace; white-space: nowrap;">
                            {{ $totaalInkomstenPerMaand[$m] > 0 ? number_format($totaalInkomstenPerMaand[$m], 2, ',', '.') : '0,00' }}
                        </td>
                    @endforeach
                    <td style="padding: 8px 12px; text-align: right; font-family: monospace; white-space: nowrap;">
                        {{ number_format(array_sum($totaalInkomstenPerMaand), 2, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- UITGAVEN --}}
        <table style="border-collapse: collapse; width: 100%; font-size: 0.8rem; margin-bottom: 24px;">
            <thead>
                <tr style="background-color: #eb3a3a; color: white;">
                    <th style="padding: 8px 12px; text-align: left; font-weight: 700; min-width: 160px;">Uitgaven</th>
                    @foreach($maandLabels as $m => $label)
                        <th style="padding: 8px 12px; text-align: right; white-space: nowrap;">{{ $label }}-{{ $jaar }}</th>
                    @endforeach
                    <th style="padding: 8px 12px; text-align: right; white-space: nowrap;">TOTAAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['uitgaven'] as $categorie => $maandBedragen)
                    @php $totaal = array_sum($maandBedragen); @endphp
                    <tr style="border-bottom: 1px solid #f3f4f6; background-color: {{ $loop->even ? '#f9fafb' : 'white' }};">
                        <td style="padding: 7px 12px; font-weight: 500;">{{ $categorie }}</td>
                        @foreach($maandLabels as $m => $label)
                            <td style="padding: 7px 12px; text-align: right; font-family: monospace; white-space: nowrap;">
                                {{ isset($maandBedragen[$m]) ? number_format($maandBedragen[$m], 2, ',', '.') : '' }}
                            </td>
                        @endforeach
                        <td style="padding: 7px 12px; text-align: right; font-family: monospace; font-weight: 600; white-space: nowrap;">
                            {{ number_format($totaal, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                {{-- Totaalrij uitgaven --}}
                <tr style="background-color: #7f1d1d; color: white; font-weight: 700;">
                    <td style="padding: 8px 12px;">TOTAAL</td>
                    @foreach($maandLabels as $m => $label)
                        <td style="padding: 8px 12px; text-align: right; font-family: monospace; white-space: nowrap;">
                            {{ $totaalUitgavenPerMaand[$m] > 0 ? number_format($totaalUitgavenPerMaand[$m], 2, ',', '.') : '0,00' }}
                        </td>
                    @endforeach
                    <td style="padding: 8px 12px; text-align: right; font-family: monospace; white-space: nowrap;">
                        {{ number_format(array_sum($totaalUitgavenPerMaand), 2, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- RESULTAAT --}}
        <table style="border-collapse: collapse; width: 100%; font-size: 0.8rem;">
            <thead>
                <tr style="background-color: #374151; color: white;">
                    <th style="padding: 8px 12px; text-align: left; font-weight: 700; min-width: 160px;"></th>
                    @foreach($maandLabels as $m => $label)
                        <th style="padding: 8px 12px; text-align: right; white-space: nowrap;">{{ $label }}-{{ $jaar }}</th>
                    @endforeach
                    <th style="padding: 8px 12px; text-align: right; white-space: nowrap;">TOTAAL</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: #f3f4f6; font-weight: 700;">
                    <td style="padding: 8px 12px;">Resultaat</td>
                    @foreach($maandLabels as $m => $label)
                        @php $resultaat = $totaalInkomstenPerMaand[$m] - $totaalUitgavenPerMaand[$m]; @endphp
                        <td style="padding: 8px 12px; text-align: right; font-family: monospace; white-space: nowrap; color: {{ $resultaat >= 0 ? '#16a34a' : '#dc2626' }};">
                            {{ number_format($resultaat, 2, ',', '.') }}
                        </td>
                    @endforeach
                    @php $totaalResultaat = array_sum($totaalInkomstenPerMaand) - array_sum($totaalUitgavenPerMaand); @endphp
                    <td style="padding: 8px 12px; text-align: right; font-family: monospace; font-weight: 700; white-space: nowrap; color: {{ $totaalResultaat >= 0 ? '#16a34a' : '#dc2626' }};">
                        {{ number_format($totaalResultaat, 2, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

    </x-filament::section>
</div>