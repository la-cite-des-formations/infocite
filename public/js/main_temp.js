
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
        let showNotification = document.getElementById('notificationPush')
        showNotification.innerText = notification.message + notification.post
    });

