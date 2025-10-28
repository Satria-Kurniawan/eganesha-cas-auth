<?php
// PASTIKAN NAMA FILE INI:
// packages/EGanesha/CasAuth/src/Http/Middleware/CasAuthMiddleware.php

namespace EGanesha\CasAuth\Http\Middleware; // <-- Namespace sudah benar

use Closure;
use Illuminate\Http\Request;
use EGanesha\CasAuth\CasManager; // <-- PENTING: Impor CasManager dari namespace root

class CasAuthMiddleware // <-- Nama class sudah benar
{
    /**
     * @var \EGanesha\CasAuth\CasManager
     */
    protected $cas;

    /**
     * INI CONSTRUCTOR YANG BENAR
     * Middleware me-request 'CasManager', BUKAN 'array $config'.
     * Laravel akan otomatis memberikan CasManager yang sudah Anda daftarkan di ServiceProvider.
     *
     * @param \EGanesha\CasAuth\CasManager $cas
     */
    public function __construct(CasManager $cas)
    {
        $this->cas = $cas;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Cek session menggunakan CasManager
        if ($this->cas->isAuthenticated()) {
            return $next($request);
        }

        // 2. URL service yang bersih
        $serviceUrl = $request->url();

        // 3. Cek apakah ada 'ticket' di URL
        $ticket = $request->query('ticket');

        if ($ticket) {
            // 4. Validasi tiket MENGGUNAKAN CasManager
            if ($this->cas->validateTicket($ticket, $serviceUrl)) {

                // 5. Simpan user ke session MENGGUNAKAN CasManager
                if ($this->cas->storeCasUserInSession()) {

                    // 6. Redirect ke URL bersih (untuk menghapus 'ticket')
                    return redirect($serviceUrl);
                }
            }
            // Jika validasi gagal, akan lanjut ke langkah 7
        }

        // 7. Jika belum login atau tiket tidak valid, redirect ke CAS Server
        $loginUrl = $this->cas->getLoginUrl($serviceUrl);

        return redirect($loginUrl);
    }
}
