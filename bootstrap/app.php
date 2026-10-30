<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.admin'   => \App\Http\Middleware\AdminMiddleware::class,
            'auth.anggota' => \App\Http\Middleware\AnggotaMiddleware::class,
            // Middleware baru: proteksi halaman verifikasi wajah
            'auth.wajah'   => \App\Http\Middleware\WajahMiddleware::class,
            // Middleware baru: pastikan status_akun = 'aktif' untuk anggota
            'akun.aktif'   => \App\Http\Middleware\AkunAktifMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
