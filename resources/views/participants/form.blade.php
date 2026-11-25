@extends('layout')
@section('title',$participant->exists?'Edit peserta':'Daftarkan peserta')
@section('content')
<div class="form-container"><div class="page-heading"><div><div class="eyebrow">Data peserta KB</div><h1>{{ $participant->exists?'Edit data peserta':'Daftarkan peserta baru' }}</h1><p>Satu NIK untuk satu identitas dengan seluruh riwayat pelayanan.</p></div><a class="btn" href="{{ $participant->exists?route('participants.show',$participant):route('participants.index') }}">Kembali</a></div>
<form class="panel" method="post" action="{{ $participant->exists?route('participants.update',$participant):route('participants.store') }}">@csrf @if($participant->exists)@method('put')@endif
<div class="form-section"><h2>Identitas peserta</h2><p>Pastikan data sesuai dengan identitas resmi peserta.</p><div class="form-grid">
<x-field label="NIK" name="nik" :value="$participant->nik" inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16" help="16 digit NIK. Tidak boleh sama dengan peserta lain." required/>
<x-field label="Nama lengkap" name="name" :value="$participant->name" maxlength="150" required/>
<x-field label="Tanggal lahir" name="birth_date" type="date" :value="$participant->birth_date?->toDateString()" :max="now()->toDateString()" min="1900-01-02" required/>
<x-field label="Nama pasangan" name="spouse_name" :value="$participant->spouse_name" maxlength="150"/>
<x-field label="Jumlah anak" name="children_count" type="number" :value="$participant->children_count??0" min="0" max="30" required/>
<x-field label="Nomor HP" name="phone" type="tel" :value="$participant->phone" placeholder="08xxxxxxxxxx" maxlength="16" help="9–15 digit, boleh diawali tanda +."/>
</div></div><div class="form-section"><h2>Alamat & wilayah</h2><p>Wilayah digunakan untuk pengelompokan capaian pelayanan.</p><div class="form-grid">
<x-field label="Wilayah" name="region_id" :value="$participant->region_id" :options="$regions" required/>
<x-field label="Alamat lengkap" name="address" type="textarea" :value="$participant->address" maxlength="1000" required/>
</div><div class="sensitive-note"><x-icon name="lock" size="15"/>NIK, nama pasangan, alamat, dan nomor HP disimpan terenkripsi.</div></div><div class="form-actions"><a class="btn" href="{{ route('participants.index') }}">Batal</a><button class="btn btn-primary" type="submit"><x-icon name="check" size="16"/>{{ $participant->exists?'Simpan perubahan':'Simpan peserta' }}</button></div></form></div>
@endsection
