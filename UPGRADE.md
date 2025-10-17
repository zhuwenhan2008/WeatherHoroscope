# 升級指南

## 版本管理策略

本包使用 [語義化版本控制](https://semver.org/lang/zh-TW/) (Semantic Versioning)：

- **主版本號 (MAJOR)**: 當你做了不相容的 API 修改
- **次版本號 (MINOR)**: 當你做了向下相容的功能性新增
- **修訂號 (PATCH)**: 當你做了向下相容的問題修正

## 升級方式

### 1. 通過 Composer 升級（推薦）

```bash
# 升級到最新版本
composer update royzhu/weather-horoscope-package

# 升級到特定版本
composer require royzhu/weather-horoscope-package:^2.0

# 檢查可用的版本
composer show royzhu/weather-horoscope-package --available

# 檢查當前版本
composer show royzhu/weather-horoscope-package
```

### 2. 本地開發升級

如果你使用本地路徑安裝：

```bash
# 更新本地包
cd /path/to/weather-horoscope-package
git pull origin main
composer install

# 更新你的專案
cd /path/to/your-project
composer update royzhu/weather-horoscope-package
```

### 3. Git 倉庫升級

如果你使用 Git 倉庫安裝：

```bash
composer update royzhu/weather-horoscope-package
```

## 版本歷史

### v1.0.0 (2024-10-14)

- 🎉 初始版本發布
- ✨ 天氣資訊功能
- ✨ 星座運勢功能
- ✨ Array 格式 API
- ✨ 錯誤處理機制
- ✨ Laravel 自動發現

### 計劃中的版本

#### v1.1.0 (計劃中)

- ✨ 添加更多天氣預報選項
- ✨ 支援多個地區
- ✨ 添加天氣圖表功能
- 🔧 改進錯誤處理

#### v1.2.0 (計劃中)

- ✨ 添加星座詳細分析
- ✨ 支援自訂星座配置
- ✨ 添加運勢歷史記錄
- 🔧 性能優化

#### v2.0.0 (計劃中)

- ⚠️ **重大變更**: API 重構
- ✨ 新的服務架構
- ✨ 支援更多天氣服務提供商
- ✨ 完全重寫的星座運勢引擎

## 升級注意事項

### 從 v1.x 升級到 v2.x

⚠️ **重要**: v2.0 將包含重大變更，請仔細閱讀 [升級指南](UPGRADE.md)。

### 配置變更

某些版本可能包含配置變更：

```bash
# 發布新的配置文件
php artisan vendor:publish --provider="RoyZhu\WeatherHoroscope\WeatherHoroscopeServiceProvider" --tag="config" --force
```

### 數據庫變更

如果包包含數據庫遷移：

```bash
php artisan migrate
```

## 故障排除

### 常見問題

1. **版本衝突**

```bash
# 檢查依賴衝突
composer why-not royzhu/weather-horoscope-package:^2.0
```

2. **自動發現問題**

```bash
# 清除配置緩存
php artisan config:clear
php artisan config:cache
```

3. **服務提供者問題**

```bash
# 手動註冊服務提供者
# 在 config/app.php 中添加：
'providers' => [
    // ...
    RoyZhu\WeatherHoroscope\WeatherHoroscopeServiceProvider::class,
],
```

### 回滾版本

如果需要回滾到舊版本：

```bash
composer require royzhu/weather-horoscope-package:^1.0
```

## 支援

如果你在升級過程中遇到問題：

1. 查看 [GitHub Issues](https://github.com/royzhu/weather-horoscope-package/issues)
2. 閱讀 [文檔](README.md)
3. 提交 [Issue](https://github.com/royzhu/weather-horoscope-package/issues/new)

## 貢獻

歡迎貢獻代碼來幫助改進這個包！

1. Fork 這個倉庫
2. 創建你的功能分支 (`git checkout -b feature/AmazingFeature`)
3. 提交你的變更 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 開啟一個 Pull Request
