<?php
namespace App\Http\Controllers;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
class AuthController extends Controller {
    public function login() { return view('auth.login'); }
    public function authenticate(Request $r) {
        $data=$r->validate(['login'=>'required|string|max:150','password'=>'required|string|max:128']);
        $login=Str::lower(trim($data['login']));
        $key='login:'.hash('sha256',$login.'|'.$r->ip());
        if (RateLimiter::tooManyAttempts($key,5)) return back()->withErrors(['login'=>'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.'])->onlyInput('login');
        $field=str_contains($login,'@')?'email':'username';
        if (!Auth::attempt([$field=>$login,'password'=>$data['password'],'is_active'=>true])) { RateLimiter::hit($key,60); return back()->withErrors(['login'=>'Username/email atau kata sandi tidak sesuai, atau akun tidak aktif.'])->onlyInput('login'); }
        RateLimiter::clear($key); $r->session()->regenerate(); AuditLog::record('Login','Pengguna',auth()->id());
        return redirect()->intended(route('dashboard'));
    }
    public function logout(Request $r) { AuditLog::record('Logout','Pengguna',auth()->id()); Auth::logout(); $r->session()->invalidate(); $r->session()->regenerateToken(); return redirect()->route('login'); }
}
