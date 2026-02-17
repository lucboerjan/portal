<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class EindeLoopbaanBdv extends Widget
{
    protected string $view = 'filament.widgets.einde-loopbaan-bdv';
    protected static null|int $sort = 2;

    protected function getViewData(): array
    {
        return [
            'werkdagenTotPensioen' => $this->berekenWerkdagenTotPensioen(),
        ];
    }

    private function berekenWerkdagenTotPensioen()
    {
        $data = json_decode(File::get(resource_path('data/afwezigheden_bdv.json')), true);

        $feestdagenLijst = $data['feestdagen'];
        $vrijdagenLijst = $data['vrijdagen'];
        $verlofdagenLijst = $data['verlofdagen'];
        $verlofdagenPerJaar = $data['verlofdagen_per_jaar'];

        // Start- en einddatum
        $startDate = Carbon::today();

        $endDate = Carbon::create(2029, 12, 31);

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
        foreach ($verlofdagenPerJaar as $jaar => $verlof) {

            $werkdagenTeller -= $verlof;
        }

        return $werkdagenTeller;
    }
}
