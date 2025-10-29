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

// MENGGUNAKAN MIDDLEWARE
// VALIDASI OTOMATIS. SECARA OTOMATIS AKAN DIARAHKAN KE HALAMAN LOGIN SSO JIKA BELUM TERAUTENTIKASI
Route::middleware(['cas.auth'])->group(function () {
    Route::get('/dashboard', function () {
        $emailAtauUsername = Cas::getCurrentUser();
        return "Halo, " . $emailAtauUsername;
    });

    Route::get('/logout', function () {
        return Cas::logoutAndRedirect(url('/'));
    });
});

```

🚪 Login CAS & Handle Sesi Manual
Untuk melakukan redirect ke login ke server cas:

```bash
use EGanesha\CasAuth\Facades\Cas;
use Illuminate\Support\Facades\Route;

Route::get('/login-sso', function () {
    return Cas::redirectToLogin(url('/profile'));
});

// TANPA MIDDLEWARE
// VALIDASI MANUAL
Route::get('/profile', function () {
    $ticket = request('ticket');
    $serviceUrl = url('/profile');

    if ($ticket && Cas::validateTicket($ticket, $serviceUrl)) {
        Cas::storeCasUserInSession();
        return redirect($serviceUrl);
    }

    // Jika sudah terautentikasi
    if (Cas::isAuthenticated()) {
        $user = Cas::getCurrentUser();
        return "Halo, " . $user;
    }

    return 'Gagal autentikasi';
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
Cas::redirectToLogin($serviceUrl)	        Redirect user ke halaman login CAS dengan parameter service.
Cas::validateTicket($ticket, $serviceUrl)	Memvalidasi tiket CAS yang diterima setelah login.
Cas::storeCasUserInSession()	            Menyimpan informasi user dan atribut ke sesi Laravel.
Cas::isAuthenticated()	                    Mengecek apakah user sudah login melalui CAS.
Cas::getCurrentUser()	                    Mengambil username/email user dari sesi CAS.
Cas::getAttributes()	                    Mengambil seluruh atribut user dari CAS (misal: nama, email, role, dsb).
Cas::logout()	                            Menghapus sesi CAS di aplikasi lokal tanpa logout dari CAS server.
Cas::getLogoutUrl($redirectUrl = null)	    Mendapatkan URL logout CAS server (dapat ditambahkan parameter service).
Cas::logoutAndRedirect($redirectUrl = null)	Menghapus sesi lokal dan langsung redirect ke halaman logout CAS server.
```
