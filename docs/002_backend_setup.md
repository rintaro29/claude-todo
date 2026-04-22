# 002 バックエンド初期セットアップ

## 概要

Laravel 11 プロジェクトを作成し、データベース接続・マイグレーションまでを完了させる。

## ToDo

- [x] `backend/` に Laravel 11 プロジェクトを作成する（`composer create-project`）
- [x] `.env` を Docker 環境に合わせて設定する
  - `DB_HOST=mysql`
  - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` を docker-compose.yml と揃える
- [x] `todos` テーブルのマイグレーションファイルを作成する

  | カラム       | 型           | 備考               |
  |--------------|--------------|-------------------|
  | id           | BIGINT PK    | auto increment     |
  | title        | VARCHAR(255) |                   |
  | is_completed | BOOLEAN      | デフォルト: false  |
  | created_at   | TIMESTAMP    |                   |
  | updated_at   | TIMESTAMP    |                   |

- [x] `php artisan migrate` が正常に完了することを確認する
- [x] `Todo` モデルを作成し、`$fillable` に `title`・`is_completed` を設定する
