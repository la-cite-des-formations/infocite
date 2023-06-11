function onchoiceSelection(collectionSelector, selection) {

    const collection = document.querySelectorAll('.' + collectionSelector)

    switch (selection) {

        case 'all':
            collection.forEach(element => element.checked = true)
            break

        case 'none':
            collection.forEach(element => element.checked = false)
            break

        case 'reverse':
            collection.forEach(element => element.checked = !element.checked)
    }
}

function getSelectionIDs(collectionSelector) {
    return Array.from($('.' + collectionSelector + ':checked'), cbx => cbx.id)
}

window.addEventListener('load', () => {
    Livewire.on('setIndeterminateCbx', (idPfx, checkboxes) => {
        for (var key in checkboxes) {
            $('#' + idPfx + '-cbx-' + key).prop({indeterminate: checkboxes[key] === null})
        }
    })
    Livewire.on('resetCbx', collectionSelector => {
        onchoiceSelection(collectionSelector, 'none')
    })
})
