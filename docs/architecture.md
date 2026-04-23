# アーキテクチャ

## 全体構成

```
ブラウザ
  │
  │ HTTP :80
  ▼
┌─────────────────────────────────────────┐
│  nginx (リバースプロキシ)                │
│                                         │
│  /api/*  ──────────────► PHP-FPM :9000  │
│                              │          │
│  /*  ──────────────────► Vite :5173     │
└─────────────────────────────────────────┘
                               │
                        MySQL :3306
```

## コンテナ一覧

| コンテナ | イメージ | ポート | 役割 |
|---|---|---|---|
| nginx | nginx:1.25-alpine | 0.0.0.0:80 | リバースプロキシ |
| php | ./docker/php (PHP 8.3-FPM) | 9000 (内部のみ) | Laravel API |
| mysql | mysql:8.0 | 127.0.0.1:3306 | データベース |
| frontend | ./frontend (Node 20) | 127.0.0.1:5173 | Vite 開発サーバー |

## リクエストフロー

### フロントエンド（画面表示）

```
ブラウザ → nginx:80 → Vite:5173 → Vue SPA を返す
```

- nginx の `location /` が Vite 開発サーバーにプロキシ
- WebSocket（HMR）も `Upgrade` ヘッダーで透過

### API（データ操作）

```
ブラウザ → nginx:80/api/* → PHP-FPM:9000 → Laravel → MySQL
```

- nginx の `location /api/` が `try_files` 経由で `index.php` に fallback
- Laravel のルートモデルバインディングが ID → モデル解決・404 を担当

## ディレクトリ構成

```
todo-app/
├── docker-compose.yml
├── docker/
│   ├── nginx/default.conf       # nginx ルーティング設定
│   └── php/Dockerfile           # PHP 8.3-FPM + Composer 2
│
├── backend/                     # Laravel 11
│   ├── app/Http/Controllers/
│   │   └── TodoController.php   # GET/POST/PATCH/DELETE
│   ├── app/Models/
│   │   └── Todo.php             # $fillable / $casts / $attributes
│   ├── config/cors.php          # CORS 許可オリジン
│   ├── database/migrations/
│   │   └── *_create_todos_table.php
│   └── routes/api.php           # Route::apiResource
│
└── frontend/                    # Vue 3 + Vite
    ├── Dockerfile               # npm ci ベース
    ├── vite.config.js           # host / port / proxy 設定
    └── src/
        ├── main.js
        ├── App.vue              # 一覧・追加フォーム・エラー表示
        └── components/
            └── TodoItem.vue     # チェックボックス・削除ボタン
```

## データモデル

### `todos` テーブル

| カラム | 型 | 備考 |
|---|---|---|
| id | BIGINT PK | auto increment |
| title | VARCHAR(255) | |
| is_completed | BOOLEAN | デフォルト: false |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

## API エンドポイント

| メソッド | パス | 処理 | レスポンス |
|---|---|---|---|
| GET | `/api/todos` | 全件取得（`created_at` 降順） | 200 + `Todo[]` |
| POST | `/api/todos` | 新規作成（`title` 必須） | 201 + `Todo` |
| PATCH | `/api/todos/{id}` | `is_completed` をトグル | 200 + `Todo` |
| DELETE | `/api/todos/{id}` | 削除 | 204 No Content |

## 環境変数

| 変数 | 設定場所 | 用途 |
|---|---|---|
| `DB_PASSWORD` | `.env` (git 管理外) | MySQL パスワード（未設定時は起動エラー） |
| `DB_ROOT_PASSWORD` | `.env` (git 管理外) | MySQL root パスワード（未設定時は起動エラー） |
| `DB_DATABASE` | `.env` または省略 | DB 名（デフォルト: `todo`） |
| `DB_USERNAME` | `.env` または省略 | DB ユーザー（デフォルト: `todo`） |

## nginx の設計上のポイント

- **DNS 解決の遅延:** `resolver 127.0.0.11` + `set $frontend` 変数により、起動時ではなくリクエスト時に DNS 解決。frontend コンテナが未起動でも nginx がクラッシュしない
- **PHP 実行の制限:** `index.php` のみ PHP-FPM に渡し、他の `.php` へのアクセスは 404 を返す
- **WebSocket 対応:** `Upgrade` / `Connection` ヘッダーを透過させ Vite の HMR が動作
