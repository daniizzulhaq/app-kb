@extends('layout')
@section('title',($item->exists?'Edit ':'Tambah ').($kind==='regions'?'wilayah':'ruangan pelayanan'))
@section('content')
@php($label=$kind==='regions'?'wilayah':'ruangan pelayanan')
<div class="form-container"><div class="page-heading"><div><div class="eyebrow">Data master</div><h1>{{ $item->exists?'Edit':'Tambah' }} {{ $label }}</h1><p>Data digunakan bersama oleh seluruh modul pelayanan.</p></div><a class="btn" href="{{ route('master.index',$kind) }}">Kembali</a></div><form class="panel" method="post" action="{{ $item->exists?route('master.update',[$kind,$item->id]):route('master.store',$kind) }}">@csrf @if($item->exists)@method('put')@endif<div class="form-section"><div class="form-grid"><x-field :label="'Nama '.$label" name="name" :value="$item->name" maxlength="150" required/>
@if($kind==='regions')<x-field label="Kode wilayah" name="code" :value="$item->code" maxlength="30" required/>
@else
<x-field label="Wilayah" name="region_id" :value="$item->region_id" :options="$regions" required/>
<x-field label="Jenis ruangan" name="type" :value="$item->type" :options="array_combine(\App\Models\Facility::TYPES,\App\Models\Facility::TYPES)" required/>
<x-field label="Lokasi ruangan" name="address" type="textarea" :value="$item->address" maxlength="255"/>
@endif
</div></div><div class="form-actions"><a class="btn" href="{{ route('master.index',$kind) }}">Batal</a><button class="btn btn-primary"><x-icon name="check" size="16"/>Simpan {{ $label }}</button></div></form></div>
@endsection
