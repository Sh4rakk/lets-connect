// Initialize global variables safely
window.originalWorkshopOrder = window.originalWorkshopOrder || [];
window.workshopsInRounds = window.workshopsInRounds || new Set();
window.planningChanged = window.planningChanged || false;

function drag(event) {
    // Find the actual workshop element by traversing up from the target
    let workshopElement = event.target;
    if (!workshopElement.classList.contains('workshop')) {
        workshopElement = workshopElement.closest('.workshop');
        if (!workshopElement) return; // Exit if no workshop parent found
    }

    if (window.innerWidth > 800) {
        customDrag(event)
    }
    event.dataTransfer.setData("text", workshopElement.id);
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drop(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    const draggedElement = document.getElementById(data);
    const targetRound = ev.target.closest(".round");

    if (!draggedElement || !targetRound) return;

     // If no children are present, append the dragged element
     if (!targetRound.hasChildNodes()) {
         targetRound.appendChild(draggedElement);
         syncDesktopSaveInputsFromRounds();
         window.planningChanged = true;
         addCloseButton(draggedElement, targetRound);
         window.workshopsInRounds.add(draggedElement.id);
         checkWorkshopsInRounds();

        // Advance tutorial if we're on step 4 and a workshop was dropped in round 1
        if (typeof currentStepIndex !== 'undefined' && currentStepIndex === 3 && targetRound.id === '1') {
            if (typeof nextStep === 'function') {
                nextStep();
            }
        }
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

function customDrag(event) {
    let ghostEl;

    // Find the actual workshop element by traversing up from the target
    let draggedElement = event.target;

    // If the target is not a workshop, find its closest workshop parent
    if (!draggedElement.classList.contains('workshop')) {
        draggedElement = draggedElement.closest('.workshop');
        if (!draggedElement) return; // Exit if no workshop parent found
    }

    if(draggedElement.tagName.toString() === 'IMG') return;

    ghostEl = draggedElement.cloneNode(true);
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

// Helper functions
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

function addCloseButton(workshopElement, targetRound) {
    // Remove existing close button if present
    const existingCloseButton = workshopElement.querySelector('.close-button');
    if (existingCloseButton) {
        existingCloseButton.remove();
    }

    // Create close button
    const closeButton = document.createElement('button');
    closeButton.classList.add('close-button');
    closeButton.textContent = '×';
    closeButton.onclick = (e) => {
        e.stopPropagation();
        removeWorkshop(workshopElement, targetRound);
    };

    workshopElement.appendChild(closeButton);
}

function removeWorkshop(workshopElement, roundElement) {
     const workshopContainer = document.querySelector('.workshops');
     if (workshopContainer) {
         workshopContainer.appendChild(workshopElement);
     }
     const closeButton = workshopElement.querySelector('.close-button');
     if (closeButton) {
         closeButton.remove();
     }
     window.workshopsInRounds.delete(workshopElement.id);
     syncDesktopSaveInputsFromRounds();
     checkWorkshopsInRounds();
     updateSaveButton();
     window.planningChanged = true;
}

function checkWorkshopsInRounds() {
    // Update placeholder visibility
    [1, 2, 3].forEach((roundNumber) => {
        const roundElement = document.getElementById(String(roundNumber));
        if (!roundElement) return;

        const roundContainer = roundElement.parentElement;
        const placeholder = roundContainer.querySelector('.placeholder');
        const workshop = roundElement.querySelector('.workshop');

        if (workshop && placeholder) {
            // Hide placeholder when workshop is present
            placeholder.style.display = 'none';
        } else if (placeholder) {
            // Show placeholder when no workshop
            placeholder.style.display = 'flex';
        }
    });
}

function updateSaveButton() {
     const saveButton = document.getElementById('save-button');
     if (saveButton) {
         if (window.workshopsInRounds.size === 3) {
             saveButton.style.display = 'flex';
         } else {
             saveButton.style.display = 'none';
         }
     }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    if (typeof checkWorkshopsInRounds === 'function') {
        checkWorkshopsInRounds();
    }
    if (typeof updateSaveButton === 'function') {
        updateSaveButton();
    }
});


