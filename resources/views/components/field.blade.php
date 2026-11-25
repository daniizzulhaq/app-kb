@props(['label','name','value'=>null,'type'=>'text','options'=>null,'required'=>false,'help'=>null])
<div {{ $attributes->only('class')->merge(['class'=>'field']) }}>
<label for="{{ $name }}">{{ $label }} @if($required)<span class="required">*</span>@endif</label>
@if($options!==null)
<select id="{{ $name }}" name="{{ $name }}" @required($required) {{ $attributes->except('class') }}>
<option value="">Pilih {{ mb_strtolower($label) }}</option>
@foreach($options as $key=>$text)<option value="{{ $key }}" @selected((string)old($name,$value)===(string)$key)>{{ $text }}</option>@endforeach
</select>
@elseif($type==='textarea')
<textarea id="{{ $name }}" name="{{ $name }}" rows="3" @required($required) {{ $attributes->except('class') }}>{{ old($name,$value) }}</textarea>
@else
<input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $type==='password'?'':old($name,$value) }}" @required($required) {{ $attributes->except('class') }}>
@endif
@if($help)<p class="field-help">{{ $help }}</p>@endif
@error($name)<p class="field-error">{{ $message }}</p>@enderror
</div>
