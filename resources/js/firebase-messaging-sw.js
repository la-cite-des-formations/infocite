import { initializeApp } from "firebase/app"
import { getMessaging, onBackgroundMessage } from "firebase/messaging/sw"

const firebaseConfig = {
    apiKey: "AIzaSyDZg55jzD_cg4b3CIlT90E_HAgGBDFsreU",
    authDomain: "notifs-ic.firebaseapp.com",
    projectId: "notifs-ic",
    storageBucket: "notifs-ic.firebasestorage.app",
    messagingSenderId: "692702740324",
    appId: "1:692702740324:web:b4c81b7ccddca1d14c8471"
}

const app = initializeApp(firebaseConfig)
const messaging = getMessaging(app)
const appName = process.env.MIX_APP_NAME;
const appFavicon = process.env.MIX_APP_FAVICON;
const appUrl = process.env.MIX_APP_URL;

// Gérer la réception de messages en arrière-plan
onBackgroundMessage(messaging, (payload) => {
    console.log('Message reçu en arrière-plan:', payload)

    const { title, body, icon, url } = payload.data || {}

    self.registration.showNotification(title || appName, {
        body: body || '',
        icon: icon || appFavicon,
        data: {
            link: url || appUrl
        }
    })
})

// Gestion du clic sur la notification
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const url = event.notification.data.link;

    if (url) {
        event.waitUntil(clients.openWindow(url));
    }
});
