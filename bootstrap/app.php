<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // The local ngrok agent forwards the original HTTPS scheme and host.
        $middleware->trustProxies(
            at: ['127.0.0.1', '::1'],
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );
        $middleware->alias(['role' => \App\Http\Middleware\EnsureRole::class]);
        $middleware->web(append: [\App\Http\Middleware\SecureHeaders::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            $code=$e->errorInfo[1]??null;
            if (in_array($code,[1062,1451,1452],true)) {
                $message=$code===1062?'Data unik tersebut sudah digunakan. Periksa NIK, email, atau kode yang Anda masukkan.':'Data masih digunakan atau referensinya telah berubah. Muat ulang dan periksa kembali.';
                if($request->expectsJson()) return response()->json(['message'=>$message],422);
                return back()->withErrors(['database'=>$message])->withInput($request->except(['password','password_confirmation','_token']));
            }
        });
    })->create();
