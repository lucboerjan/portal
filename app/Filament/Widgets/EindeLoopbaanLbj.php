<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class EindeLoopbaanLbj extends Widget
{
    protected string $view = 'filament.widgets.einde-loopbaan-lbj';
    protected static null|int $sort = 1;


    protected function getViewData(): array
    {
        return [
            'werkdagenTotPensioen' => $this->berekenWerkdagenTotPensioen(),
        ];
    }

    private function berekenWerkdagenTotPensioen()
    {
        $data = json_decode(File::get(resource_path('data/afwezigheden_lbj.json')), true);

        $feestdagenLijst = $data['feestdagen'];
        $vrijdagenLijst = $data['vrijdagen'];
        $verlofdagenLijst = $data['verlofdagen'];
        $verlofdagenPerJaar = $data['verlofdagen_per_jaar'];

        // Start- en einddatum
        $startDate = Carbon::today();

        $endDate = Carbon::create(2026, 12, 31);

        $werkdagenTeller = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $isWerkdag = False;
            // Check of het een werkdag is (maandag t/m donderdag)
            if ($currentDate->format('N') <= 4) {
                // Check of het geen feestdag of verlofdag is
                if (
                    !in_array($currentDate->format('Y-m-d'), $feestdagenLijst) &&
                    !in_array($currentDate->format('Y-m-d'), $verlofdagenLijst)
                ) {
                    $werkdagenTeller++;
                    $isWerkdag = True;
                }
            }
            if ($currentDate->format('N') == 5) {

                if (in_array($currentDate->format('Y-m-d'), $vrijdagenLijst)) {
                    $isWerkdag = True;
                    $werkdagenTeller++;
                }
            }

            $message = ($isWerkdag == true) ? ' is een werkdag' : ' is GEEN werkdag';
            //Log::info($currentDate->format('Y-m-d') . $message);


            $currentDate->addDay();
        }
        // Trek geplande verlofdagen per jaar af
        foreach ($verlofdagenPerJaar as $verlof) {
            $werkdagenTeller -= $verlof;
        }

        return $werkdagenTeller;
    }
}
