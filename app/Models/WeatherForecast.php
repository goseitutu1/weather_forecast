<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeatherForecast extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'weather_forecasts';
    protected $fillable = ['location','temperature','wind_speed','wind_direction','weather_code','date'];
}
