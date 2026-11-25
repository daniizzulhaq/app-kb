<div {{ $attributes->merge(['class'=>'brand']) }}>
    <span class="brand-crest">
        <img src="{{ asset('images/logo-banggai.png') }}" alt="Lambang Kabupaten Banggai" width="426" height="512" decoding="async">
    </span>
    <span class="brand-wordmark">{{ config('app.name') }}<span class="brand-sub">{{ config('app.institution') }}</span></span>
</div>
