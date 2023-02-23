<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WeatherForecast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $weatherData;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($weatherData)
    {
        $this->weatherData = $weatherData;

        // log success message
        Log::channel('weather_api')->info("called WeatherForecast event successfully");
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
//        return new PrivateChannel('channel-name');
    }
}
