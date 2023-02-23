<?php

namespace App\Listeners;

use App\Events\WeatherForecast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaveWeatherData implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\WeatherForecast  $event
     * @return void
     */
    public function handle(WeatherForecast $event) : void
    {
        DB::beginTransaction();

        try {

            $data = \App\Models\WeatherForecast::query()->insertOrIgnore($event->weatherData);

            // log success message
            Log::channel('weather_api')->info("weather data created successfully: ".$data);

            DB::commit();

        }catch (\Exception $exception){
            DB::rollback();

            // log error
            Log::channel('weather_api')->error($exception->getMessage());
        }
    }
}
