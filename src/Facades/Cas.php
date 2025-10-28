<?php

namespace EGanesha\CasAuth\Facades;

use Illuminate\Support\Facades\Facade;

class Cas extends Facade
{
    /**
     * Dapatkan nama komponen yang terdaftar.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        // Ini HARUS sama dengan key yang Anda daftarkan di ServiceProvider
        return 'cas.manager';
    }
}
