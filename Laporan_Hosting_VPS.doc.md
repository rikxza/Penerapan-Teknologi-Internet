# Laporan Lengkap Hosting MoneyGement di Google Cloud VPS

**Tanggal Deploy**: 3 Februari 2026  
**Platform**: Google Cloud Platform - Compute Engine  
**IP Server**: 34.124.226.208  
**Nama VM**: moneygement-server

---

## Daftar Isi

1. [Persiapan Google Cloud](#1-persiapan-google-cloud)
2. [Membuat VM Instance](#2-membuat-vm-instance)
3. [Konfigurasi Firewall](#3-konfigurasi-firewall)
4. [Instalasi Software Server](#4-instalasi-software-server)
5. [Konfigurasi Nginx](#5-konfigurasi-nginx)
6. [Clone & Setup Project](#6-clone--setup-project)
7. [Konfigurasi Database](#7-konfigurasi-database)
8. [Import Database dari Local](#8-import-database-dari-local)
9. [Konfigurasi Environment](#9-konfigurasi-environment)
10. [Finalisasi & Testing](#10-finalisasi--testing)

---

## 1. Persiapan Google Cloud

### 1.1 Buat Akun Google Cloud

1. Kunjungi [Google Cloud Console](https://console.cloud.google.com/)
2. Login dengan akun Google
3. Aktifkan Free Trial ($300 credit untuk 90 hari)

> 📸 **Screenshot**: Halaman Google Cloud Console setelah login

### 1.2 Buat Project Baru

1. Klik dropdown project di header
2. Klik "New Project"
3. Nama project: `MoneyGement`
4. Klik "Create"

> 📸 **Screenshot**: Form pembuatan project baru

---

## 2. Membuat VM Instance

### 2.1 Akses Compute Engine

1. Buka menu hamburger (☰) di kiri atas
2. Pilih **Compute Engine** → **VM instances**
3. Klik **Enable** jika pertama kali

### 2.2 Buat VM Baru

Klik tombol **CREATE INSTANCE** dengan konfigurasi:

| Setting | Value |
|---------|-------|
| Name | `moneygement-server` |
| Region | `asia-southeast1 (Singapore)` |
| Zone | `asia-southeast1-b` |
| Machine type | `e2-micro` (Free tier eligible) |
| Boot disk | Ubuntu 24.04 LTS, 20GB |
| Firewall | ✅ Allow HTTP, ✅ Allow HTTPS |

> 📸 **Screenshot**: Konfigurasi VM Instance

### 2.3 Hasil VM Created

Setelah VM dibuat, catat **External IP**: `34.124.226.208`

> 📸 **Screenshot**: Halaman VM instances dengan External IP terlihat

---

## 3. Konfigurasi Firewall

### 3.1 Buka Port yang Diperlukan

1. **VPC Network** → **Firewall**
2. Klik **CREATE FIREWALL RULE**

**Rule untuk HTTP (80)**:

```
Name: allow-http
Direction: Ingress
Targets: All instances
Source IP: 0.0.0.0/0
Protocols: tcp:80
```

**Rule untuk HTTPS (443)**:

```
Name: allow-https
Direction: Ingress
Targets: All instances
Source IP: 0.0.0.0/0
Protocols: tcp:443
```

> 📸 **Screenshot**: Daftar Firewall Rules

---

## 4. Instalasi Software Server

### 4.1 Akses SSH

Klik tombol **SSH** di baris VM instance untuk membuka terminal browser.

> 📸 **Screenshot**: Terminal SSH browser terbuka

### 4.2 Update System

```bash
sudo apt update && sudo apt upgrade -y
```

**Output yang diharapkan**:

```
Hit:1 http://asia-southeast1.gce.archive.ubuntu.com/ubuntu noble InRelease
...
Reading package lists... Done
Building dependency tree... Done
0 upgraded, 0 newly installed, 0 to remove and 0 not upgraded.
```

> 📸 **Screenshot**: Output perintah apt update

### 4.3 Install Nginx

```bash
sudo apt install nginx -y
```

**Output yang diharapkan**:

```
Reading package lists... Done
...
Setting up nginx (1.24.0-2ubuntu7) ...
```

### 4.4 Install PHP 8.2 dan Extensions

```bash
sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd -y
```

**Output yang diharapkan**:

```
Reading package lists... Done
...
Setting up php8.2-fpm (8.2.30-1+ubuntu24.04) ...
```

> 📸 **Screenshot**: Output instalasi PHP

### 4.5 Install MySQL Server

```bash
sudo apt install mysql-server -y
```

**Output yang diharapkan**:

```
Reading package lists... Done
...
Setting up mysql-server (8.0.45-0ubuntu0.24.04.1) ...
```

### 4.6 Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

**Verifikasi**:

```bash
composer --version
```

**Output yang diharapkan**:

```
Composer version 2.7.x 2024-xx-xx
```

> 📸 **Screenshot**: Output composer --version

### 4.7 Install Node.js & NPM

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y
```

**Verifikasi**:

```bash
node --version && npm --version
```

**Output yang diharapkan**:

```
v20.x.x
10.x.x
```

> 📸 **Screenshot**: Output versi Node.js dan NPM

---

## 5. Konfigurasi Nginx

### 5.1 Buat Konfigurasi Virtual Host

```bash
sudo nano /etc/nginx/sites-available/moneygement
```

**Isi file**:

```nginx
server {
    listen 80;
    server_name 34.124.226.208;
    root /var/www/moneygement/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

> 📸 **Screenshot**: Isi file konfigurasi Nginx

### 5.2 Aktifkan Site & Restart Nginx

```bash
sudo ln -s /etc/nginx/sites-available/moneygement /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
```

**Output `nginx -t` yang diharapkan**:

```
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
```

> 📸 **Screenshot**: Output nginx -t

---

## 6. Clone & Setup Project

### 6.1 Clone Repository

```bash
cd /var/www
sudo git clone https://github.com/rikxza/Penerapan-Teknologi-Internet.git moneygement
```

**Output yang diharapkan**:

```
Cloning into 'moneygement'...
remote: Enumerating objects: xxx, done.
...
Receiving objects: 100% (xxx/xxx), x.xx MiB | x.xx MiB/s, done.
```

> 📸 **Screenshot**: Output git clone

### 6.2 Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/moneygement
sudo chmod -R 775 /var/www/moneygement/storage
sudo chmod -R 775 /var/www/moneygement/bootstrap/cache
```

### 6.3 Install Dependencies

```bash
cd /var/www/moneygement
sudo composer install --no-dev --optimize-autoloader
```

**Output yang diharapkan**:

```
Installing dependencies from lock file
...
Generating optimized autoload files
> @php artisan package:discover
...
```

> 📸 **Screenshot**: Output composer install

### 6.4 Build Frontend Assets

```bash
sudo npm install
sudo npm run build
```

**Output yang diharapkan**:

```
> build
> vite build
...
✓ built in xxxms
```

> 📸 **Screenshot**: Output npm run build

---

## 7. Konfigurasi Database

### 7.1 Buat Database

```bash
sudo mysql
```

Di dalam MySQL:

```sql
CREATE DATABASE moneygements1;
CREATE USER 'laravel'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON moneygements1.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Output yang diharapkan**:

```
Query OK, 1 row affected (0.01 sec)
Query OK, 0 rows affected (0.77 sec)
Query OK, 0 rows affected (0.01 sec)
Query OK, 0 rows affected (0.01 sec)
Bye
```

> 📸 **Screenshot**: Output pembuatan database dan user MySQL

---

## 8. Import Database dari Local

### 8.1 Export Database dari Local (Laragon)

1. Buka Laragon → klik **Database**
2. Di HeidiSQL, klik database `moneygements1`
3. Menu **Tools** → **Export database as SQL**
4. Simpan sebagai `moneygements1.sql`

> 📸 **Screenshot**: Export database dari HeidiSQL

### 8.2 Upload File SQL ke VPS

1. Di terminal SSH browser Google Cloud
2. Klik icon **gear** (⚙️) di pojok kanan atas
3. Pilih **Upload file**
4. Pilih file `moneygements1.sql`

> 📸 **Screenshot**: Proses upload file via SSH browser

### 8.3 Import ke MySQL VPS

```bash
sudo mysql moneygements1 < ~/moneygements1.sql
```

**Verifikasi**:

```bash
sudo mysql -e "USE moneygements1; SHOW TABLES;"
```

**Output yang diharapkan**:

```
+---------------------------+
| Tables_in_moneygements1   |
+---------------------------+
| budgets                   |
| categories                |
| failed_jobs               |
| migrations                |
| notifications             |
| password_reset_tokens     |
| personal_access_tokens    |
| sessions                  |
| tickets                   |
| transactions              |
| users                     |
+---------------------------+
```

> 📸 **Screenshot**: Output SHOW TABLES

---

## 9. Konfigurasi Environment

### 9.1 Setup File .env

```bash
cd /var/www/moneygement
sudo cp .env.example .env
sudo nano .env
```

**Konfigurasi yang diubah**:

```ini
APP_NAME=MoneyGement
APP_ENV=production
APP_DEBUG=false
APP_URL=http://34.124.226.208

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=moneygements1
DB_USERNAME=laravel
DB_PASSWORD=password123

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

> 📸 **Screenshot**: Isi file .env (sensor password)

### 9.2 Generate App Key

```bash
sudo php artisan key:generate
```

**Output yang diharapkan**:

```
INFO  Application key set successfully.
```

### 9.3 Run Migrations (Opsional jika import database)

```bash
sudo php artisan migrate --force
```

### 9.4 Link Storage

```bash
sudo php artisan storage:link
```

**Output yang diharapkan**:

```
INFO  The [public/storage] link has been connected to [storage/app/public].
```

### 9.5 Optimize untuk Production

```bash
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

> 📸 **Screenshot**: Output optimize commands

---

## 10. Finalisasi & Testing

### 10.1 Perbaiki Permission Final

```bash
sudo chown -R www-data:www-data /var/www/moneygement
sudo chmod -R 775 /var/www/moneygement/storage
sudo chmod -R 775 /var/www/moneygement/bootstrap/cache
```

### 10.2 Restart Services

```bash
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

### 10.3 Test Koneksi Database

```bash
cd /var/www/moneygement
php artisan tinker
```

```php
\App\Models\User::count()
```

**Output yang diharapkan**:

```
= 13
```

> 📸 **Screenshot**: Output User::count() menunjukkan 13

### 10.4 Akses Website

Buka browser dan akses: `http://34.124.226.208`

> 📸 **Screenshot**: Halaman login MoneyGement

### 10.5 Test Login

Login dengan akun yang ada di database lokal.

> 📸 **Screenshot**: Dashboard setelah login berhasil

---

## Troubleshooting yang Ditemui

### Error 1: Permission Denied saat edit .env

**Solusi**: Gunakan `sudo nano`

### Error 2: MySQL Access Denied untuk root

**Solusi**: Buat user MySQL baru dengan password authentication

### Error 3: 500 Error saat Register (Email)

**Solusi**: Konfigurasi SMTP dengan benar di .env

---

## Kesimpulan

✅ VM Instance berhasil dibuat di Google Cloud  
✅ Nginx, PHP 8.2, MySQL 8.0 berhasil diinstal  
✅ Project Laravel berhasil di-deploy  
✅ Database lokal berhasil diimport ke VPS  
✅ Website dapat diakses via IP publik  
✅ User dapat login dengan akun yang sudah ada  

**URL Akses**: <http://34.124.226.208>

---

**Catatan**: Ganti placeholder `📸 **Screenshot**:` dengan screenshot yang sesuai dari proses yang Anda lakukan.
