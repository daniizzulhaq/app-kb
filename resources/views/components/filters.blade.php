@props(['f','extended'=>false])
<div class="filter-fields">
<div class="field"><label for="month">Bulan</label><select id="month" name="month"><option value="">Semua bulan</option>@foreach(\App\Services\Statistics::months() as $i=>$label)<option value="{{ $i+1 }}" @selected(($f['month']??null)===$i+1)>{{ $label }}</option>@endforeach</select></div>
<div class="field"><label for="year">Tahun</label><input type="number" id="year" name="year" value="{{ $f['year'] }}" min="2000" max="2100" required></div>
@if($extended)
<div class="field"><label for="region_id">Wilayah peserta</label><select id="region_id" name="region_id"><option value="">Semua wilayah</option>@foreach(\App\Models\Region::orderBy('name')->pluck('name','id') as $id=>$name)<option value="{{ $id }}" @selected(($f['region_id']??null)==$id)>{{ $name }}</option>@endforeach</select></div>
<div class="field"><label for="facility_id">Ruangan pelayanan</label><select id="facility_id" name="facility_id"><option value="">Semua ruangan</option>@foreach(\App\Models\Facility::orderBy('name')->pluck('name','id') as $id=>$name)<option value="{{ $id }}" @selected(($f['facility_id']??null)==$id)>{{ $name }}</option>@endforeach</select></div>
<div class="field"><label for="method">Metode KB</label><select id="method" name="method"><option value="">Semua metode</option>@foreach(\App\Models\Service::METHODS as $method)<option @selected(($f['method']??null)===$method)>{{ $method }}</option>@endforeach</select></div>
@endif
<button class="btn btn-primary" type="submit">Terapkan</button>
</div>
