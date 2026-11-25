@extends('layout')
@section('title',$service->exists?'Edit pelayanan':'Catat pelayanan')
@section('content')
<div class="form-container"><div class="page-heading"><div><div class="eyebrow">Pelayanan KB</div><h1>{{ $service->exists?'Edit pelayanan':'Catat pelayanan baru' }}</h1><p>Lengkapi tindakan dan jadwal kontrol peserta.</p></div><a class="btn" href="{{ route('services.index') }}">Kembali</a></div>
<div class="info-strip"><x-icon name="info" size="18"/><span>Jenis peserta baru/lama ditentukan otomatis dari urutan tanggal pelayanan. Satu peserta dapat memiliki banyak riwayat tanpa menggandakan identitas.</span></div>
<form class="panel" method="post" action="{{ $service->exists?route('services.update',$service):route('services.store') }}">@csrf @if($service->exists)@method('put')@endif
<div class="form-section"><h2>Peserta pelayanan</h2><p>Cari peserta terdaftar atau daftarkan identitas terlebih dahulu.</p>
@if($participant)
<input type="hidden" name="participant_id" value="{{ $participant->id }}"><div class="selected-participant"><strong>{{ $participant->name }}</strong><span class="table-sub">{{ $participant->masked_nik }} · {{ $participant->region->name }}</span></div>
@else
@php($chosen=old('participant_id')?\App\Models\Participant::find(old('participant_id')):null)
<div class="field lookup"><label for="participant-search">Cari peserta <span class="required">*</span></label><input id="participant-search" autocomplete="off" placeholder="Ketik minimal 2 huruf nama atau NIK lengkap…" value="{{ $chosen?->name }}" data-url="{{ route('participants.lookup') }}" role="combobox" aria-controls="participant-results" aria-autocomplete="list"><input id="participant_id" type="hidden" name="participant_id" value="{{ $chosen?->id }}"><div class="lookup-results" id="participant-results" role="listbox" hidden></div></div><div class="selected-participant" id="selected-participant" {{ $chosen?'':'hidden' }}>{{ $chosen?->name }} {{ $chosen?->masked_nik }}</div><p class="field-help" style="margin-top:12px">Belum terdaftar? <a href="{{ route('participants.create') }}" class="link">Daftarkan peserta baru</a>.</p>
@endif
</div><div class="form-section"><h2>Detail pelayanan</h2><p>Pelayanan yang tersimpan langsung masuk ke dashboard dan rekapitulasi.</p><div class="form-grid">
<x-field label="Tanggal pelayanan" name="service_date" type="date" :value="$service->service_date?->toDateString()??now()->toDateString()" :max="now()->toDateString()" required/>
<x-field label="Metode KB" name="method" :value="$service->method" :options="array_combine(\App\Models\Service::METHODS,\App\Models\Service::METHODS)" required/>
<x-field label="Jenis tindakan" name="action" :value="$service->action" :options="array_combine(\App\Models\Service::ACTIONS,\App\Models\Service::ACTIONS)" required/>
<x-field label="Ruangan pelayanan" name="facility_id" :value="$service->facility_id" :options="$facilities" required/>
@if(auth()->user()->role==='admin')<x-field label="Petugas pelayanan" name="officer_id" :value="$service->officer_id??auth()->id()" :options="$officers" required/>@else<input type="hidden" name="officer_id" value="{{ $service->officer_id??auth()->id() }}"><div class="field"><label for="officer-name">Petugas pelayanan</label><input id="officer-name" value="{{ $service->exists?$service->officer->name:auth()->user()->name }}" readonly><p class="field-help">{{ $service->exists?'Identitas petugas pada riwayat dipertahankan.':'Dicatat sebagai petugas yang sedang masuk.' }}</p></div>@endif
<x-field label="Jadwal kontrol berikutnya" name="next_visit_date" type="date" :value="$service->next_visit_date?->toDateString()" help="Opsional. Harus setelah tanggal pelayanan."/>
<x-field label="Catatan pelayanan" name="notes" type="textarea" :value="$service->notes" maxlength="2000" class="col-span-2" placeholder="Catatan tindakan, keluhan, atau informasi tindak lanjut…"/>
</div></div><div class="form-actions"><a class="btn" href="{{ route('services.index') }}">Batal</a><button class="btn btn-primary" type="submit"><x-icon name="check" size="16"/>Simpan pelayanan</button></div></form></div>
@endsection
