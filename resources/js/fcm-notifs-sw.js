import { initializeApp } from "firebase/app"
import { getMessaging, getToken, onMessage } from "firebase/messaging"

const firebaseConfig = {
    apiKey: "AIzaSyDZg55jzD_cg4b3CIlT90E_HAgGBDFsreU",
    authDomain: "notifs-ic.firebaseapp.com",
    projectId: "notifs-ic",
    storageBucket: "notifs-ic.firebasestorage.app",
    messagingSenderId: "692702740324",
    appId: "1:692702740324:web:b4c81b7ccddca1d14c8471"
};

const app = initializeApp(firebaseConfig)
const messaging = getMessaging(app)
const fcmVapidKey = process.env.MIX_FCM_VAPID_KEY;
const appName = process.env.MIX_APP_NAME;
const appFavicon = process.env.MIX_APP_FAVICON;
const appUrl = process.env.MIX_APP_URL;

// Enregistrer le Service Worker pour FCM
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/firebase-messaging-sw.js', { type: 'module' })
        .then((registration) => {
            console.log('Service Worker enregistré avec succès:', registration)

            // Note : Pour la réception des notifications, Firebase Messaging exige généralement la configuration d'une clé VAPID
            getToken(messaging, { fcmVapidKey })
                .then((fcmToken) => {
                    if (fcmToken) {
                        console.log('Token FCM récupéré et transmis au serveur');
                        Livewire.emit('traitFcmToken', fcmToken)
                    } else {
                        console.warn('Aucun token n’a été généré. Autorisation refusée !')
                    }
                })
                .catch((err) => {
                    console.error('Erreur lors de la récupération du token FCM:', err)
                });
        })
        .catch((err) => console.error('Erreur lors de l’enregistrement du Service Worker:', err))
}

// message reçu quand l'application est active en premier plan
onMessage(messaging, (payload) => {
    console.log('Message reçu en premier plan :', payload)

    // Afficher une notification via l'API Notification (si l'utilisateur a accordé la permission)
    if (Notification.permission === 'granted') {
        const { title, body, icon, url } = payload.data || {}

        let notif = new Notification(title || appName, {
            body: body || '',
            icon: icon || appFavicon,
            data: {
                link: url || appUrl,
            }
        })

        notif.onclick = function(event) {
            event.preventDefault(); // Empêche l'action de clic par défaut

            if (notif.data.link) {
                window.open(notif.data.link, '_blank');
            }
        }
    }
})
