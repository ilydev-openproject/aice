const CACHE_NAME = "1.0.2";
const urlsToCache = [
    "/", // home
    "/outlet", // route outlet
    "/visit", // route visit
    "/produk", // route produk
    "/manifest.json", // manifest
    "/build/assets/app.css",
    "/build/assets/app.js",
    "/icons/192.png",
    "/offline.html", // fallback offline page
];

// Install Service Worker
self.addEventListener("activate", (event) => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (!cacheWhitelist.includes(cacheName)) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Fetch event - Intersep permintaan jaringan
self.addEventListener("fetch", (event) => {
    event.respondWith(
        // Coba ambil dari cache dulu
        caches.match(event.request).then((response) => {
            // Jika ditemukan di cache, kembalikan
            if (response) {
                return response;
            }
            // Jika tidak ada di cache, ambil dari jaringan
            return fetch(event.request).then((response) => {
                // Periksa apakah kita mendapat respons yang valid
                if (
                    !response ||
                    response.status !== 200 ||
                    response.type !== "basic"
                ) {
                    return response;
                }

                var responseToCache = response.clone();

                caches.open(CACHE_NAME).then((cache) => {
                    // Hanya cache permintaan GET
                    if (event.request.method === "GET") {
                        cache.put(event.request, responseToCache);
                    }
                });

                return response;
            });
        })
    );
});
