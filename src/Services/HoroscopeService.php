<?php

namespace RoyZhu\WeatherHoroscope\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class HoroscopeService
{
    protected string $baseUrl;
    protected array $stars;

    public function __construct()
    {
        $this->baseUrl = Config::get('weather-horoscope.horoscope.base_url');
        $this->stars = Config::get('weather-horoscope.horoscope.stars');
    }

    /**
     * 獲取今日星座運勢
     */
    public function getTodayHoroscope(): array
    {
        $allArr = [];
        $loveArr = [];
        $jobArr = [];
        $moneyArr = [];

        // 獲取所有星座的運勢
        foreach ($this->stars as $starNum => $starName) {
            $this->getTodayStarLucky($starName, $starNum, $allArr, $loveArr, $jobArr, $moneyArr);
        }

        // 找出最高運勢的星座
        $maxAllStars = $this->getTopStars($allArr);
        $maxLoveStars = $this->getTopStars($loveArr);
        $maxJobStars = $this->getTopStars($jobArr);
        $maxMoneyStars = $this->getTopStars($moneyArr);

        return [
            'date' => Carbon::now()->format('Y-m-d'),
            'overall_luck' => [
                'top_stars' => $maxAllStars,
                'max_score' => !empty($allArr) ? max($allArr) : 0
            ],
            'love_luck' => [
                'top_stars' => $maxLoveStars,
                'max_score' => !empty($loveArr) ? max($loveArr) : 0
            ],
            'work_luck' => [
                'top_stars' => $maxJobStars,
                'max_score' => !empty($jobArr) ? max($jobArr) : 0
            ],
            'money_luck' => [
                'top_stars' => $maxMoneyStars,
                'max_score' => !empty($moneyArr) ? max($moneyArr) : 0
            ],
            'all_scores' => [
                'overall' => $allArr,
                'love' => $loveArr,
                'work' => $jobArr,
                'money' => $moneyArr
            ],
            'status' => 'success'
        ];
    }

    /**
     * 獲取指定星座的今日運勢
     */
    protected function getTodayStarLucky(string $starName, string $starNum, array &$allArr, array &$loveArr, array &$jobArr, array &$moneyArr): void
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            $starLink = $this->baseUrl . '?iAcDay=' . $today . '&iAstro=' . $starNum;
            
            $response = Http::timeout(30)->get($starLink);
            
            if (!$response->successful()) {
                $allArr[$starName] = 0;
                $loveArr[$starName] = 0;
                $jobArr[$starName] = 0;
                $moneyArr[$starName] = 0;
                return;
            }

            $html = $response->body();
            
            // 解析 HTML 獲取星級
            $allArr[$starName] = $this->countStars($html, 'txt_green');
            $loveArr[$starName] = $this->countStars($html, 'txt_pink');
            $jobArr[$starName] = $this->countStars($html, 'txt_blue');
            $moneyArr[$starName] = $this->countStars($html, 'txt_orange');
            
        } catch (\Exception $e) {
            // 發生錯誤時設為 0
            $allArr[$starName] = 0;
            $loveArr[$starName] = 0;
            $jobArr[$starName] = 0;
            $moneyArr[$starName] = 0;
        }
    }

    /**
     * 計算指定類別的星級數量
     */
    protected function countStars(string $html, string $className): int
    {
        // 使用正則表達式匹配指定 class 的 span 標籤
        $pattern = '/<span[^>]*class="[^"]*' . preg_quote($className, '/') . '[^"]*"[^>]*>.*?<\/span>/s';
        
        if (preg_match($pattern, $html, $matches)) {
            return substr_count($matches[0], '★');
        }
        
        return 0;
    }

    /**
     * 獲取最高運勢的星座列表
     */
    protected function getTopStars(array $dict): array
    {
        if (empty($dict)) {
            return [];
        }
        
        $maxValue = max($dict);
        $topStars = [];
        
        foreach ($dict as $starName => $value) {
            if ($value === $maxValue) {
                $topStars[] = $starName;
            }
        }
        
        return $topStars;
    }

    /**
     * 獲取相同運勢值的星座名稱（保留用於向後兼容）
     */
    protected function getShowStarStr(string $starName, array $dict): string
    {
        $rtStr = '';
        $value = $dict[$starName] ?? 0;
        
        foreach ($dict as $key => $val) {
            if ($val === $value) {
                $rtStr .= $key . ',';
            }
        }
        
        return rtrim($rtStr, ',');
    }
}
