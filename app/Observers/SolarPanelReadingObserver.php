<?php

namespace App\Observers;

use App\Models\UtilitySolarPanelReading;
use App\Models\UtilityReading;
use App\Models\UtilityType;
use Illuminate\Support\Facades\Log;

class SolarPanelReadingObserver
{
 protected int $utilityTypeId;

    public function __construct()
    {
        $this->utilityTypeId = UtilityType::where('name', 'Zonnepanelen')->value('id');
        
    }


    public function created(UtilitySolarPanelReading $reading)
    {
        if ($reading->date instanceof \DateTime) {
            $month = $reading->date->format('m');
            $year = $reading->date->format('Y');
        } else {
            // Handle the case when $reading->date is not a valid DateTime object
        }


        // Zoek maandrecord op basis van reading_date
        $monthly = UtilityReading::where('utility_type_id', $this->utilityTypeId)
            ->whereYear('reading_date', $year)
            ->whereMonth('reading_date', $month)
            ->first();

        // Bestaat het record niet? Maak het aan
        if (!$monthly) {
            $monthly = new UtilityReading();
            $monthly->utility_type_id = $this->utilityTypeId;
            $monthly->reading_date = $reading->date;
        }

        // Datum moet hoogste datum van de maand zijn
        if ($reading->date > $monthly->reading_date) {
            $monthly->reading_date = $reading->date;
        }

        // Tellerstand zonnepanelen bijwerken
        $monthly->meter_stand = $reading->counter_reading;
        $monthly->save();
    }


    public function updated(UtilitySolarPanelReading $reading)
    {

        if ($reading->date instanceof \DateTime) {
            $month = $reading->date->format('m');
            $year = $reading->date->format('Y');
    
        } else {
            // Handle the case when $reading->date is not a valid DateTime object
        }

        // Zoek het maandrecord
        $monthly = UtilityReading::where('utility_type_id', $this->utilityTypeId)
            ->whereYear('reading_date', $year)
            ->whereMonth('reading_date', $month)
            ->first();

        // Datum moet altijd de hoogste datum van die maand zijn
        // We moeten dus opnieuw checken of dit de hoogste is
        $highestDaily = UtilitySolarPanelReading::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderByDesc('date')
            ->first();

        if ($highestDaily) {
            $monthly->reading_date = $highestDaily->date;
            $monthly->meter_stand       = $highestDaily->counter_reading;
        }
         $monthly->save();
    }

    Public function deleted(UtilitySolarPanelReading $reading)
    {
        if ($reading->date instanceof \DateTime) {
            $month = $reading->date->format('m');
            $year = $reading->date->format('Y');
        } else {
            // Handle the case when $reading->date is not a valid DateTime object
        }

        // Zoek het maandrecord
        $monthly = UtilityReading::where('utility_type_id', $this->utilityTypeId)
            ->whereYear('reading_date', $year)
            ->whereMonth('reading_date', $month)
            ->first();

        // Na verwijderen moeten we opnieuw de hoogste datum van die maand bepalen
        $highestDaily = UtilitySolarPanelReading::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderByDesc('date')
            ->first();

        if ($highestDaily) {
            $monthly->reading_date = $highestDaily->date;
            $monthly->meter_stand       = $highestDaily->counter_reading;
        } else {
            // Geen dagelijkse readings meer, reset maandrecord
            $monthly->reading_date = null;
            $monthly->meter_stand       = null;
        }

        $monthly->save();
    }



}
