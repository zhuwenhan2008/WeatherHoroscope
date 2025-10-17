<?php

namespace RoyZhu\WeatherHoroscope\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use GuzzleHttp\Client;

class WeatherService
{
    protected string $apiKey;
    protected string $dailyWeatherUrl;
    protected string $rainfallUrl;
    protected string $locationName;
    protected string $rainfallLocation;

    public function __construct()
    {
        // 嘗試使用 Laravel Config，如果不存在則使用 function_exists 檢查
        if (class_exists('Illuminate\Support\Facades\Config')) {
            $this->apiKey = Config::get('weather-horoscope.weather.api_key');
            $this->dailyWeatherUrl = Config::get('weather-horoscope.weather.daily_weather_url');
            $this->rainfallUrl = Config::get('weather-horoscope.weather.rainfall_url');
            $this->locationName = Config::get('weather-horoscope.weather.location_name');
            $this->rainfallLocation = Config::get('weather-horoscope.weather.rainfall_location');
        } else {
            // 使用全局 config 函數（測試環境）
            $this->apiKey = config('weather-horoscope.weather.api_key');
            $this->dailyWeatherUrl = config('weather-horoscope.weather.daily_weather_url');
            $this->rainfallUrl = config('weather-horoscope.weather.rainfall_url');
            $this->locationName = config('weather-horoscope.weather.location_name');
            $this->rainfallLocation = config('weather-horoscope.weather.rainfall_location');
        }
    }

    /**
     * 獲取今日天氣資訊
     */
    public function getDailyWeather(): array
    {
        try {
            $url = $this->dailyWeatherUrl . '?Authorization=' . $this->apiKey . '&locationName=' . urlencode($this->locationName);
            
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                return [
                    'error' => '無法獲取天氣資訊',
                    'status' => 'error'
                ];
            }

            $data = $response->json();
            $records = $data['records']['location'][0]['weatherElement'] ?? [];
            
            $weatherData = [
                'location' => $this->locationName,
                'weather_condition' => '',
                'temperature' => [
                    'min' => '',
                    'max' => '',
                    'unit' => '°C'
                ],
                'comfort_index' => '',
                'status' => 'success'
            ];
            
            foreach ($records as $item) {
                switch ($item['elementName']) {
                    case 'Wx':
                        $weatherData['weather_condition'] = $item['time'][0]['parameter']['parameterName'] ?? '';
                        break;
                    case 'CI':
                        $weatherData['comfort_index'] = $item['time'][0]['parameter']['parameterName'] ?? '';
                        break;
                    case 'MinT':
                        $weatherData['temperature']['min'] = $item['time'][0]['parameter']['parameterName'] ?? '';
                        break;
                    case 'MaxT':
                        $weatherData['temperature']['max'] = $item['time'][0]['parameter']['parameterName'] ?? '';
                        break;
                }
            }
            
            return $weatherData;
            
        } catch (\Exception $e) {
            return [
                'error' => '獲取天氣資訊時發生錯誤: ' . $e->getMessage(),
                'status' => 'error'
            ];
        }
    }

    /**
     * 獲取3小時降雨機率
     */
    public function get3HourlyRainfallRate(): array
    {
        try {
            $url = $this->rainfallUrl . '?Authorization=' . $this->apiKey . '&LocationName=' . urlencode($this->rainfallLocation) . '&elementName=WeatherDescription';
            
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                return [
                    'error' => '無法獲取降雨機率資訊',
                    'status' => 'error'
                ];
            }

            $data = $response->json();
            $items = $data['records']['Locations'][0]['Location'][0]['WeatherElement'] ?? [];
            
            $rainfallData = [
                'location' => $this->rainfallLocation,
                'forecasts' => [],
                'status' => 'success'
            ];
            
            $today = Carbon::now()->format('Y-m-d');
            
            foreach ($items as $item) {
                if ($item['ElementName'] === '3小時降雨機率') {
                    foreach ($item['Time'] as $itemTime) {
                        $startTime = Carbon::parse($itemTime['StartTime']);
                        
                        if ($today === $startTime->format('Y-m-d')) {
                            $rainfallData['forecasts'][] = [
                                'time' => $startTime->format('H:i'),
                                'probability' => (int)($itemTime['ElementValue'][0]['ProbabilityOfPrecipitation'] ?? 0),
                                'unit' => '%'
                            ];
                        }
                    }
                }
            }
            
            return $rainfallData;
            
        } catch (\Exception $e) {
            return [
                'error' => '獲取降雨機率時發生錯誤: ' . $e->getMessage(),
                'status' => 'error'
            ];
        }
    }
}
