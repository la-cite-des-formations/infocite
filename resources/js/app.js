require('./bootstrap')

document.addEventListener('livewire:load', function() {
    Livewire.emit('loadPermission', Notification.permission)

    Livewire.on('verifyPermission', () => {
        console.log("Permission actuelle du navigateur : ", Notification.permission)

        Livewire.emit('updatePermission', Notification.permission)
    })
})
