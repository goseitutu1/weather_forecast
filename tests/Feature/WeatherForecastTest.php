<?php

namespace Tests\Feature;

use App\Models\WeatherForecast;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WeatherForecastTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_api_returns_successful_weather_data() : void
    {
        $response = $this->getJson('/api/get/weather/forecast?date=2023-01-04');

        $response->assertStatus(200);
    }

    public function test_api_returns_error_message_for_incorrect_date() : void
    {
        $response = $this->getJson('/api/get/weather/forecast?date=');

        $response->assertSee(400);
    }
}
