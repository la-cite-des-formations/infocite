

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

        Toastify({
            text: `${notification.message} ${notification.post_title} `,
            duration: -1,
            destination: notification.href,
            newWindow: false,
            close: true,
            gravity: "bottom", // `top` or `bottom`
            position: "right", // `left`, `center` or `right`
            stopOnFocus: true, // Prevents dismissing of toast on hover
            style: {
                color :"var(--select-color-11)",
                background: "linear-gradient(to right, var(--select-color-10),var(--select-color-16),var(--select-color-11))",
            },
        }).showToast()

        let originalTitle = document.title
        let newTilte = "New notification"
        let blink = true

        let interval = setInterval(()=>{
            document.title = blink ? newTilte : originalTitle
            blink = !blink
        }, 1000)

        //Stop blink quand l'utilisateur focus la page
        window.addEventListener('focus',()=>{
            clearInterval(interval);
            document.title = originalTitle
        });
    });

// Echo.channel(`notificationPostChannel.${window.userId}`)
//     .listen('NotificationPusher', (notification) => {
//
//         //Edition de la notification
//         let notificationElement = document.getElementById('notificationPush')
//         let notificationTitle = document.getElementById('notifTitle')
//         let notificationBody = document.getElementById('notifBody')
//         let elapsedTimeElement = document.getElementById('time')
//         let postRedirectElement = document.getElementById('postRedirect')
//
//         notificationTitle.innerText =''
//         notificationBody.innerText =''
//         postRedirectElement.href = ''
//         elapsedTimeElement.innerText =''
//
//         notificationTitle.innerText = notification.message
//         notificationBody.innerText = notification.post_title
//         postRedirectElement.href = notification.href
//
//         // Horodatage de la notification
//         let receivedTime = new Date();
//
//         function updateElapsedTime() {
//             let now = new Date();
//             let elapsed = Math.floor((now - receivedTime) / 1000); // temps écoulé en secondes
//             let minutes = Math.floor(elapsed / 60);
//             elapsedTimeElement.innerText = `Il y a ${minutes} mins`;
//         }
//
//         setInterval(updateElapsedTime, 60);
//
//         //création de la notification
//         let notificationPush = new bootstrap.Toast(notificationElement, {
//             autohide: false
//         });
//
//         //Affichage de la notification et modification du titre de l'onglet
//         notificationPush.show();
//
//         let originalTitle = document.title
//         let newTilte = "New notification"
//         let blink = true
//
//         let interval = setInterval(()=>{
//             document.title = blink ? newTilte : originalTitle
//             blink = !blink
//         }, 1000)
//
//         //Stop blink quand l'utilisateur focus la page
//         window.addEventListener('focus',()=>{
//             clearInterval(interval);
//             document.title = originalTitle
//         })
//
//
//     });







