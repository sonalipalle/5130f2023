const CACHE_NAME = 'my-pwa-cache-v1';
const urlsToCache = [
  '/',
  '/index.html',
  '/welcome.html',
  '/style.css',
  '/script.js',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(urlsToCache);
    })
  );
});

self.addEventListener('fetch', (event) => {
  // Check for the signup request
  if (event.request.url.endsWith('/signup') && event.request.method === 'POST') {
    event.respondWith(
      fetch(event.request).then((response) => {
        if (response.status === 201) {
          // Redirect to the new page (welcome.html) on successful sign-up
          return Response.redirect('/welcome.html', 302);
        }
        return response;
      })
    );
  } else {
    // Handle other fetch requests as usual
    event.respondWith(
      caches.match(event.request).then((response) => {
        return response || fetch(event.request);
      })
    );
  }
});

// Handle cache cleanup on activation
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
