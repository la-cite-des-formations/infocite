// Importer les scripts nécessaires depuis les CDN de Firebase
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js')
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js')

// Initialiser l’application Firebase dans le Service Worker
firebase.initializeApp({
    apiKey: "AIzaSyDZg55jzD_cg4b3CIlT90E_HAgGBDFsreU",
    authDomain: "notifs-ic.firebaseapp.com",
    projectId: "notifs-ic",
    storageBucket: "notifs-ic.firebasestorage.app",
    messagingSenderId: "692702740324",
    appId: "1:692702740324:web:b4c81b7ccddca1d14c8471"
})

const messaging = firebase.messaging()

// Gérer la réception de messages en arrière-plan
messaging.onBackgroundMessage((payload) => {
    console.log('Message reçu en arrière-plan:', payload)

    const { title, body, icon } = payload.data || {}

    self.registration.showNotification(title || 'Notification', {
        body: body || '',
        icon: icon || '/img/favicon.png'
    })
})
