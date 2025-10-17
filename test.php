<?php

/**
 * Weather Horoscope Package 簡單測試
 * 
 * 用於快速測試包的基本功能
 * 在實際 Laravel 應用中請使用: app('weather-horoscope')
 */

require_once __DIR__ . '/vendor/autoload.php';

// 模擬 Laravel 的 config() 函數
if (!function_exists('config')) {
    function config($key, $default = null) {
        // 簡單的配置模擬
        $config = [
            'weather-horoscope.weather.api_key' => 'CWB-8E0A6322-CF5A-4DAE-847B-D34467C62686',
            'weather-horoscope.weather.daily_weather_url' => 'https://opendata.cwa.gov.tw/api/v1/rest/datastore/F-C0032-001',
            'weather-horoscope.weather.rainfall_url' => 'https://opendata.cwa.gov.tw/api/v1/rest/datastore/F-D0047-061',
            'weather-horoscope.weather.location_name' => '臺北市',
            'weather-horoscope.weather.rainfall_location' => '信義區',
            'weather-horoscope.horoscope.base_url' => 'https://astro.click108.com.tw/daily_10.php',
            'weather-horoscope.horoscope.stars' => [
                '1' => '魔羯座', '2' => '水瓶座', '3' => '雙魚座', '4' => '牡羊座',
                '5' => '金牛座', '6' => '雙子座', '7' => '巨蟹座', '8' => '獅子座',
                '9' => '處女座', '10' => '天秤座', '11' => '天蠍座', '12' => '射手座',
            ],
        ];
        
        return $config[$key] ?? $default;
    }
}

use RoyZhu\WeatherHoroscope\WeatherHoroscopePackage;

echo "=== Weather Horoscope Package 測試 ===\n\n";

try {
    // 只測試基本功能，不創建需要 HTTP 的服務
    $package = new WeatherHoroscopePackage(null, null);

    // 測試基本功能
    echo "✅ 歡迎訊息: " . $package->welcome('測試用戶') . "\n";
    echo "✅ 當前時間: " . $package->getCurrentTime() . "\n";
    echo "✅ 計算功能: 5 + 3 = " . $package->add(5, 3) . "\n\n";

    // 測試新的 array 格式
    echo "📊 測試 Array 格式輸出:\n";
    $weatherData = $package->getDailyWeather();
    echo "天氣資料結構: " . json_encode($weatherData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

    $horoscopeData = $package->getTodayHoroscope();
    echo "星座運勢資料結構: " . json_encode($horoscopeData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

    $fullReport = $package->getWeatherAndHoroscopeReport();
    echo "完整報告資料結構: " . json_encode($fullReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

    echo "⚠️  注意：天氣和星座功能需要完整的 Laravel 環境\n";
    echo "⚠️  在實際 Laravel 應用中請使用: app('weather-horoscope')\n\n";

    echo "=== 測試完成 ===\n";

} catch (Exception $e) {
    echo "❌ 錯誤: " . $e->getMessage() . "\n";
}
