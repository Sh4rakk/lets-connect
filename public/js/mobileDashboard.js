let currentWorkshopId = null;
let currentWorkshopName = null;
let selectedRounds = { 1: null, 2: null, 3: null };
let selectedRoundNames = { 1: null, 2: null, 3: null };
let workshopRoundCapacities = {};
let workshopRoundCapacitiesLoaded = false;

let round1WorkshopId = null;
let round2WorkshopId = null;
let round3WorkshopId = null;

// Store removed workshop elements to restore later
let removedWorkshops = {};

function areAllRoundsSelected() {
    return Boolean(selectedRounds[1] && selectedRounds[2] && selectedRounds[3]);
}

function updateMobileSaveButtonState() {
    const saveButton = document.getElementById('mobile-save-button');
    if (!saveButton) return;

    const isEnabled = areAllRoundsSelected();
    saveButton.disabled = !isEnabled;

    saveButton.classList.toggle('bg-green-600', isEnabled);
    saveButton.classList.toggle('hover:bg-green-700', isEnabled);
    saveButton.classList.toggle('cursor-pointer', isEnabled);

    saveButton.classList.toggle('bg-gray-400', !isEnabled);
    saveButton.classList.toggle('cursor-not-allowed', !isEnabled);
}

window.addEventListener('DOMContentLoaded', updateMobileSaveButtonState);

function getMobileRoundModalButton(round) {
    return document.getElementById(`mobile-modal-round-${round}`);
}

function mapWmIdToRound(wmId) {
    let round = Number(wmId) % 3;
    if (round === 0) round = 3;
    return round;
}

async function loadMobileWorkshopCapacities() {
    if (workshopRoundCapacitiesLoaded) return;

    try {
        const response = await fetch('/viewCapacity');
        const data = await response.json();

        if (data.status !== 'success' || !Array.isArray(data.data)) {
            workshopRoundCapacitiesLoaded = true;
            return;
        }

        const capacityMap = {};

        data.data.forEach((workshop) => {
            if (!Array.isArray(workshop.moments)) return;

            workshop.moments.forEach((moment) => {
                const workshopId = String(moment.workshop_id);
                const round = mapWmIdToRound(moment.wm_id);
                const spotsLeft = Number(moment.capacity) - Number(moment.bookings);

                if (!capacityMap[workshopId]) {
                    capacityMap[workshopId] = {};
                }

                capacityMap[workshopId][round] = {
                    spotsLeft,
                    isFull: spotsLeft <= 0
                };
            });
        });

        workshopRoundCapacities = capacityMap;
        workshopRoundCapacitiesLoaded = true;
    } catch (error) {
        workshopRoundCapacitiesLoaded = true;
    }
}

function getRoundCapacityLabel(workshopId, round) {
    if (!workshopId) return 'capaciteit onbekend';

    const workshopData = workshopRoundCapacities[String(workshopId)];
    if (!workshopData || !workshopData[round]) return 'capaciteit onbekend';

    return workshopData[round].isFull ? 'workshop zit vol!' : `${workshopData[round].spotsLeft} plekken over`;
}

function updateMobileRoundModalButtons() {
    const roundTimes = {
        1: '13:00 - 13:45',
        2: '13:45 - 14:30',
        3: '15:00 - 15:45'
    };

    [1, 2, 3].forEach((round) => {
        const button = getMobileRoundModalButton(round);
        if (!button) return;

        const isFilled = Boolean(selectedRounds[round]);
        const selectedName = selectedRoundNames[round];
        const capacityLabel = getRoundCapacityLabel(currentWorkshopId, round);

        // Check if workshop is full for this round
        const workshopData = workshopRoundCapacities[String(currentWorkshopId)];
        const isFull = workshopData && workshopData[round] && workshopData[round].isFull;

        button.disabled = isFilled || isFull;
        button.textContent = isFilled
            ? `Ronde ${round} (${roundTimes[round]}) - ${capacityLabel} - Gekozen: ${selectedName}`
            : `Ronde ${round} (${roundTimes[round]}) - ${capacityLabel}`;

        button.classList.toggle('bg-deltion-blue-900', !isFilled && !isFull);
        button.classList.toggle('hover:bg-deltion-blue-600', !isFilled && !isFull);
        button.classList.toggle('cursor-pointer', !isFilled && !isFull);

        button.classList.toggle('bg-gray-400', isFilled || isFull);
        button.classList.toggle('cursor-not-allowed', isFilled || isFull);
    });
}

window.addEventListener('DOMContentLoaded', updateMobileRoundModalButtons);
window.addEventListener('DOMContentLoaded', () => {
    loadMobileWorkshopCapacities().then(updateMobileRoundModalButtons);
});

function selectWorkshopMobile(workshopId, workshopName) {
    // Check if this workshop is already selected in any round
    for (let r = 1; r <= 3; r++) {
        if (selectedRounds[r] === workshopId) {
            // Workshop is already selected in another round
            document.getElementById('error-title-mobile').textContent = "Workshop al geselecteerd";
            document.getElementById('error-message-mobile').textContent = `De workshop '${workshopName}' is al geselecteerd voor ronde ${r}.`;
            document.getElementById('error-popup-mobile').classList.remove('hidden');
            document.getElementById('error-popup-mobile').classList.add('flex');
            return;
        }
    }

    currentWorkshopId = workshopId;
    currentWorkshopName = workshopName;
    document.getElementById('workshopModalTitle').textContent = workshopName;
    document.getElementById('workshopSelectionModal').classList.remove('hidden');
    document.getElementById('workshopSelectionModal').classList.add('flex');
    updateMobileRoundModalButtons();
    loadMobileWorkshopCapacities().then(updateMobileRoundModalButtons);
}

function closeWorkshopSelection() {
    document.getElementById('workshopSelectionModal').classList.add('hidden');
    document.getElementById('workshopSelectionModal').classList.remove('flex');
    currentWorkshopId = null;
    currentWorkshopName = null;
}

function addToRoundMobile(round) {
    // Ensure current workshop is not already selected in ANY round
    for (let r = 1; r <= 3; r++) {
        if (selectedRounds[r] === currentWorkshopId) {
             return;
        }
    }

    if (currentWorkshopId === null || selectedRounds[round]) return;

    selectedRounds[round] = currentWorkshopId;
    selectedRoundNames[round] = currentWorkshopName;

    const roundElements = {
        1: document.getElementById('mobile-round-1'),
        2: document.getElementById('mobile-round-2'),
        3: document.getElementById('mobile-round-3')
    };

    const removeButtonElements = {
        1: document.getElementById('remove-round-1'),
        2: document.getElementById('remove-round-2'),
        3: document.getElementById('remove-round-3')
    };

    roundElements[round].textContent = currentWorkshopName;
    roundElements[round].classList.remove('text-gray-600');
    removeButtonElements[round].classList.remove('hidden');

    // Instead of hiding, remove from DOM
    // document.getElementById(currentWorkshopId).classList.add('hidden');
    let el = document.getElementById(currentWorkshopId);
    if (el) {
        removedWorkshops[currentWorkshopId] = el;
        el.remove();
    }

    roundElements[round].classList.add('text-gray-800', 'font-semibold');

    // Store the workshop ID in the appropriate variable
    if (round === 1) round1WorkshopId = currentWorkshopId;
    if (round === 2) round2WorkshopId = currentWorkshopId;
    if (round === 3) round3WorkshopId = currentWorkshopId;

    console.log(`Round ${round}: `, currentWorkshopId)

    updateMobileSaveButtonState();
    updateMobileRoundModalButtons();
    closeWorkshopSelection();
}

function removeWorkshopRound(round) {
    // Get the stored workshop ID for this round
    let workshopId = null;
    if (round === 1) workshopId = round1WorkshopId;
    if (round === 2) workshopId = round2WorkshopId;
    if (round === 3) workshopId = round3WorkshopId;

    if (workshopId === null) return;

    const roundElements = {
        1: document.getElementById('mobile-round-1'),
        2: document.getElementById('mobile-round-2'),
        3: document.getElementById('mobile-round-3')
    };

    const removeButtonElements = {
        1: document.getElementById('remove-round-1'),
        2: document.getElementById('remove-round-2'),
        3: document.getElementById('remove-round-3')
    };

    roundElements[round].textContent = "Geen workshop geselecteerd";
    roundElements[round].classList.add('text-gray-600');
    removeButtonElements[round].classList.add('hidden');
    roundElements[round].classList.remove('text-gray-800', 'font-semibold');

    // Restore element to DOM
    // document.getElementById(workshopId).classList.remove('hidden');
    let el = removedWorkshops[workshopId];
    if (el) {
        let container = document.getElementById('mobile-workshops-container');
        if (container) {
            container.appendChild(el);
            delete removedWorkshops[workshopId];
        }
    }

    // Clear the stored workshop ID
    if (round === 1) round1WorkshopId = null;
    if (round === 2) round2WorkshopId = null;
    if (round === 3) round3WorkshopId = null;

    selectedRounds[round] = null;
    selectedRoundNames[round] = null;
    updateMobileSaveButtonState();
    updateMobileRoundModalButtons();
}

function showMobileSavePopup() {

    if (!areAllRoundsSelected()) {
        document.getElementById('mobileSavePopup').classList.add('hidden');
        document.getElementById('error-message-mobile').textContent = "Selecteer eerst een workshop voor alle rondes.";
        return;
    }

    document.getElementById('save1Mobile').value = selectedRounds[1];
    document.getElementById('save2Mobile').value = selectedRounds[2];
    document.getElementById('save3Mobile').value = selectedRounds[3];
    document.getElementById('mobileSavePopup').classList.remove('hidden');
    document.getElementById('mobileSavePopup').classList.add('flex');
}

function closeMobileSavePopup() {
    document.getElementById('mobileSavePopup').classList.add('hidden');
    document.getElementById('mobileSavePopup').classList.remove('flex');
}

function closeErrorPopupMobile() {
    document.getElementById('error-popup-mobile').classList.add('hidden');
    document.getElementById('mobileSavePopup').classList.add('hidden');
    document.getElementById('error-popup-mobile').classList.remove('flex');
    document.getElementById('mobileSavePopup').classList.remove('flex');
}

function closeInformation() {
    document.getElementById('workshopInformationModal').classList.add('hidden');
    document.getElementById('workshopInformationModal').classList.remove('flex');
}

function mobileInfo(event, Title, Description) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    if (!Title || !Description) {
        return;
    }

    document.getElementById('workshopInformationModal').classList.remove('hidden');
    document.getElementById('workshopInformationModal').classList.add('flex');

    document.getElementById('workshopInformationTitle').textContent = Title
    document.getElementById('InformationDescription').textContent = Description
}
