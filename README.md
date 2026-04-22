# claude-todo

Vue 3 + Laravel 11 で構築したシンプルな TODO アプリ。

## 技術スタック

| レイヤー | 技術 |
|---|---|
| フロントエンド | Vue 3 + Vite |
| バックエンド | Laravel 11 (PHP-FPM) |
| データベース | MySQL 8.0 |
| Web サーバー | nginx |
| 実行環境 | Docker / Docker Compose |

## セットアップ

### 1. 環境変数の設定

```bash
cp .env.example .env
```

`.env` を開いてパスワードを設定:

```dotenv
DB_PASSWORD=任意のパスワード
DB_ROOT_PASSWORD=任意のルートパスワード
```

### 2. 起動

```bash
docker compose up -d
```

### 3. マイグレーション（初回のみ）

```bash
docker compose exec php php artisan migrate
```

### 4. アクセス

| サービス | URL |
|---|---|
| アプリ | http://localhost |
| API | http://localhost/api/todos |
| MySQL | 127.0.0.1:3306 |

---

## 停止

```bash
# コンテナのみ停止（データ保持）
docker compose down

# データも含めて削除
docker compose down -v
```

## 開発コマンド

```bash
# バックエンドのテスト
docker compose exec php php artisan test

# マイグレーション再実行
docker compose exec php php artisan migrate:fresh

# ログ確認
docker compose logs php
docker compose logs nginx
```
