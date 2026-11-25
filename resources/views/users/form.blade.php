@extends('layout')
@section('title',$user->exists?'Edit pengguna':'Tambah pengguna')
@section('content')
<div class="form-container"><div class="page-heading"><div><div class="eyebrow">Pengaturan akses</div><h1>{{ $user->exists?'Edit':'Tambah' }} pengguna</h1><p>Berikan akses sesuai tanggung jawab pengguna.</p></div><a class="btn" href="{{ route('users.index') }}">Kembali</a></div>
<div class="info-strip"><x-icon name="shield" size="18"/><span><strong>Admin:</strong> seluruh data dan pengaturan. <strong>Petugas:</strong> pencatatan peserta, pelayanan, jadwal, dan laporan. <strong>Pimpinan:</strong> dashboard dan laporan agregat.</span></div>
<form class="panel" method="post" action="{{ $user->exists?route('users.update',$user):route('users.store') }}">@csrf @if($user->exists)@method('put')@endif<div class="form-section"><div class="form-grid">
<x-field label="Nama lengkap" name="name" :value="$user->name" maxlength="150" required/>
<x-field label="Username" name="username" :value="$user->username" minlength="3" maxlength="50" help="3–50 karakter: huruf kecil, angka, titik, tanda hubung, atau garis bawah." required/>
<x-field label="Email resmi" name="email" type="email" :value="$user->email" maxlength="150" help="Opsional. Isi dengan email pengguna yang sebenarnya."/>
<x-field label="Peran pengguna" name="role" :value="$user->role??'petugas'" :options="['admin'=>'Admin','petugas'=>'Petugas','pimpinan'=>'Pimpinan']" required/>
<x-field label="Status akun" name="is_active" :value="$user->exists?(int)$user->is_active:1" :options="[1=>'Aktif',0=>'Nonaktif']" required/>
<x-field label="Kata sandi" name="password" type="password" autocomplete="new-password" minlength="10" maxlength="128" :required="!$user->exists" :help="$user->exists?'Kosongkan jika tidak diubah. Minimal 10 karakter.':'Minimal 10 karakter.'"/>
<x-field label="Konfirmasi kata sandi" name="password_confirmation" type="password" autocomplete="new-password" minlength="10" maxlength="128" :required="!$user->exists"/>
</div></div><div class="form-actions"><a class="btn" href="{{ route('users.index') }}">Batal</a><button class="btn btn-primary"><x-icon name="check" size="16"/>Simpan pengguna</button></div></form></div>
@endsection
