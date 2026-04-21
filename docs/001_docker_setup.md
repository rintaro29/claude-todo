# 001 Docker 環境構築

## 概要

Docker Compose を使ってローカル開発環境を構築する。
ホストマシンへの直接インストールなしに、コンテナのみで開発が完結する構成にする。

## コンテナ構成

| コンテナ   | 役割                          | ポート |
|-----------|-------------------------------|--------|
| nginx     | リバースプロキシ              | 80     |
| php       | PHP-FPM（Laravel）            | —      |
| mysql     | データベース（MySQL 8.0）     | 3306   |
| frontend  | Node.js + Vite 開発サーバー   | 5173   |

## ToDo

- [ ] `docker-compose.yml` を作成する
- [ ] `docker/php/Dockerfile` を作成する（PHP-FPM + Composer）
- [ ] `docker/nginx/default.conf` を作成する
  - `/api/*` → PHP-FPM へプロキシ
  - その他 → Vite 開発サーバー（port 5173）へプロキシ
- [ ] MySQL の初期データベース・ユーザー設定を環境変数で定義する
- [ ] `docker compose up -d` で全コンテナが起動することを確認する
- [ ] 各コンテナ間の疎通確認（nginx → php、php → mysql）
