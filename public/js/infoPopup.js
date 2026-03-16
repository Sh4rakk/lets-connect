let currentZIndex = 1002;
let originalWorkshopPositions = new Map();

function storeOriginalPositions() {
    const workshopList = document.getElementById("4");
    if (!workshopList) return;
    Array.from(workshopList.children).forEach((workshop, index) => {
        originalWorkshopPositions.set(workshop.id, index);
    });
}

function restoreWorkshopPosition(workshop) {
    const workshopList = document.getElementById("4");
    const originalIndex = originalWorkshopPositions.get(workshop.id);
    const workshops = Array.from(workshopList.children);

    if (originalIndex !== undefined) {

        let referenceNode = workshops[originalIndex] || null;
        workshopList.insertBefore(workshop, referenceNode);
    } else {
        workshopList.appendChild(workshop);
    }

    const closeButton = workshop.querySelector(".close-button");
    if (closeButton) {
        closeButton.remove();
    }
}

function addCloseButton(workshop) {
    if (workshop.querySelector(".close-button")) {
        return;
    }

    const closeButton = document.createElement("button");
    closeButton.classList.add("close-button");
    closeButton.textContent = "X";
    closeButton.addEventListener("click", function () {
        const workshopList = document.getElementById("4");
        const roundBeforeMove = workshop.parentNode && workshop.parentNode.classList.contains('round')
            ? workshop.parentNode
            : null;

        let index = originalWorkshopOrder.indexOf(workshop.id);

        if (index !== -1) {
            let referenceNode = workshopList.children[index] || null;
            workshopList.insertBefore(workshop, referenceNode);
        } else {
            workshopList.appendChild(workshop);
        }
        restoreWorkshopPosition(workshop);
        workshopsInRounds.delete(workshop.id);
        if (roundBeforeMove) {
            document.getElementById("save" + roundBeforeMove.id).value = "";
        }
        if (typeof syncDesktopSaveInputsFromRounds === 'function') {
            syncDesktopSaveInputsFromRounds();
        }
        updateSaveButton();
        closeButton.remove();
        setTimeout(() => workshop.removeAttribute("has-been-selected"), 500);
    });

    workshop.appendChild(closeButton);
}

document.addEventListener("DOMContentLoaded", storeOriginalPositions);

function checkRoundsFilled() {
    const rounds = document.querySelectorAll(".round");
    const isFilled = [...rounds].every(round => round.children.length > 0);

    if (isFilled && planningChanged) {
        showSavePopup();
    }
}

function info(event) {
    event.stopPropagation();

    const buttonId = event.target.id;
    const popupId = "popup" + buttonId.slice(4);
    const popup = document.getElementById(popupId);

    // Mobile cards do not render desktop popup nodes.
    if (!popup) {
        return;
    }

    const allPopups = document.querySelectorAll(".popup");
    allPopups.forEach(p => {
        if (p !== popup) {
            p.style.display = "none";
        }
    });

    if (popup.style.display === "flex") {
        popup.style.display = "none";

    } else {
        currentZIndex++;
        popup.style.zIndex = currentZIndex;
        popup.style.display = "flex";
    }
}

function closePopup(workshopId) {
    const popup = document.getElementById("popup" + workshopId);
    popup.style.display = "none";
}

document.addEventListener("click", function (event) {
    const isInfoButton = event.target.classList.contains("info") || event.target.classList.contains("mobile-info");
    const isPopup = event.target.closest(".popup");
    if (!isInfoButton && !isPopup) {
        const allPopups = document.querySelectorAll(".popup");
        allPopups.forEach((popup) => {
            popup.style.display = "none";
        });
    }
});
