<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"><meta name="csrf-token" content="{{ csrf_token() }}"><meta name="theme-color" content="#087f83"><title>@yield('title','Dashboard') · {{ config('app.name') }}</title><link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" sizes="96x96"><x-pwa-head/>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body>
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
<div class="sidebar-brand-row"><a class="brand-link" href="{{ route('dashboard') }}" aria-label="{{ config('app.name') }} — beranda"><x-brand/></a><button type="button" class="icon-button sidebar-close" id="sidebar-close" aria-label="Tutup menu"><x-icon name="close"/></button></div>
<div class="workspace-label"><span class="workspace-dot"></span> Sistem Pelayanan KB <span class="internal-tag">Internal</span></div>
<nav>
<p class="nav-caption">MENU UTAMA</p>
<a class="nav-item {{ request()->routeIs('dashboard')?'active':'' }}" href="{{ route('dashboard') }}"><x-icon name="dashboard"/>Dashboard</a>
@if(auth()->user()->canEdit())
<a class="nav-item {{ request()->routeIs('participants.*')?'active':'' }}" href="{{ route('participants.index') }}"><x-icon name="users"/>Data peserta</a>
<a class="nav-item {{ request()->routeIs('services.*')?'active':'' }}" href="{{ route('services.index') }}"><x-icon name="heart"/>Pelayanan KB</a>
<a class="nav-item {{ request()->routeIs('appointments.*')?'active':'' }}" href="{{ route('appointments.index') }}"><x-icon name="calendar"/>Jadwal kontrol</a>
@endif
<a class="nav-item {{ request()->routeIs('reports.*')?'active':'' }}" href="{{ route('reports.index') }}"><x-icon name="chart"/>Laporan & rekapitulasi</a>
@if(auth()->user()->role==='admin')
<p class="nav-caption">PENGELOLAAN</p>
<a class="nav-item {{ request()->is('master/regions*')?'active':'' }}" href="{{ route('master.index','regions') }}"><x-icon name="map"/>Data wilayah</a>
<a class="nav-item {{ request()->is('master/facilities*')?'active':'' }}" href="{{ route('master.index','facilities') }}"><x-icon name="building"/>Ruangan pelayanan</a>
<a class="nav-item {{ request()->routeIs('targets.*')?'active':'' }}" href="{{ route('targets.index') }}"><x-icon name="target"/>Target capaian</a>
<a class="nav-item {{ request()->routeIs('users.*')?'active':'' }}" href="{{ route('users.index') }}"><x-icon name="users"/>Manajemen pengguna</a>
<a class="nav-item {{ request()->routeIs('audit.*')?'active':'' }}" href="{{ route('audit.index') }}"><x-icon name="shield"/>Audit log</a>
@endif
</nav>
<div class="sidebar-bottom"><div class="privacy-note"><x-icon name="shield" size="22"/><strong>Data keluarga terlindungi</strong><p>Akses sesuai peran. Setiap aktivitas penting tercatat.</p></div><form method="post" action="{{ route('logout') }}">@csrf<button class="nav-item logout" type="submit"><x-icon name="log-out"/>Keluar dari akun</button></form><div class="sidebar-version">{{ config('app.name') }} <span>v1.0</span></div></div>
</aside>
<div class="app-shell"><header class="topbar"><div class="topbar-left"><button class="icon-button mobile-toggle" id="sidebar-toggle" aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false"><x-icon name="menu"/></button><span class="breadcrumb"><span class="breadcrumb-context">Ruang kerja</span><span class="breadcrumb-divider">/</span> <strong>@yield('title','Dashboard')</strong></span></div><div class="topbar-right"><span class="today"><x-icon name="calendar" size="16"/>{{ now()->translatedFormat('d F Y') }}</span><div class="profile"><span class="avatar">{{ mb_substr(auth()->user()->name,0,1) }}</span><div class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }}</small></div></div></div></header>
<main id="main-content" class="main-content">
@if(session('success'))<div class="alert alert-success" role="status"><x-icon name="check"/>{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error" role="alert"><x-icon name="info"/><div><strong>Periksa kembali isian Anda.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
<x-pwa-install/>
@yield('content')
<footer class="page-footer"><span>© {{ now()->year }} {{ config('app.name') }}. Sistem Informasi Keluarga Berencana.</span><span><x-icon name="lock" size="13"/> Akses internal terbatas</span></footer>
</main></div>
@stack('scripts')
</body></html>
