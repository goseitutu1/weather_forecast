<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class WeatherForecastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() : void
    {
        // location coordinates
        $locations = [
            ['location' => 'New York','latitude' => 40.71,'longitude' => -74.01],
            ['location' => 'London','latitude' => 51.51,'longitude' => -0.13],
            ['location' => 'Paris','latitude' => 48.85,'longitude' => 2.35],
            ['location' => 'Berlin','latitude' => 52.52,'longitude' => 13.41],
            ['location' => 'Tokyo','latitude' => 35.69,'longitude' => 139.69],
        ];

        // logging
        Log::channel('weather_job')->info('START JOB');

        // this is to get weather data for every location
        for($x = 0;$x < count($locations);$x++){
            $response = $this->getWeatherData($locations[$x]['latitude'],$locations[$x]['longitude']);
            $date = date('Y-m-d');

            if (isset($response->error)){
                // log error
                Log::channel('weather_job')->error("API error: ".$response->reason);

            }else{
                $data['location'] = $locations[$x]['location'];
                $data['temperature'] = $response->current_weather->temperature;
                $data['wind_speed'] = $response->current_weather->windspeed;
                $data['wind_direction'] = $response->current_weather->winddirection;
                $data['weather_code'] = $response->current_weather->weathercode;
                $data['date'] = $date;

                DB::beginTransaction();

                try {
                    $weatherForecast = \App\Models\WeatherForecast::query()->whereDate('date',$date)->where('location',$data['location'])->first();

                    if(!empty($weatherForecast)){
                        // update existing weather forecast
                        $weatherForecast->update($this->dbDumb($data));

                        // log success message
                        Log::channel('weather_job')->info("$weatherForecast updated successfully");
                    }else{
                        // insert weather forecast
                        $weatherForecast = \App\Models\WeatherForecast::create($this->dbDumb($data));

                        // log success message
                        Log::channel('weather_job')->info("$weatherForecast created successfully");

                    }

                    DB::commit();

                }catch (\Exception $exception){
                    DB::rollback();

                    // log error
                    Log::channel('weather_job')->error($exception->getMessage());
                }
            }
        }

        // logging
        Log::channel('weather_job')->info("END JOB");
    }

    // this function returns database dumb array
    #[ArrayShape(['location' => "mixed", 'temperature' => "mixed", 'wind_speed' => "mixed", 'wind_direction' => "mixed", 'weather_code' => "mixed", 'date' => "mixed"])] private function dbDumb($data) : array
    {
        return [
            'location' => $data['location'],
            'temperature' => $data['temperature'],
            'wind_speed' => $data['wind_speed'],
            'wind_direction' => $data['wind_direction'],
            'weather_code' => $data['weather_code'],
            'date' => $data['date'],
        ];
    }

    // this function makes a get request to weather api and return the response object
    private function getWeatherData($latitude,$longitude) : object
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&current_weather=true",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $curlResponse = curl_exec($curl);
        curl_close($curl);

        return json_decode($curlResponse);
    }
}
