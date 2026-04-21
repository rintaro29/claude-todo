# 004 フロントエンド初期セットアップ

## 概要

Vue 3 + Vite プロジェクトを作成し、Docker コンテナ上で開発サーバーが起動する状態にする。

## ToDo

- [ ] `frontend/` に Vue 3 + Vite プロジェクトを作成する（`npm create vite@latest`）
- [ ] `vite.config.js` を設定する
  - `server.host: '0.0.0.0'`（コンテナ外からアクセス可能にする）
  - `server.port: 5173`
  - `/api` へのリクエストを `http://nginx` へプロキシする設定を追加する
- [ ] `docker compose up` 後にブラウザで `http://localhost` にアクセスして Vue のデフォルト画面が表示されることを確認する
- [ ] `axios`（または `fetch`）を導入してバックエンドとの通信手段を用意する
