let originalWorkshopOrder = [];

function getWorkshopIdFromElement(workshopElement) {
    if (!workshopElement || !workshopElement.id) return "";
    return workshopElement.id.replace(/^workshop/, "");
}

function syncDesktopSaveInputsFromRounds() {
    [1, 2, 3].forEach((roundNumber) => {
        const roundElement = document.getElementById(String(roundNumber));
        const saveInput = document.getElementById(`save${roundNumber}`);
        if (!saveInput || !roundElement) return;

        const workshopInRound = roundElement.querySelector('.workshop');
        saveInput.value = getWorkshopIdFromElement(workshopInRound);
    });
}

// Event listener for enabling drops on a valid target
function allowDrop(ev) {
    ev.preventDefault();
}

// Event listener for dragging
function drag(ev) {
    if (window.innerWidth > 800) {
        customDrag(ev)
    }
    ev.dataTransfer.setData("text", ev.target.id);
}

// Event listener for dropping
function drop(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    const draggedElement = document.getElementById(data);
    const targetRound = ev.target.closest(".round");

    if (!targetRound) return;

    // if (draggedElement.id === "workshop0" && targetRound.id !== "1") {
    //     showErrorPopup("Theaterworkshop 1 kan alleen in Ronde 1 geplaatst worden.");
    //     return;
    // }

    // if (draggedElement.id === "workshop1" && targetRound.id !== "2") {
    //     showErrorPopup("Theaterworkshop 2 kan alleen in Ronde 2 geplaatst worden.");
    //     return;
    // }

    // if (draggedElement.id === "workshop2" && targetRound.id !== "3") {
    //     showErrorPopup("Theaterworkshop 3 kan alleen in Ronde 3 geplaatst worden.");
    //     return;
    // }

    // If no children are present, append the dragged element
    if (!targetRound.hasChildNodes()) {
        targetRound.appendChild(draggedElement);
        syncDesktopSaveInputsFromRounds();
        planningChanged = true;
        addCloseButton(draggedElement, targetRound);
        workshopsInRounds.add(draggedElement.id);
        checkWorkshopsInRounds();
    } else {
        // Handle swapping if there's an existing workshop in the target round
        oldWorkshop = targetRound.firstChild;
        oldRound = draggedElement.parentNode;

        if (targetRound.firstChild.id !== "workshop0" && targetRound.firstChild.id !== "workshop1" && targetRound.firstChild.id !== "workshop2") {
             targetRound.replaceChild(draggedElement, oldWorkshop);
             oldRound.appendChild(oldWorkshop);
             syncDesktopSaveInputsFromRounds();
             addCloseButton(draggedElement, targetRound)
             if (oldWorkshop.parentNode.id == 4) {
                 oldWorkshop.querySelector(".close-button").remove();
             }
         }
     }
    updateSaveButton();
}

// Custom drag element style and ghost image
function customDrag(event) {
    let ghostEl;

    const draggedElement = event.target;

    const title = draggedElement.querySelector('.title');

    if(draggedElement.tagName.toString() === 'IMG') return;

    ghostEl = event.target.cloneNode(true);
    ghostEl.classList.remove('hiddenText');
    ghostEl.classList.add('showText');

    ghostEl.classList.add("ghost");

    ghostEl.style.width = draggedElement.offsetWidth + 'px';
    ghostEl.style.height = draggedElement.offsetHeight + 'px';
    ghostEl.style.position = "absolute";

    document.body.appendChild(ghostEl);

    event.dataTransfer.setDragImage(ghostEl, ghostEl.offsetWidth / 2, ghostEl.offsetHeight / 2);

    // When drag ends
    draggedElement.addEventListener("dragend", () => {
        if (ghostEl && document.body.contains(ghostEl)) {
            document.body.removeChild(ghostEl);
        }
    });
}
