# 003 バックエンド API 実装

## 概要

`TodoController` に CRUD エンドポイントを実装し、REST API として公開する。

## エンドポイント仕様

| メソッド | パス              | 処理           | レスポンス              |
|----------|-------------------|----------------|------------------------|
| GET      | `/api/todos`      | TODO 一覧取得  | `Todo[]` (JSON)        |
| POST     | `/api/todos`      | TODO 新規作成  | 作成した `Todo` (JSON) |
| PATCH    | `/api/todos/{id}` | 完了状態のトグル | 更新した `Todo` (JSON) |
| DELETE   | `/api/todos/{id}` | TODO 削除      | 204 No Content         |

## ToDo

- [x] `app/Http/Controllers/TodoController.php` を作成する
- [x] `routes/api.php` にルーティングを定義する
- [x] `index` アクション — 全件取得して JSON を返す
- [x] `store` アクション — `title` を受け取り新規作成する（バリデーション含む）
- [x] `update` アクション — `is_completed` をトグルする
- [x] `destroy` アクション — 指定 ID を削除する
- [x] 存在しない ID へのアクセス時に 404 を返すことを確認する
- [x] CORS 設定を追加し、フロントエンド（port 5173）からのリクエストを許可する
- [x] `php artisan test` で各エンドポイントのテストが通ることを確認する
