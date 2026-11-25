const CACHE_NAME = 'si-kb-rs-public-v1';
const BASE = new URL('./', self.location.href).pathname;
const OFFLINE_URL = `${BASE}offline.html`;
const PUBLIC_FILES = [
  OFFLINE_URL,
  `${BASE}favicon.png`,
  `${BASE}images/logo-banggai.png`,
  `${BASE}icons/icon-192.png`,
  `${BASE}icons/icon-512.png`,
  `${BASE}icons/icon-maskable-512.png`,
  `${BASE}icons/apple-touch-icon.png`,
];

self.addEventListener('install', event => {
  event.waitUntil((async () => {
    const cache = await caches.open(CACHE_NAME);
    await cache.addAll(PUBLIC_FILES);
    await self.skipWaiting();
  })());
});

self.addEventListener('activate', event => {
  event.waitUntil((async () => {
    const names = await caches.keys();
    await Promise.all(names.filter(name => name.startsWith('si-kb-rs-public-') && name !== CACHE_NAME).map(name => caches.delete(name)));
    await self.clients.claim();
  })());
});

async function publicAsset(request) {
  const cache = await caches.open(CACHE_NAME);
  const saved = await cache.match(request);
  if (saved) return saved;
  const response = await fetch(request);
  if (response.ok && response.type === 'basic') {
    await cache.put(request, response.clone());
    // Retain a small number of immutable Vite files across application updates.
    const assets = (await cache.keys()).filter(item => new URL(item.url).pathname.startsWith(`${BASE}build/assets/`));
    await Promise.all(assets.slice(0, Math.max(0, assets.length - 12)).map(item => cache.delete(item)));
  }
  return response;
}

self.addEventListener('fetch', event => {
  const request = event.request;
  const url = new URL(request.url);
  if (request.method !== 'GET' || url.origin !== self.location.origin) return;

  if (request.mode === 'navigate') {
    // Authenticated HTML always comes from the server; never cache identities,
    // sessions, dashboards, searches, reports, or submitted form data.
    event.respondWith(fetch(request, { cache: 'no-store' }).catch(async () => {
      const cached = await caches.match(OFFLINE_URL);
      const html = cached ? await cached.text() : '<h1>Koneksi internet terputus</h1><p>Sambungkan kembali internet untuk menggunakan SI KB RS.</p>';
      return new Response(html.replaceAll('__PWA_BASE__', BASE), {
        status: 200,
        headers: { 'Content-Type': 'text/html; charset=utf-8', 'Cache-Control': 'no-store' },
      });
    }));
    return;
  }

  const isBuildFile = url.pathname.startsWith(`${BASE}build/assets/`) && /\.(css|js|woff2?)$/.test(url.pathname);
  if (!url.search && (PUBLIC_FILES.includes(url.pathname) || isBuildFile)) event.respondWith(publicAsset(request));
});
