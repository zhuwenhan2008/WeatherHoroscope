<?php

namespace RoyZhu\WeatherHoroscope;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class WeatherHoroscopeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // 發布配置文件
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/weather-horoscope.php' => 
                    base_path('config/weather-horoscope.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // 合併配置文件
        $this->mergeConfigFrom(__DIR__.'/../config/weather-horoscope.php', 'weather-horoscope');

        // 註冊服務
        $this->app->singleton('weather-horoscope', function ($app) {
            return new WeatherHoroscopePackage(
                $app->make(\RoyZhu\WeatherHoroscope\Services\WeatherService::class),
                $app->make(\RoyZhu\WeatherHoroscope\Services\HoroscopeService::class)
            );
        });

        // 註冊天氣服務
        $this->app->singleton(\RoyZhu\WeatherHoroscope\Services\WeatherService::class);

        // 註冊星座服務
        $this->app->singleton(\RoyZhu\WeatherHoroscope\Services\HoroscopeService::class);
    }
}

