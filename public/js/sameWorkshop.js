
const observer = new MutationObserver((mutationsList, observer) => {
    checkDupe();
});

observer.observe(document.body, { childList: true, subtree: true });

function checkDupe() {
    var rounds = document.querySelector('.main .rounds');
    var workshops = rounds.querySelectorAll('.workshop')

    if (!workshops) return;

    var allTitles = []
    workshops.forEach(workshop => {
        var text = workshop.querySelector('.title').innerHTML;

        if (!text) return;

        var truncatedText = text.substring(0, 13) + text.substring(15, 20);

        allTitles.push(truncatedText)
    })

    const hasDuplicates = allTitles.some((item, index) => {
        const firstIndex = allTitles.indexOf(item);
        if (firstIndex !== index) {
            var closeIcon = workshops[index].querySelector('.close-button');
            if (!closeIcon) return;
            
            showErrorPopup("Je hebt deze workshop al in een andere workshop geselecteerd!")
            closeIcon.click()
        }
        return false;
    });
}