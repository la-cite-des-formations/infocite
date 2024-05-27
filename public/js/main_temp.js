
function scrollToAnchorWithOffset(anchorID, offset) {
    let target = document.getElementById(anchorID)
    if (target) {
        let targetPosition = target.offsetTop - offset
        window.scrollTo({
            top: targetPosition,
            behavior: "smooth"
        });
    }
}

if(document.getElementById('paginationContainer')){
    document.getElementById('paginationContainer').addEventListener('click', function() {
        scrollToAnchorWithOffset('scrollToResult', 100);
        console.log('paginationContainer')
    });
}

Echo.channel(`notificationPostChannel.${window.userId}`)
    .listen('NotificationPusher', (notification) => {
        let notificationElement = document.getElementById('notificationPush')
        let notificationTitle = document.getElementById('notifTitle')
        let notificationBody = document.getElementById('notifBody')
        let elapsedTimeElement = document.getElementById('time')
        let postRedirectElement = document.getElementById('postRedirect')

        notificationTitle.innerText = notification.message
        notificationBody.innerText = notification.post_title
        postRedirectElement.href = notification.href

        // Enregistrer l'heure de réception de la notification
        let receivedTime = new Date();

        // Fonction pour mettre à jour le temps écoulé
        function updateElapsedTime() {
            let now = new Date();
            let elapsed = Math.floor((now - receivedTime) / 1000); // temps écoulé en secondes
            let minutes = Math.floor(elapsed / 60);
            elapsedTimeElement.innerText = `Il y a ${minutes} mins`;
        }

        // Mettre à jour le temps écoulé toutes les secondes
        setInterval(updateElapsedTime, 60);

        let notificationPush = new bootstrap.Toast(notificationElement, {
            autohide: false
        });
        notificationPush.show();
        console.log('test')
    });
