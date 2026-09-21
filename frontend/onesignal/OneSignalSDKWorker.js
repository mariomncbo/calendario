/* ============================================================
   CALENDARIO · Service worker de OneSignal
   Necesario para que el navegador muestre las notificaciones push
   incluso cuando la app no está abierta. Vive en una subcarpeta
   para no chocar con el worker de la PWA (service-worker.js).
   ============================================================ */

// Cargar el motor de OneSignal desde su CDN
importScripts("https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.sw.js");