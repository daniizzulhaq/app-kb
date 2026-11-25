@props(['name','size'=>20])
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" {{ $attributes->merge(['aria-hidden'=>'true']) }}>
@switch($name)
@case('dashboard')<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>@break
@case('users')<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m20 0v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/><circle cx="9" cy="7" r="4"/>@break
@case('user-plus')<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m18-13v6m-3-3h6"/><circle cx="9" cy="7" r="4"/>@break
@case('heart')<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/><path d="M3 12h4l2-4 4 8 2-4h6"/>@break
@case('clipboard')<rect x="5" y="4" width="14" height="17" rx="2"/><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M9 11h6m-6 5h6"/>@break
@case('calendar')<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18m-13 4h2m4 0h2"/>@break
@case('map')<path d="m9 18-6 3V6l6-3 6 3 6-3v15l-6 3-6-3Zm0 0V3m6 18V6"/>@break
@case('building')<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 7h2m2 0h2m-6 4h2m2 0h2m-6 10v-6h6v6M3 21h18"/>@break
@case('target')<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>@break
@case('chart')<path d="M3 3v18h18M8 16v-4m5 4V7m5 9v-7"/>@break
@case('shield')<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"/><path d="m8 12 3 3 5-6"/>@break
@case('log-out')<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5M21 12H9"/>@break
@case('search')<circle cx="10.5" cy="10.5" r="7"/><path d="m16 16 5 5"/>@break
@case('plus')<path d="M12 5v14M5 12h14"/>@break
@case('download')<path d="M12 3v12m-4-4 4 4 4-4M5 16v4h14v-4"/>@break
@case('chevron')<path d="m6 9 6 6 6-6"/>@break
@case('menu')<path d="M4 6h16M4 12h16M4 18h16"/>@break
@case('close')<path d="m6 6 12 12M6 18 18 6"/>@break
@case('check')<path d="m5 12 4 4L19 6"/>@break
@case('edit')<path d="m16 3 5 5L8 21H3v-5L16 3Zm-2 2 5 5"/>@break
@case('trash')<path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7"/>@break
@case('eye')<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>@break
@case('clock')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@break
@case('info')<circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10v.01"/>@break
@case('lock')<rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 5v2"/>@break
@case('mail')<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/>@break
@case('trend')<path d="m3 17 6-6 4 4 8-10m-6 0h6v6"/>@break
@case('print')<path d="M6 9V3h12v6M6 17H3v-8h18v8h-3M6 14h12v7H6v-7Z"/>@break
@default<circle cx="12" cy="12" r="8"/>
@endswitch
</svg>
