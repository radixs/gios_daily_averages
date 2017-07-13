<?php

use Illuminate\Database\Seeder;

use App\SensorRecord;
use App\Helpers\GiosApiData;

class MakeRandomSensorRecords extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfRecordsToSeed = 24*30*3;

        foreach (GiosApiData::SENSOR_CODES as $sensorCode) {
            $newRandomSensorRecords = [];
            $insertDateTime = new DateTime();

            for ($i=0; $i<$numberOfRecordsToSeed; $i++) {
                $newRandomSensorRecords[] = [
                    'name'  => $sensorCode,
                    'date'  => $insertDateTime->format('Y-m-d H:i:s'),
                    'value' => rand(1,5000000)/100000
                ];
                $insertDateTime->sub(new DateInterval('PT1H'));
            }

            SensorRecord::insert($newRandomSensorRecords);
        }
    }
}
