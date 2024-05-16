
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

document.getElementById('paginationContainer').addEventListener('click', function() {
    scrollToAnchorWithOffset('scrollToResult', 100);
    console.log('paginationContainer')
});

document.getElementById('filterContainer').addEventListener('click', function() {
    scrollToAnchorWithOffset('scrollToResult', 450);
    console.log('filterContainer')
});


