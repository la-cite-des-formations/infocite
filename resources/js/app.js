require('./bootstrap')

document.addEventListener('livewire:load', function() {
    Livewire.on('verifyPermission', () => {
        console.log("Vérification de la permission : ", Notification.permission)

        Livewire.emit('updatePermission', Notification.permission)
    })
})
