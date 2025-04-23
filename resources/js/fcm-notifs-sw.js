import { initializeApp } from "firebase/app"
import { getMessaging, getToken, onMessage } from "firebase/messaging"

document.addEventListener('livewire:load', function() {
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

    const fcmVapidKey = process.env.MIX_FCM_VAPID_KEY
    const appName = process.env.MIX_APP_NAME
    const appFavicon = process.env.MIX_APP_FAVICON
    const appUrl = process.env.MIX_APP_URL

    // Traitement du souhait de l'utilisateur après avoir lu les directives techniques
    const askNotificationPermission = () => {
        Notification.requestPermission().then(permission => {
            console.log("Notification permission demandée:", permission)

            // Transmettre la réponse à Livewire pour enregistrer l'état côté serveur
            Livewire.emit('registerNotificationPermission', permission)

            if (permission === 'granted') {
                registerServiceWorkerAndToken();
            }
        })
    }

    // Fonction de récupération du token et transmission au backend via Livewire
    const registerServiceWorkerAndToken = () => {
        // Enregistrer le Service Worker pour FCM
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/firebase-messaging-sw.js', { type: 'module' })
                .then((registration) => {
                    console.log('Service Worker enregistré avec succès:', registration)

                    getToken(messaging, { fcmVapidKey })
                        .then((fcmToken) => {
                            if (fcmToken) {
                                console.log('Token FCM récupéré et transmis au serveur')

                                Livewire.emit('traitFcmToken', fcmToken);
                            } else {
                                console.warn('Aucun token n’a été généré. Autorisation refusée !')
                            }
                        })
                        .catch((err) => {
                            console.error('Erreur lors de la récupération du token FCM:', err)
                        })
                })
                .catch((err) => console.error('Erreur lors de l’enregistrement du Service Worker:', err))
        }
    }

    // Gestion de la permission de notification
    if (Notification.permission === 'default') {
        // Si la permission est encore par défaut, afficher la modale pour informer l'utilisateur
        console.log("Permission en état 'default'")

        Livewire.emit('showModal', 'guidelines', { subject: 'desktop-notifications' })
    }

    if (Notification.permission === 'granted') {
        // Si la permission est déjà accordée, on récupère et envoie directement le token
        console.log("Permission déjà accordée : 'granted'")

        registerServiceWorkerAndToken()
    }

    if (Notification.permission === 'denied') {
        console.log("Permission refusée : 'denied'")

        Livewire.emit('registerNotificationPermission', 'denied')
    }

    // Demander la permission de notification une fois que l'utilisateur a pris connaissance des directives techniques associées
    Livewire.on('guidelinesRead', subject => {
        console.log("Sujet des directives : ", subject)

        if (subject === 'desktop-notifications') {
            askNotificationPermission()
        }
    })

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
                event.preventDefault() // Empêche l'action de clic par défaut

                if (notif.data.link) {
                    window.open(notif.data.link, '_blank')
                }
            }
        }
    })
})
