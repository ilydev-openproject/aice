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
self.addEventListener("install", (event) => {
    event.waitUntil(
        (async () => {
            const cache = await caches.open(CACHE_NAME);
            await Promise.allSettled(urlsToCache.map((url) => cache.add(url)));
        })()
    );
    self.skipWaiting();
});

// Activate — bersihkan cache lama
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) =>
            Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            )
        )
    );
    self.clients.claim();
});

// Fetch — coba network dulu, kalau gagal fallback ke cache
self.addEventListener("fetch", (event) => {
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                const resClone = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, resClone);
                });
                return response;
            })
            .catch(() =>
                caches
                    .match(event.request)
                    .then((res) => res || caches.match("/offline.html"))
            )
    );
});

// Background Sync — simpan data kunjungan saat offline
self.addEventListener("sync", (event) => {
    if (event.tag === "sync-visits") {
        event.waitUntil(syncVisits());
    }
});

async function syncVisits() {
    const db = await openDB();
    const tx = db.transaction("visits", "readwrite");
    const store = tx.objectStore("visits");
    const visits = await store.getAll();

    for (const visit of visits) {
        try {
            const response = await fetch("/api/visits", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(visit),
            });

            if (response.ok) {
                await store.delete(visit.id);
            }
        } catch (error) {
            console.log("Sync failed, will retry later");
        }
    }
}

// IndexedDB — simpan data offline
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open("SalesTrackerDB", 1);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains("visits")) {
                db.createObjectStore("visits", {
                    keyPath: "id",
                    autoIncrement: true,
                });
            }
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}
