# 🔐 Laravel CAS Auth by Eganesha

Package ini menyediakan **integrasi Single Sign-On (SSO)** dengan **Central Authentication Service (CAS)** untuk aplikasi berbasis **Laravel**.  
Didesain agar mudah digunakan, ringan, dan bisa diintegrasikan dengan CAS server seperti **https://sso.undiksha.ac.id/cas**.

---

## 🚀 Instalasi

Tambahkan package ini menggunakan Composer:

```bash
composer require eganesha/cas-auth
```

⚙️ Konfigurasi

Publikasikan file konfigurasi:

```bash
php artisan vendor:publish --provider="EGanesha\CasAuth\CasServiceProvider"
```

Tambahkan URL server CAS di file .env:

```bash
CAS_SERVER_URL=https://sso.undiksha.ac.id/cas
```

🧩 Penggunaan

Tambahkan middleware cas.auth ke route Anda:

```bash

<?php

use EGanesha\CasAuth\Facades\Cas;
use Illuminate\Support\Facades\Route;

Route::middleware('cas.auth')->get('/', function () {
    if (Cas::isAuthenticated()) {
        $user = Cas::getCurrentUser();
        $attributes = Cas::getAttributes();

        return $user;
    }

    return 'Belum terautentikasi melalui CAS.';
});

```

🚪 Logout CAS

Untuk melakukan logout dari CAS dan menghapus sesi:

```bash
use EGanesha\CasAuth\Facades\Cas;
use Illuminate\Support\Facades\Route;

Route::get('/logout', function () {
    Cas::logoutAndRedirect();
});
```

🧠 Metode yang Tersedia

```bash
Cas::isAuthenticated()	    Mengecek apakah user sudah login melalui CAS.
Cas::getCurrentUser()	    Mendapatkan username/NIP/email user dari CAS.
Cas::logoutAndRedirect()    Logout dari CAS dan redirect ke halaman logout CAS server.
```
