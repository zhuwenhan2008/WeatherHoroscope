# Weather Horoscope Package 安裝指南

## 快速安裝

### 1. 在 Laravel 專案中安裝

```bash
# 方法一：本地開發安裝
# 在 composer.json 中添加：
{
    "repositories": [
        {
            "type": "path",
            "url": "/Users/sai/Sites/test/my-laravel-package"
        }
    ],
    "require": {
        "royzhu/weather-horoscope-package": "*"
    }
}

# 然後執行：
composer install

# 方法二：如果已發布到 Packagist
composer require royzhu/weather-horoscope-package
```

### 2. 發布配置文件

```bash
php artisan vendor:publish --provider="RoyZhu\WeatherHoroscope\WeatherHoroscopeServiceProvider" --tag="config"
```

### 3. 配置環境變量

在 `.env` 文件中添加：

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

## 使用範例

### 控制器範例

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

    public function dashboard()
    {
        $package = app('weather-horoscope');

        return view('weather.dashboard', [
            'weather' => $package->getDailyWeather(),
            'horoscope' => $package->getTodayHoroscope(),
            'time' => $package->getCurrentTime()
        ]);
    }
}
```

### Blade 模板範例

```php
{{-- resources/views/weather/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ app('weather-horoscope')->welcome('訪客') }}</h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">天氣資訊</div>
                <div class="card-body">
                    <pre>{{ $weather }}</pre>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">星座運勢</div>
                <div class="card-body">
                    <pre>{{ $horoscope }}</pre>
                </div>
            </div>
        </div>
    </div>

    <p class="text-muted">更新時間：{{ $time }}</p>
</div>
@endsection
```

### Artisan 命令範例

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WeatherReport extends Command
{
    protected $signature = 'weather:report {--format=text : 輸出格式 (text|json)}';
    protected $description = '生成天氣和星座運勢報告';

    public function handle()
    {
        $package = app('weather-horoscope');

        $this->info('=== 天氣和星座運勢報告 ===');

        if ($this->option('format') === 'json') {
            $this->line(json_encode([
                'weather' => $package->getDailyWeather(),
                'rainfall' => $package->get3HourlyRainfallRate(),
                'horoscope' => $package->getTodayHoroscope(),
                'timestamp' => $package->getCurrentTime()
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->line($package->getWeatherAndHoroscopeReport());
        }
    }
}
```

### 路由範例

```php
// routes/web.php
Route::get('/weather', [App\Http\Controllers\WeatherController::class, 'dashboard']);
Route::get('/api/weather', [App\Http\Controllers\WeatherController::class, 'index']);

// routes/api.php
Route::get('/weather-report', function () {
    $package = app('weather-horoscope');
    return response()->json([
        'weather' => $package->getDailyWeather(),
        'horoscope' => $package->getTodayHoroscope(),
        'timestamp' => $package->getCurrentTime()
    ]);
});
```

## 測試

創建測試路由來驗證安裝：

```php
// routes/web.php
Route::get('/test-package', function () {
    $package = app('weather-horoscope');

    return response()->json([
        'package_name' => 'RoyZhu Weather Horoscope Package',
        'welcome_message' => $package->welcome('測試用戶'),
        'current_time' => $package->getCurrentTime(),
        'calculation_test' => $package->add(5, 3),
        'status' => 'Package installed successfully!'
    ]);
});
```

訪問 `http://your-app.test/test-package` 來測試包是否正常工作。
