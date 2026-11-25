<section class="pwa-install" data-pwa-install hidden aria-label="Pasang {{ config('app.name') }}">
    <img class="pwa-install-icon" src="{{ asset('icons/icon-192.png') }}" alt="" width="192" height="192">
    <div class="pwa-install-copy"><strong>SI KB RS di layar utama</strong><p>Buka pelayanan lebih mudah, langsung dari ikon aplikasi.</p><p class="pwa-ios-help" data-pwa-ios-help hidden>Di Safari, ketuk <strong>Bagikan</strong>, lalu pilih <strong>Tambahkan ke Layar Utama</strong>.</p></div>
    <button type="button" class="btn btn-soft" data-pwa-install-button hidden><x-icon name="download" size="16"/>Pasang</button>
    <button type="button" class="pwa-install-dismiss" data-pwa-dismiss aria-label="Sembunyikan petunjuk pemasangan"><x-icon name="close" size="16"/></button>
</section>
