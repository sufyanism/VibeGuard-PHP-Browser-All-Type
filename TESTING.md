# VibeGuard PHP Browser — Test Matrix

Start the browser app with:

```powershell
php -S localhost:8000
```

Open `http://localhost:8000`.

| Fixture | Expected |
|---|---|
| `testdata/clean.php` | 0 — PASS / CLEAN |
| `testdata/suspicious.php` | 1 — WARNING / SUSPICIOUS |
| `testdata/flagged.php` | 2 — FLAGGED / AI BREACH |
| `testdata/fatal.php` | 3 — FATAL ERROR |

The fatal fixture is deliberately invalid PHP. The server catches `ParseError`
and returns JSON instead of exposing a PHP fatal-error page.

You can also paste each fixture into the **PASTE CODE** tab.
