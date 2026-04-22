# /simplify 実施記録

## 対象

003 バックエンド API 実装後のコードレビューと修正。

---

## 検出・修正した問題

### [FIX] `update()` がトグル前の値を返すバグ

**ファイル:** `backend/app/Http/Controllers/TodoController.php`

`$todo->update()` は DB を更新するが、インメモリのモデルインスタンスは変更されない。
そのためレスポンスにトグル前の `is_completed` が返っていた。

```php
// Before
return response()->json($todo);

// After
return response()->json($todo->fresh());
```

テストでは `patchJson` が毎回新しいモデルインスタンスを作るため、このバグは検出できていなかった。

---

### [FIX] `config/cors.php` — 未使用パスと無効な preflight キャッシュ

**ファイル:** `backend/config/cors.php`

- `sanctum/csrf-cookie` はこのプロジェクトで使用していない Sanctum のパス → 削除
- `max_age: 0` は preflight キャッシュを無効にし、CORS リクエストのたびに OPTIONS が発生する → `3600` に変更

```php
// Before
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'max_age' => 0,

// After
'paths' => ['api/*'],
'max_age' => 3600,
```

---

### [FIX] `TodoFactory` — モデルの `$attributes` と重複する定義を削除

**ファイル:** `backend/database/factories/TodoFactory.php`

`Todo` モデルに `protected $attributes = ['is_completed' => false]` が定義済みのため、
ファクトリに `'is_completed' => false` を書くのは冗長。

```php
// Before
return [
    'title' => fake()->sentence(3),
    'is_completed' => false,
];

// After
return [
    'title' => fake()->sentence(3),
];
```

---

### [FIX] `backend/.env.example` — Laravel scaffold による上書きの修正

Laravel の `composer create-project` 実行時に `.env.example` がデフォルト内容で上書きされ、
Docker 向けの設定が失われていた。

```dotenv
# Before
APP_DEBUG=true
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1  (コメントアウト)
# DB_PORT=3306       (コメントアウト)
# DB_DATABASE=laravel (コメントアウト)

# After
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=todo
DB_USERNAME=todo
DB_PASSWORD=your-db-password-here
```

---

## 試みたが見送った修正

### `bootstrap/app.php` の空 `withExceptions` 削除

空のコールバックはノイズに見えるが、Laravel 11 では `withExceptions()` を省略すると
`Illuminate\Contracts\Debug\ExceptionHandler` がバインドされず、バリデーションエラー（422）や
モデル未検出（404）を返すテストが `BindingResolutionException` で落ちる。

**結論:** 空でも必須 → 削除しない。

---

## 修正後のテスト結果

```
OK (6 tests, 17 assertions)
```
