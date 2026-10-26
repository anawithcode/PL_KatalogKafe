# Senja Kopi — Katalog Menu Kafe

Aplikasi CRUD berbasis **PHP native** dengan arsitektur **MVC (Model–View–Controller)** dan **Query Builder berbasis PDO**. Dapat dijalankan menggunakan SQLite tanpa konfigurasi atau MySQL/MariaDB melalui XAMPP.

## Fitur

- Create: menambahkan menu beserta kategori, deskripsi, harga, stok, dan status.
- Read: daftar menu, detail menu, pencarian nama, filter kategori/status, pengurutan, dan pagination.
- Update: memperbarui seluruh informasi menu.
- Delete: menghapus menu dengan dialog konfirmasi.
- Validasi server, pesan kesalahan, flash message, CSRF token, dan prepared statement PDO.
- Tampilan responsif untuk desktop dan ponsel.

## Struktur MVC

```text
app/
├── Core/
│   ├── Database.php       # Koneksi PDO
│   ├── QueryBuilder.php   # Fluent Query Builder + parameter binding
│   ├── Router.php         # Routing HTTP
│   └── View.php           # Renderer View
├── Controllers/
│   └── MenuController.php # Alur request, validasi, dan response
├── Models/
│   └── Menu.php           # Operasi data tabel menus melalui Query Builder
├── Views/
│   ├── layouts/main.php
│   ├── menus/             # Daftar, detail, dan form menu
│   └── errors/404.php
├── helpers.php
config/database.php        # Konfigurasi SQLite/MySQL
public/
├── index.php              # Front Controller
└── assets/                # CSS dan JavaScript
database/
├── katalog_kafe.sql       # Skema + seed MySQL
└── schema.sqlite.sql      # Skema + seed SQLite
storage/                   # Lokasi database SQLite
Dockerfile
```

## Menjalankan dengan SQLite (paling mudah)

Persyaratan: PHP 8.1+ dengan ekstensi `pdo_sqlite`.

```bash
php -S localhost:8000 -t public public/index.php
```

Buka `http://localhost:8000`. Database `storage/katalog_kafe.sqlite` beserta delapan data contoh dibuat otomatis saat pertama kali aplikasi dijalankan.

## Menjalankan dengan MySQL / XAMPP

1. Salin folder proyek ke `htdocs/senja-kopi`.
2. Aktifkan Apache dan MySQL melalui XAMPP.
3. Buka phpMyAdmin, lalu impor `database/katalog_kafe.sql`.
4. Atur konfigurasi melalui environment variable atau ubah nilai default di `config/database.php`:

```text
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=katalog_kafe
DB_USERNAME=root
DB_PASSWORD=
```

5. Buka `http://localhost/senja-kopi/public`.

Pastikan ekstensi `pdo_mysql` aktif di `php.ini`. Jika Apache tidak mengaktifkan `mod_rewrite`, izinkan `.htaccess` dengan `AllowOverride All`.

## Contoh penggunaan Query Builder

Semua operasi CRUD pada Model menggunakan Query Builder, bukan SQL mentah:

```php
// Read
$menu = $this->query()->where('id', '=', $id)->first();

// Create
$this->query()->insert($data);

// Update
$this->query()->where('id', '=', $id)->update($data);

// Delete
$this->query()->where('id', '=', $id)->delete();
```

Query Builder mendukung `select`, `distinct`, `where`, `orWhere`, `orderBy`, `limit`, `offset`, `get`, `first`, `count`, `sum`, `insert`, `update`, dan `delete`. Nilai dikirim dengan prepared statement PDO. Nama tabel/kolom dan operator divalidasi; operasi update/delete tanpa `WHERE` ditolak.

## Menjalankan dengan Docker

```bash
docker build -t senja-kopi .
docker run --rm -p 8080:80 senja-kopi
```

Buka `http://localhost:8080`. Docker menggunakan SQLite agar aplikasi langsung dapat dicoba.
