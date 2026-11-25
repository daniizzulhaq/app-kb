@props(['title'=>'Belum ada data','text'=>'Data yang sesuai akan muncul di sini.'])
<div class="empty"><span class="empty-icon"><x-icon name="clipboard" size="28"/></span><h3>{{ $title }}</h3><p>{{ $text }}</p>{{ $slot }}</div>
