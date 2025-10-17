#!/bin/bash

# Weather Horoscope Package 發布腳本
# 使用方法: ./release.sh [版本號] [發布類型]

set -e

# 顏色定義
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 函數：打印彩色消息
print_message() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}================================${NC}"
}

# 檢查是否在正確的目錄
if [ ! -f "composer.json" ]; then
    print_error "請在包的根目錄中運行此腳本"
    exit 1
fi

# 獲取當前版本
CURRENT_VERSION=$(grep '"version"' composer.json | sed 's/.*"version": *"\([^"]*\)".*/\1/')
print_message "當前版本: $CURRENT_VERSION"

# 獲取參數
NEW_VERSION=$1
RELEASE_TYPE=$2

if [ -z "$NEW_VERSION" ]; then
    print_error "請提供新版本號 (例如: 1.0.0)"
    exit 1
fi

if [ -z "$RELEASE_TYPE" ]; then
    RELEASE_TYPE="patch"
fi

print_header "開始發布流程"

# 1. 檢查工作目錄是否乾淨
if [ -n "$(git status --porcelain)" ]; then
    print_warning "工作目錄不乾淨，請先提交所有變更"
    git status
    exit 1
fi

# 2. 運行測試
print_message "運行測試..."
if [ -f "test.php" ]; then
    php test.php
    if [ $? -ne 0 ]; then
        print_error "測試失敗，請修復後再試"
        exit 1
    fi
else
    print_warning "未找到測試文件，跳過測試"
fi

# 3. 更新版本號
print_message "更新版本號到 $NEW_VERSION..."

# 更新 composer.json
sed -i.bak "s/\"version\": *\"[^\"]*\"/\"version\": \"$NEW_VERSION\"/" composer.json
rm composer.json.bak

# 更新 CHANGELOG.md
if [ -f "CHANGELOG.md" ]; then
    # 在 CHANGELOG.md 頂部添加新版本
    sed -i.bak "1i\\
## [$NEW_VERSION] - $(date +%Y-%m-%d)\\
\\
### 新增\\
- 版本 $NEW_VERSION 發布\\
\\
" CHANGELOG.md
    rm CHANGELOG.md.bak
fi

# 4. 提交變更
print_message "提交版本變更..."
git add .
git commit -m "Release version $NEW_VERSION"

# 5. 創建標籤
print_message "創建 Git 標籤..."
git tag -a "v$NEW_VERSION" -m "Release version $NEW_VERSION"

# 6. 推送到遠程倉庫
print_message "推送到遠程倉庫..."
git push origin main
git push origin "v$NEW_VERSION"

# 7. 更新 autoload
print_message "更新 Composer autoload..."
composer dump-autoload

print_header "發布完成！"

print_message "版本 $NEW_VERSION 已成功發布"
print_message "Git 標籤: v$NEW_VERSION"
print_message "請記得："
echo "  1. 在 GitHub 上創建 Release"
echo "  2. 更新 Packagist (如果已發布)"
echo "  3. 通知用戶升級"

print_message "用戶可以通過以下命令升級："
echo "  composer update royzhu/weather-horoscope-package"
echo "  composer require royzhu/weather-horoscope-package:^$NEW_VERSION"

print_header "發布腳本執行完成"
