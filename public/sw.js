const CACHE_NAME = "1.0.1";
const urlsToCache = [
    "/",
    "/home",
    "/produk",
    "/outlet",
    "/visit",
    "/public/manifest.json",
    "/build/assets/app.css",
    "/build/assets/app.js",
    "/icons/192.png",
    // Tambahkan asset lain yang sering dipakai
];

// Install Service Worker
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(urlsToCache))
    );
});

// Fetch — pakai cache dulu, kalau gagal baru ke network
self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});

// Sync Background — simpan data kunjungan saat offline
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
            db.createObjectStore("visits", { keyPath: "id" });
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}
