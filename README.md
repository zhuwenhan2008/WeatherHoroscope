# Weather Horoscope Package

一個功能豐富的 Laravel Package，提供天氣資訊和星座運勢功能

## 安裝

1. 將此 package 添加到您的 Laravel 專案中：

```bash
composer require royzhu/weather-horoscope-package
```

2. 發布配置文件（可選）：

```bash
php artisan vendor:publish --provider="RoyZhu\WeatherHoroscope\WeatherHoroscopeServiceProvider" --tag="config"
```

## 使用方法

### 基本使用

```php
use RoyZhu\WeatherHoroscope\WeatherHoroscopePackage;

$package = new WeatherHoroscopePackage();

// 獲取歡迎訊息
echo $package->welcome('張三'); // 輸出: Hello, 張三! 歡迎使用 Weather Horoscope Package!

// 獲取當前時間
echo $package->getCurrentTime(); // 輸出: 2024-01-01 12:00:00

// 簡單計算
echo $package->add(5, 3); // 輸出: 8
```

### 天氣功能

```php
// 獲取今日天氣資訊 (Array 格式)
$weather = $package->getDailyWeather();
// 返回: ['location' => '臺北市', 'weather_condition' => '多雲', 'temperature' => ['min' => '20', 'max' => '25'], ...]

// 獲取3小時降雨機率 (Array 格式)
$rainfall = $package->get3HourlyRainfallRate();
// 返回: ['location' => '信義區', 'forecasts' => [['time' => '09:00', 'probability' => 30], ...], ...]

// 獲取完整的天氣和星座報告 (Array 格式)
$report = $package->getWeatherAndHoroscopeReport();
// 返回: ['timestamp' => '2024-01-01 12:00:00', 'weather' => [...], 'rainfall' => [...], 'horoscope' => [...], ...]

// 格式化為字串 (如果需要)
echo $package->getFormattedReport();
```

### 星座運勢功能

```php
// 獲取今日星座運勢 (Array 格式)
$horoscope = $package->getTodayHoroscope();
// 返回: ['date' => '2024-01-01', 'overall_luck' => ['top_stars' => ['牡羊座'], 'max_score' => 5], ...]

// 格式化為字串 (如果需要)
echo $package->formatHoroscopeAsString($horoscope);
```

### 使用 Laravel 容器

```php
// 在控制器或其他地方使用
$package = app('weather-horoscope');

// 獲取天氣資訊
$weather = $package->getDailyWeather();

// 獲取星座運勢
$horoscope = $package->getTodayHoroscope();

// 獲取完整報告
$report = $package->getWeatherAndHoroscopeReport();
```

### 在控制器中使用

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function index()
    {
        $package = app('weather-horoscope');

        return response()->json([
            'weather' => $package->getDailyWeather(),
            'rainfall' => $package->get3HourlyRainfallRate(),
            'horoscope' => $package->getTodayHoroscope(),
            'full_report' => $package->getWeatherAndHoroscopeReport()
        ]);
    }
}
```

### 配置

您可以在 `.env` 文件中設置以下配置：

```env
# 基本配置
WEATHER_HOROSCOPE_NAME="我的 Weather Horoscope Package"
WEATHER_HOROSCOPE_ENABLED=true
WEATHER_HOROSCOPE_TIMEZONE="Asia/Taipei"

# 天氣 API 配置
CWB_API_KEY="CWB-8E0A6322-CF5A-4DAE-847B-D34467C62686"
WEATHER_LOCATION="臺北市"
RAINFALL_LOCATION="信義區"

# ChatWork 配置 (可選)
CHATWORK_API_TOKEN="your_api_token"
CHATWORK_ROOM_ID="your_room_id"
CHATWORK_ENABLED=false
```

## 功能

### 基本功能

- ✅ 歡迎訊息功能
- ✅ 時間獲取功能
- ✅ 簡單計算功能
- ✅ 配置管理
- ✅ Laravel Service Provider 整合

### 天氣功能

- ✅ 獲取今日天氣資訊（天氣狀況、溫度、舒適度）
- ✅ 獲取 3 小時降雨機率
- ✅ 整合中央氣象局開放資料平台 API
- ✅ 支援自訂地區設定

### 星座運勢功能

- ✅ 獲取 12 星座今日運勢
- ✅ 整體運勢、戀愛運、工作運、財運分析
- ✅ 自動爬取星座運勢網站資料
- ✅ 星級評分系統

### 整合功能

- ✅ 生成完整的天氣和星座報告
- ✅ 支援 ChatWork 訊息格式
- ✅ 錯誤處理和異常管理

## API 依賴

本套件使用以下外部 API：

- 中央氣象局開放資料平台 (CWB Open Data)
- Click108 星座運勢網站

## 升級

請查看 [升級指南](UPGRADE.md) 了解如何升級到新版本。

```bash
# 升級到最新版本
composer update royzhu/weather-horoscope-package

# 升級到特定版本
composer require royzhu/weather-horoscope-package:^2.0
```

## 更新日誌

請查看 [CHANGELOG.md](CHANGELOG.md) 了解詳細的變更記錄。

## 授權

MIT License
