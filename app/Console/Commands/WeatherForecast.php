<?php

namespace App\Console\Commands;

use App\Jobs\WeatherForecastJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class WeatherForecast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:forecast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gives you weather forecast data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() : void
    {
//        // logging
//        Log::channel('weather_command')->info('START COMMAND');
//
//        // logging
//        Log::channel('weather_command')->info("END COMMAND");
    }
}
