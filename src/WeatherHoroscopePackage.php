<?php

namespace RoyZhu\WeatherHoroscope;

use RoyZhu\WeatherHoroscope\Services\WeatherService;
use RoyZhu\WeatherHoroscope\Services\HoroscopeService;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class WeatherHoroscopePackage
{
    protected ?WeatherService $weatherService;
    protected ?HoroscopeService $horoscopeService;

    public function __construct(?WeatherService $weatherService = null, ?HoroscopeService $horoscopeService = null)
    {
        $this->weatherService = $weatherService;
        $this->horoscopeService = $horoscopeService;
    }

    /**
     * 獲取歡迎訊息
     */
    public function welcome(string $name = 'World'): string
    {
        return "Hello, {$name}! 歡迎使用 Simple Package!";
    }

    /**
     * 獲取當前時間
     */
    public function getCurrentTime(): string
    {
        return Carbon::now()->format('Y-m-d H:i:s');
    }

    /**
     * 簡單的計算功能
     */
    public function add(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * 獲取配置值
     */
    public function getConfig(string $key, $default = null)
    {
        return Config::get("simple-package.{$key}", $default);
    }

    /**
     * 獲取今日天氣資訊
     */
    public function getDailyWeather(): array
    {
        if (!$this->weatherService) {
            return ['error' => 'WeatherService not available', 'status' => 'error'];
        }
        return $this->weatherService->getDailyWeather();
    }

    /**
     * 獲取3小時降雨機率
     */
    public function get3HourlyRainfallRate(): array
    {
        if (!$this->weatherService) {
            return ['error' => 'WeatherService not available', 'status' => 'error'];
        }
        return $this->weatherService->get3HourlyRainfallRate();
    }

    /**
     * 獲取今日星座運勢
     */
    public function getTodayHoroscope(): array
    {
        if (!$this->horoscopeService) {
            return ['error' => 'HoroscopeService not available', 'status' => 'error'];
        }
        return $this->horoscopeService->getTodayHoroscope();
    }

    /**
     * 獲取完整的天氣和星座資訊
     */
    public function getWeatherAndHoroscopeReport(): array
    {
        $weather = $this->getDailyWeather();
        $rainfall = $this->get3HourlyRainfallRate();
        $horoscope = $this->getTodayHoroscope();

        return [
            'timestamp' => $this->getCurrentTime(),
            'weather' => $weather,
            'rainfall' => $rainfall,
            'horoscope' => $horoscope,
            'status' => 'success'
        ];
    }

    /**
     * 格式化天氣資訊為字串
     */
    public function formatWeatherAsString(array $weather): string
    {
        if ($weather['status'] === 'error') {
            return $weather['error'] ?? '天氣資訊獲取失敗';
        }

        $output = "地點: {$weather['location']}\n";
        $output .= "天氣狀況: {$weather['weather_condition']}\n";
        $output .= "溫度: {$weather['temperature']['min']} ~ {$weather['temperature']['max']} {$weather['temperature']['unit']}\n";
        $output .= "舒適度: {$weather['comfort_index']}";

        return $output;
    }

    /**
     * 格式化降雨機率為字串
     */
    public function formatRainfallAsString(array $rainfall): string
    {
        if ($rainfall['status'] === 'error') {
            return $rainfall['error'] ?? '降雨機率資訊獲取失敗';
        }

        $output = "地點: {$rainfall['location']}\n";
        $output .= "3小時降雨機率:\n";
        
        foreach ($rainfall['forecasts'] as $forecast) {
            $output .= "{$forecast['time']}: {$forecast['probability']}{$forecast['unit']}\n";
        }

        return rtrim($output);
    }

    /**
     * 格式化星座運勢為字串
     */
    public function formatHoroscopeAsString(array $horoscope): string
    {
        if ($horoscope['status'] === 'error') {
            return $horoscope['error'] ?? '星座運勢資訊獲取失敗';
        }

        $output = "日期: {$horoscope['date']}\n";
        $output .= "整體運勢最高: " . implode(', ', $horoscope['overall_luck']['top_stars']) . "\n";
        $output .= "戀愛運最高: " . implode(', ', $horoscope['love_luck']['top_stars']) . "\n";
        $output .= "工作運最高: " . implode(', ', $horoscope['work_luck']['top_stars']) . "\n";
        $output .= "財運最高: " . implode(', ', $horoscope['money_luck']['top_stars']);

        return $output;
    }

    /**
     * 獲取格式化的完整報告字串
     */
    public function getFormattedReport(): string
    {
        $weather = $this->getDailyWeather();
        $rainfall = $this->get3HourlyRainfallRate();
        $horoscope = $this->getTodayHoroscope();

        $report = "[info][title]今日天氣 🌈️[/title]\n";
        $report .= $this->formatWeatherAsString($weather) . "\n";
        $report .= "-----------------\n";
        $report .= "降雨機率 🌧\n";
        $report .= $this->formatRainfallAsString($rainfall) . "\n";
        $report .= "[/info]\n\n";

        $report .= "[info][title]今日星座運勢 🌟[/title]\n";
        $report .= $this->formatHoroscopeAsString($horoscope) . "\n";
        $report .= "[/info]";

        return $report;
    }

    /**
     * 獲取包版本信息
     */
    public function getVersion(): array
    {
        $composerFile = __DIR__ . '/../composer.json';
        
        if (file_exists($composerFile)) {
            $composer = json_decode(file_get_contents($composerFile), true);
            return [
                'version' => $composer['version'] ?? 'unknown',
                'name' => $composer['name'] ?? 'royzhu/weather-horoscope-package',
                'description' => $composer['description'] ?? '',
                'status' => 'success'
            ];
        }
        
        return [
            'version' => 'unknown',
            'name' => 'royzhu/weather-horoscope-package',
            'description' => 'Laravel Weather Horoscope Package',
            'status' => 'error',
            'error' => 'Cannot read composer.json'
        ];
    }

    /**
     * 檢查更新
     */
    public function checkForUpdates(): array
    {
        $currentVersion = $this->getVersion();
        
        if ($currentVersion['status'] === 'error') {
            return $currentVersion;
        }
        
        // 這裡可以添加檢查 Packagist API 的邏輯
        // 目前返回基本信息
        return [
            'current_version' => $currentVersion['version'],
            'latest_version' => 'unknown',
            'update_available' => false,
            'update_command' => 'composer update royzhu/weather-horoscope-package',
            'status' => 'success'
        ];
    }
}
