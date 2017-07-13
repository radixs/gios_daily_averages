<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use App\SensorRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiosApiController extends Controller
{
    /**
     * @param Request $request
     */
    public function getSensorData(Request $request)
    {
        if (!empty($request->code)) {

            $currentDate = new DateTime();
            $interval = new DateInterval("P3M");
            $threeMonthsAgoDate = $currentDate->sub($interval);

            $results = SensorRecord::where('date', '>', $threeMonthsAgoDate->format('Y-m-d H:i:s'))
                ->where('name', $request->code)
                ->groupBy('day')
                ->get([
                    DB::raw('DATE(date) as day'),
                    DB::raw('ROUND(AVG(value),5) as averageValue')
                ]);

            echo $results->toJson();
        }
    }
}
