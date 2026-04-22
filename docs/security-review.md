# セキュリティレビュー記録

## 実施タイミング

003 バックエンド API 実装完了後

---

## 検出した問題と対応状況

### CRITICAL

#### 1. `.env` に実パスワードが存在（誤 add のリスク）

- **ファイル:** `.env`, `backend/.env`
- **内容:** `DB_PASSWORD=secret`, `DB_ROOT_PASSWORD=rootsecret`, `APP_KEY=base64:...` が記載されている
- **リスク:** `.gitignore` で保護されているが `git add .` などによる誤コミットの可能性
- **対応:** `.gitignore` で保護済み ✅
- **運用注意:** `APP_KEY` はクローン後に必ず `php artisan key:generate` で再生成すること。DB パスワードはこのリポジトリ外では使い回さないこと

#### 2. `settings.local.json` が git で追跡されていた

- **ファイル:** `.claude/settings.local.json`
- **内容:** `git rm --cached` 前は git HEAD で追跡されており、将来の変更が意図せずコミットされるリスクがあった
- **対応:** `git rm --cached .claude/settings.local.json` で追跡解除 ✅

---

### HIGH

#### 3. `backend/.env` の `APP_DEBUG=true`

- **ファイル:** `backend/.env`
- **内容:** デバッグモードが有効だと Laravel がスタックトレースや環境変数をレスポンスに露出する
- **対応:** `APP_DEBUG=false` に修正 ✅

#### 4. Vite の port 5173 が全インターフェースに公開されていた

- **ファイル:** `docker-compose.yml`
- **内容:** `"5173:5173"` は `0.0.0.0` バインドで同一 LAN から到達可能だった
- **対応:** `"127.0.0.1:5173:5173"` に変更 ✅

#### 5. nginx にレート制限・セキュリティヘッダーなし

- **ファイル:** `docker/nginx/default.conf`
- **内容:** `POST /api/todos` 等への無制限リクエストが可能。`X-Frame-Options`, `X-Content-Type-Options` 等のヘッダーも未設定
- **対応:** ⏭ ローカル開発専用のため本番移行前に対応

---

### MEDIUM

#### 6. Docker イメージタグが浮動

- **ファイル:** `docker-compose.yml`, `docker/php/Dockerfile`
- **内容:** `mysql:8.0`, `node:20-alpine`, `composer:2` はパッチリリースで意図しない変更が入る可能性がある
- **対応:** ⏭ 本番移行前にダイジェスト固定（例: `mysql:8.0.36`）を推奨

#### 7. nginx が全 `.php` ファイルを PHP-FPM に渡していた

- **ファイル:** `docker/nginx/default.conf`
- **内容:** `location ~ \.php$` はパストラバーサルや意図しない PHP ファイル実行のリスク
- **対応:** `index.php` のみ実行可に制限し、他の `.php` は 404 を返すよう修正 ✅

```nginx
location ~ ^/index\.php(/|$) {
    fastcgi_pass php:9000;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
    internal;
}
location ~ \.php$ { return 404; }
```

#### 8. `APP_KEY` が `.env` に実値として存在

- **ファイル:** `backend/.env`
- **内容:** Laravel の暗号化キーが untracked ファイルに存在。漏洩するとセッション・Cookie の偽造が可能
- **対応:** `.gitignore` で保護済み ✅。クローン後は `php artisan key:generate` で再生成すること

---

### LOW

#### 9. `backend/.env` の `DB_PASSWORD` が空

- **ファイル:** `backend/.env`
- **内容:** `DB_PASSWORD=` が空だが、docker-compose が OS 環境変数として注入するため動作上は問題なし。ただし `config:cache` 実行時に空の値が焼き込まれる危険がある
- **対応:** 設計として許容。`config:cache` はこの開発環境では使用しないこと

#### 10. `npm install` を毎起動実行（再現性なし）

- **ファイル:** `docker-compose.yml`
- **内容:** 起動のたびに最新パッケージを取得するため、パッケージの改ざんが無検証で取り込まれるリスク
- **対応:** ⏭ 004 フロントエンド実装時に Dockerfile 化し `npm ci` に変更予定

---

## 対応済み一覧

| 対応内容 | ファイル |
|---|---|
| `settings.local.json` を git 追跡から除外 | `.gitignore` + `git rm --cached` |
| `APP_DEBUG=false` に修正 | `backend/.env` |
| Vite port を `127.0.0.1` にバインド | `docker-compose.yml` |
| nginx PHP 実行を `index.php` のみに制限 | `docker/nginx/default.conf` |

## 未対応（本番前に対応が必要）

- nginx レート制限・セキュリティヘッダーの追加
- Docker イメージタグのダイジェスト固定
- frontend の Dockerfile 化と `npm ci` への変更
