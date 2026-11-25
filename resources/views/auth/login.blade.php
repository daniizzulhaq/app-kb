<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#087f83">
    <title>Masuk · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" sizes="96x96">
    <x-pwa-head/>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<div class="login-page">
    <aside class="login-aside">
        <x-brand/>
        <div class="login-aside-content">
            <div class="institution-label">RSUD Luwuk <span>·</span> Kabupaten Banggai</div>
            <h1>Pelayanan terarah.<br>Keluarga <span>lebih sehat.</span></h1>
            <p>Sistem informasi pelayanan dan pelaporan Keluarga Berencana. Terintegrasi untuk mendukung pelayanan yang tertib, tepat, dan berkesinambungan.</p>
            <div class="login-features">
                <div><span class="login-feature-icon"><x-icon name="users" size="21"/></span><span><strong>Peserta terdata</strong><small>Identitas dan riwayat dalam satu tempat.</small></span></div>
                <div><span class="login-feature-icon"><x-icon name="heart" size="21"/></span><span><strong>Pelayanan terintegrasi</strong><small>Pencatatan tindakan dan kunjungan ulang.</small></span></div>
                <div><span class="login-feature-icon"><x-icon name="chart" size="21"/></span><span><strong>Capaian terpantau</strong><small>Rekapitulasi dan laporan setiap periode.</small></span></div>
            </div>
        </div>
        <footer><x-icon name="shield" size="17"/><span>Pelayanan profesional. Data keluarga terlindungi.</span></footer>
    </aside>
    <main class="login-main">
        <div class="login-box">
            <x-brand class="login-mobile-brand"/>
            <div class="login-access-label"><span></span>Akses internal rumah sakit</div>
            <h2>Selamat datang kembali</h2>
            <p>Masuk untuk mengelola pelayanan KB di {{ config('app.institution') }}.</p>
            @if($errors->any())
                <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('login.submit') }}">
                @csrf
                <x-field label="Username atau email resmi" name="login" autocomplete="username" placeholder="Masukkan username atau email resmi" maxlength="150" required autofocus/>
                <div class="field">
                    <label for="password">Kata sandi <span class="required">*</span></label>
                    <div class="password-input">
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required>
                        <button id="toggle-password" type="button" aria-label="Tampilkan kata sandi"><x-icon name="eye" size="19"/></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><x-icon name="lock" size="17"/>Masuk ke {{ config('app.name') }}</button>
            </form>
            <div class="login-note"><x-icon name="shield" size="18"/><span>Gunakan akun yang diberikan oleh administrator. Data peserta bersifat rahasia dan hanya dapat diakses sesuai peran.</span></div>
            <x-pwa-install/>
            <div class="login-copyright">© {{ now()->year }} {{ config('app.institution') }} <span>·</span> {{ config('app.name') }}</div>
        </div>
    </main>
</div>
</body>
</html>
