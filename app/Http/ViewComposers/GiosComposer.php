<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\SensorRecord;

class GiosComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $sensorCodes = SensorRecord::distinct()->select('name')->get();
        $view->with('sensorCodes', $sensorCodes);
    }
}