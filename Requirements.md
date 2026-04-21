# TODOアプリ 要件定義

## 概要

VueとLaravelを使ったシンプルなTODOアプリ。  
フロントエンドはSPA構成、バックエンドはREST APIとして実装する。

---

## 技術スタック

| レイヤー       | 技術                    |
| -------------- | ----------------------- |
| フロントエンド | Vue 3 + Vite            |
| バックエンド   | Laravel 11              |
| データベース   | MySQL 8.0               |
| Web サーバー   | nginx                   |
| 実行環境       | PHP-FPM                 |
| 開発環境       | Docker / Docker Compose |

---

## 開発環境

- **構成**: Docker（コンテナ構成）
- **コンテナ**:
  - `nginx` — リバースプロキシ（ポート 80）
  - `php` — PHP-FPM（Laravel）
  - `mysql` — データベース（ポート 3306）
  - `frontend` — Node.js + Vite 開発サーバー（ポート 5173）

---

## 機能要件

### TODO管理（基本CRUD）

| 機能         | 詳細                                   |
| ------------ | -------------------------------------- |
| 追加         | テキスト入力でTODOを新規作成する       |
| 削除         | TODOを一件削除する                     |
| 完了チェック | TODOの完了／未完了をトグルで切り替える |

### 対象外（今回のスコープ外）

- ユーザー認証（ログイン・会員登録）
- カテゴリ・タグ機能
- 期限日の設定

---

## API設計

| メソッド | エンドポイント    | 処理           |
| -------- | ----------------- | -------------- |
| GET      | `/api/todos`      | TODO一覧取得   |
| POST     | `/api/todos`      | TODO新規作成   |
| PATCH    | `/api/todos/{id}` | 完了状態の更新 |
| DELETE   | `/api/todos/{id}` | TODO削除       |

---

## データモデル

### `todos` テーブル

| カラム名     | 型           | 説明                            |
| ------------ | ------------ | ------------------------------- |
| id           | BIGINT (PK)  | 主キー                          |
| title        | VARCHAR(255) | TODOのタイトル                  |
| is_completed | BOOLEAN      | 完了フラグ（デフォルト: false） |
| created_at   | TIMESTAMP    | 作成日時                        |
| updated_at   | TIMESTAMP    | 更新日時                        |

---

## ディレクトリ構成（想定）

```
todo-app/
├── docker-compose.yml
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── Dockerfile
├── backend/          # Laravel プロジェクト
│   ├── app/
│   │   └── Http/
│   │       └── Controllers/
│   │           └── TodoController.php
│   ├── database/
│   │   └── migrations/
│   └── routes/
│       └── api.php
└── frontend/         # Vue + Vite プロジェクト
    ├── src/
    │   ├── components/
    │   │   └── TodoItem.vue
    │   ├── App.vue
    │   └── main.js
    ├── index.html
    └── vite.config.js
```

---

## 非機能要件

- フロントエンドはSPA（シングルページアプリケーション）として動作すること
- フロントエンドとバックエンドはAPIで分離すること（疎結合）
- ローカル開発はDockerのみで完結すること（ホストマシンへの直接インストール不要）
