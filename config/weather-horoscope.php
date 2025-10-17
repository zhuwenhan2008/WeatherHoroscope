<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Weather Horoscope Package 配置
    |--------------------------------------------------------------------------
    |
    | 這裡是 Weather Horoscope Package 的配置文件
    |
    */

    'name' => env('WEATHER_HOROSCOPE_NAME', 'Weather Horoscope Package'),
    
    'version' => '1.0.0',
    
    'enabled' => env('WEATHER_HOROSCOPE_ENABLED', true),
    
    'default_message' => '歡迎使用 Weather Horoscope Package!',
    
    'timezone' => env('WEATHER_HOROSCOPE_TIMEZONE', 'Asia/Taipei'),

    /*
    |--------------------------------------------------------------------------
    | 天氣 API 配置
    | 請先註冊帳號並申請 API 金鑰（https://opendata.cwa.gov.tw/index）
    |--------------------------------------------------------------------------
    */
    
    'weather' => [
        'api_key' => env('CWB_API_KEY', 'API KEY'),
        'daily_weather_url' => 'https://opendata.cwa.gov.tw/api/v1/rest/datastore/F-C0032-001',
        'rainfall_url' => 'https://opendata.cwa.gov.tw/api/v1/rest/datastore/F-D0047-061',
        'location_name' => env('WEATHER_LOCATION', '臺北市'),
        'rainfall_location' => env('RAINFALL_LOCATION', '信義區'),
    ],

    /*
    |--------------------------------------------------------------------------
    | 星座運勢配置
    |--------------------------------------------------------------------------
    */
    
    'horoscope' => [
        'base_url' => 'https://astro.click108.com.tw/daily_10.php',
        'stars' => [
            '1' => '魔羯座',
            '2' => '水瓶座',
            '3' => '雙魚座',
            '4' => '牡羊座',
            '5' => '金牛座',
            '6' => '雙子座',
            '7' => '巨蟹座',
            '8' => '獅子座',
            '9' => '處女座',
            '10' => '天秤座',
            '11' => '天蠍座',
            '12' => '射手座',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ChatWork 配置 (可選)
    |--------------------------------------------------------------------------
    */
    
    'chatwork' => [
        'api_token' => env('CHATWORK_API_TOKEN'),
        'room_id' => env('CHATWORK_ROOM_ID'),
        'enabled' => env('CHATWORK_ENABLED', false),
    ],
];

