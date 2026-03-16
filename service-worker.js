const CACHE_NAME = 'mylab-pwa-v1';
const ASSETS = [
  '/',
  '/index.php',
  '/style.css',
  '/script.js',
  '/manifest.json',
  'https://cdn.tailwindcss.com',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

// Install Event
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('Caching essential assets');
      return cache.addAll(ASSETS);
    })
  );
});

// Activate Event
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.filter(key => key !== CACHE_NAME)
            .map(key => caches.delete(key))
      );
    })
  );
});

// Fetch Event
self.addEventListener('fetch', event => {
  // Only handle GET requests
  if (event.request.method !== 'GET') return;

  event.respondWith(
    caches.match(event.request).then(cachedResponse => {
      return cachedResponse || fetch(event.request).then(fetchResponse => {
        // Optional: Cache new requests on the fly (uncomment if needed)
        // return caches.open(CACHE_NAME).then(cache => {
        //   cache.put(event.request, fetchResponse.clone());
        //   return fetchResponse;
        // });
        return fetchResponse;
      });
    }).catch(() => {
      // Offline fallback can be added here if we have an offline.php page
    })
  );
});
