# Module 13 starter — Portfolios saved on EC2

Follows the Power Up! Data Contract v1.0 (see portfolio-example.json).

## Files
| File | What it does |
|---|---|
| `index.html`, `style.css` | Page: list of Portfolios, create form, add-Item form |
| `app.js` | Uses `fetch()` + `FormData` instead of localStorage |
| `api/storage.php` | Shared helpers: readPortfolios, writePortfolios, sendJson |
| `api/portfolios.php` | GET = all Portfolios · POST = create ONE Portfolio |
| `api/items.php` | POST = add ONE Item to ONE Portfolio |

How data travels: the browser sends **form fields** (`new FormData(form)`) → PHP reads `$_POST` (same as php_form/receive.php) → PHP stores and answers **JSON**.
Every input needs a `name="..."` attribute (use the Contract field names) so FormData can send it.

Data file on EC2: `/var/www/data/portfolios.json` (an array of Portfolios, each with `items`).

## Test on your laptop first
Live Server, Live Preview and GitHub Pages only send files — they do **not** run PHP.
Use PHP's own small server instead:
```bash
cd m12-portfolio          # your project folder (the one with index.html)
php -S localhost:8000
```
Open http://localhost:8000 . Keep the terminal open (Ctrl+C stops the server).
On the laptop, data is saved in `local-data/` inside your project (it is in `.gitignore`).
On EC2, the same code saves in `/var/www/data`.

## Practice files (Part 2b): `php_form/`
| File | What it does |
|---|---|
| `php_form/echo.php` | Same as `receive.php`, but answers with JSON |
| `php_form/fetch-demo.html` | The POST form from `form.html`, without `action`/`method` |
| `php_form/fetch-demo.js` | Sends the form with `fetch()` + `FormData`, shows the JSON answer |

Copy these three files into your own `php_form/` folder (next to `receive.php`), then open
http://localhost:8000/php_form/fetch-demo.html

## One-time setup on EC2
```bash
sudo mkdir -p /var/www/data
sudo chown www-data:www-data /var/www/data
```
`/var/www/data` is outside the public folder `/var/www/html`, so nobody can download the JSON file by URL.
Apache runs as the user `www-data`, so that user must own the folder to write the file.

## Deploy (every update)
```bash
cd ~/m12-portfolio
git pull
sudo cp -r ~/m12-portfolio/* /var/www/html/
```

## Tests (Final Demo style)
1. Create 2 Portfolios × 2 Items.
2. Refresh → everything is still there.
3. Open the EC2 URL on a phone / another browser → same data.
4. `sudo systemctl restart apache2` (and later: reboot EC2) → still there.
5. `cat /var/www/data/portfolios.json` shows the stored JSON.

Errors: DevTools → Network/Console, and `sudo tail /var/log/apache2/error.log`.
Note: GitHub Pages cannot run PHP — test with `php -S` on your laptop, then on the EC2 URL.
