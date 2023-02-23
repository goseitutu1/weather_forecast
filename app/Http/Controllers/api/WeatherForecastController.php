<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\WeatherForecast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WeatherForecastController extends Controller
{
    public function getWeatherForecast(Request $request) : object
    {
        // log success message
        Log::channel('weather_api')->info("START");

        $validator = validator::make($request->all(),['date' => 'required|date']);
        if($validator->fails()){

            // log error message
            Log::channel('weather_api')->error($validator->errors()->all());

            return $this->failedResponse($validator->errors()->all());
        }else{
            $date = $request->date;
            $weatherForecast = WeatherForecast::query()->whereDate('date',$date)->get(['location','temperature','wind_speed','wind_direction','weather_code','date']);

            // return weather data if they exist
            if (count($weatherForecast) > 0){

                // log success message
                Log::channel('weather_api')->info("RETURNED FROM DB: $weatherForecast");

                return response()->json([
                    'code' => 200,
                    'data' => $weatherForecast
                ]);
            }
            else{
                // else pull weather data from https://api.open-meteo.com/v1/forecast and return

                // location coordinates
                $locations = [
                    ['location' => 'New York','latitude' => 40.71,'longitude' => -74.01],
                    ['location' => 'London','latitude' => 51.51,'longitude' => -0.13],
                    ['location' => 'Paris','latitude' => 48.85,'longitude' => 2.35],
                    ['location' => 'Berlin','latitude' => 52.52,'longitude' => 13.41],
                    ['location' => 'Tokyo','latitude' => 35.69,'longitude' => 139.69],
                ];

                // variable declaration
                $weatherData = array();

                // this is to get weather data for every location
                for($x = 0;$x < count($locations);$x++){
                    $response = $this->getWeatherData($locations[$x]['latitude'],$locations[$x]['longitude'],$date);

                    if (isset($response->error)){
                        // log error
                        Log::channel('weather_api')->error("API error: ".$response->reason);

                        return $this->failedResponse("No data for the date provided");

                    }else{
                        $data['location'] = $locations[$x]['location'];
                        $data['temperature'] = $response->daily->temperature_2m_max[0];
                        $data['wind_speed'] = $response->daily->windspeed_10m_max[0];
                        $data['wind_direction'] = $response->daily->winddirection_10m_dominant[0];
                        $data['weather_code'] = $response->daily->weathercode[0];
                        $data['date'] = $date;

                        // pushing $data array to $weatherData array
                        $weatherData[] = $data;
                    }
                }

                // fire an event that saves weather data into weather_forecasts table
                event(new \App\Events\WeatherForecast($weatherData));

                $response =  response()->json([
                    'code' => 200,
                    'data' => $weatherData
                ]);

                // log success message
                Log::channel('weather_api')->info("RETURNED FROM API: ".$response);

                // return weather data collection
                return $response;
            }
        }

    }

    // this function makes a get request to weather api and return the response object
    private function getWeatherData($latitude,$longitude,$date) : object
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.open-meteo.com/v1/forecast?latitude=$latitude&longitude=$longitude&daily=temperature_2m_max,windspeed_10m_max,winddirection_10m_dominant,weathercode&timezone=GMT&start_date=$date&end_date=$date",
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
