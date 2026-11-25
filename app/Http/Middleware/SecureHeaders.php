<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SecureHeaders {
    public function handle(Request $request,Closure $next) {
        if ($request->user() && !$request->user()->is_active) { Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect()->route('login'); }
        $response=$next($request);
        $response->headers->set('X-Content-Type-Options','nosniff');
        $response->headers->set('X-Frame-Options','DENY');
        $response->headers->set('Referrer-Policy','same-origin');
        $response->headers->set('Cache-Control','no-store, private');
        return $response;
    }
}
