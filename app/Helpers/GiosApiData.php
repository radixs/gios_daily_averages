<?php

namespace App\Helpers;

use DateTime;
use App\SensorRecord;
use GuzzleHttp\Client;

class GiosApiData
{

    const STATION_API_PATH = 'http://api.gios.gov.pl/pjp-api/rest/station/sensors/14';
    const SENSOR_API_PATH  = 'http://api.gios.gov.pl/pjp-api/rest/data/getData/';
    const SENSOR_CODES     = ['NO2', 'SO2'];


    /**
     * Required to make requests to the API.
     *
     * @var Client
     */
    protected $httpClient;


    /**
     * GiosApiData constructor.
     *
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }


    /**
     * Carry out the process of updating the database with data fetched from the API.
     */
    public function updateFromAPI()
    {
        $stationData = json_decode($this->fetchData(self::STATION_API_PATH));
        $sensorIds = $this->getSensorIds($stationData, self::SENSOR_CODES);

        foreach ($sensorIds as $sensorCode => $sensorId) {
            $sensorData = json_decode($this->fetchData(self::SENSOR_API_PATH.$sensorId));
            $this->saveNewSensorRecords($sensorData, $sensorCode);
        }
    }


    /**
     * Store new sensor records into the database.
     *
     * @param \stdClass $sensorData
     * @param string $sensorCode
     */
    private function saveNewSensorRecords($sensorData, $sensorCode)
    {
        $lastSensorRecordDate = $this->getLastSensorRecorDate($sensorCode);
        $insertRecords = [];

        if (!empty($sensorData->values) && is_array($sensorData->values)) {
            foreach ($sensorData->values as $receivedSensorData) {
                if (!empty($receivedSensorData->date) && !empty($receivedSensorData->value)) {

                    $recordingDate = new DateTime($receivedSensorData->date);

                    //current recording date must be later than then last recorder date, or there is no last recorded date
                    if (($lastSensorRecordDate instanceof DateTime && $lastSensorRecordDate < $recordingDate) || is_null($lastSensorRecordDate)) {
                        $insertRecords[] = [
                            'name'  => $sensorCode,
                            'date'  => $receivedSensorData->date,
                            'value' => $receivedSensorData->value
                        ];
                    }
                }
            }
        }

        if (!empty($insertRecords)) {
            SensorRecord::insert($insertRecords);
        }
    }


    /**
     * Get the date of the last record made by the sensor.
     *
     * @param $sensorCode
     * @return null|string
     */
    private function getLastSensorRecorDate($sensorCode)
    {
        $lastSensorRecord = SensorRecord::select('date')->where('name', $sensorCode)->orderBy('date', 'desc')->first();
        return $lastSensorRecord ? new DateTime($lastSensorRecord->date) : null;
    }


    /**
     * Fetch the data from the API.
     *
     * @param string $url The url to get data from.
     * @return string
     */
    private function fetchData($url)
    {
        $result = $this->httpClient->get($url);
        return $result->getBody()->getContents();
    }


    /**
     * Extract from the station data the id's of the sensors by their codes.
     *
     * @param array $stationData List of sensors for a given station.
     * @param array $sensorCodes Sensor codes for which to get the id's.
     * @return array
     */
    private function getSensorIds(array $stationData, array $sensorCodes)
    {
        $sensorKeys = [];
        //evaluate each sensor the station has
        foreach ($stationData as $stationSensor) {
            //the sensor must have the required parameters and it's code must match one of the codes provided in the list
            if (!empty($stationSensor->param->paramCode) && !empty($stationSensor->id) && in_array($stationSensor->param->paramCode, $sensorCodes)) {
                $sensorKeys[$stationSensor->param->paramCode] = $stationSensor->id;
            }
        }
        return $sensorKeys;
    }
}