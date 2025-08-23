const CACHE_NAME = "laporaja-v1";
const urlsToCache = [
    "/",
    "/favicon.ico",
    "/manifest.json",
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            // Use Promise.allSettled to handle files that might not exist
            return Promise.allSettled(
                urlsToCache.map(url => 
                    fetch(url).then(response => {
                        if (response.ok) {
                            return cache.put(url, response);
                        }
                        console.warn(`Failed to cache ${url}: ${response.status}`);
                    }).catch(error => {
                        console.warn(`Failed to fetch ${url}:`, error);
                    })
                )
            );
        })
    );
});

self.addEventListener("fetch", (event) => {
    // Only handle GET requests for caching
    if (event.request.method !== 'GET') {
        return;
    }
    
    event.respondWith(
        caches.match(event.request).then((response) => {
            if (response) {
                return response;
            }
            return fetch(event.request).then((response) => {
                if (
                    !response ||
                    response.status !== 200 ||
                    response.type !== "basic"
                ) {
                    return response;
                }
                const responseToCache = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseToCache);
                });
                return response;
            }).catch((error) => {
                console.warn('Fetch failed:', error);
                // Return a fallback response or let it fail silently
                return new Response('Network error', { status: 503 });
            });
        })
    );
});
