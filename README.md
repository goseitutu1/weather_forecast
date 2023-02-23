Notes: 
1. In order to use the application's api, pass date to url like so "APP_URL/api/get/weather/forecast?date=2023-02-01" or you can make a get request by passing a correct DATE to the date parameter
2. app/Jobs/WeatherForecastJob.php is scheduled to execute 4 times daily to get weather data from a third party api on daily bases.
3. Weather data from the api is either updated if record for the current date already exists or created if not. This is done by app/Jobs/WeatherForecastJob.php.
4. In order to prevent client request delay. app/Events/WeatherForecastEvent.php is used to store weather data if it does not exist in the database when a client make a get request to the api.
5. There is also an automated tests/Feature/WeatherForecastTest.php 
6. application uses https://api.open-meteo.com/v1/forecast third party api to pull weather data.
