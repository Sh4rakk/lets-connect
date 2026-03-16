if (window.innerWidth < 800) {
    var element = document.querySelector(".main .workshops");
    element.remove()
}

var rounds = document.querySelectorAll('.rounds .round');
var workshops = document.querySelectorAll('.workshops .workshop');
var clickedWorkshop = document.querySelector('.clickedWorkshop');
var popupWrapper = document.getElementById('workshopsPopup');
var selectedPopupWrapper = document.getElementById('selectedWorkshopPopup');

document.addEventListener("DOMContentLoaded", () => {
    clickedRound()

    window.addEventListener('click', function (e) {
        var popup = document.querySelector('#workshopsPopup .popupWrapper');
        var mainElement = document.querySelector('.main');

        var tutOverlay = document.querySelector('.tutorial-overlay');

        if (!popupWrapper || !popup || !mainElement || !tutOverlay) return;
        const tut = window.getComputedStyle(tutOverlay);
        if (tut.display === "flex") return;

        const styles = window.getComputedStyle(popupWrapper);
        if (!styles.display === "flex") return;

        rounds.forEach(round => {
            round.removeEventListener('click', function () { })
        })

        // Check if clicked outside of popup and outside main container
        if (!popup.contains(e.target) &&
            !mainElement.contains(e.target) &&
            !selectedPopupWrapper.contains(e.target) &&
            !tutOverlay.contains(e.target)) {

            popupWrapper.style.display = "none";
        }
    });
});

async function roundClick(round) {
    if (!round) return;

    if (round.querySelector('div')) return;

    var roundNumber = round.getAttribute('id');
    roundNumber = Number(roundNumber);

    var roundString = document.querySelector('.roundWorkshop');
    roundString.innerText = roundNumber;

    const popup = document.getElementById("workshopsPopup");
    if (!popup) return;

    popup.style.display = "flex";
    var loader = popup.querySelector('.loader');
    loader.style.display = "inline-block";

    var popupTitle = document.querySelector('.chosenRound');
    popupTitle.innerText = "Laden ronde: ";

    var workshops = popup.querySelector('.workshops');
    if (workshops) {
        console.log(workshops);
        
        workshops.remove();
    }

    try {
        var data = await fetchUserData();
        loader.style.display = "none";
        popupTitle.innerText = "Kies je workshop voor ronde: "

        const filtered = data
            .map(workshop => {
                const moment = workshop.moments.find(m => m.ronde === roundNumber);
                if (moment) {
                    return {
                        workshop_name: workshop.workshop_name,
                        ...moment
                    };
                }
                return null;
            })
            .filter(Boolean);

        createWorkshops(filtered, round);
    } catch {
        return;
    }
}

async function fetchUserData() {
    var response = await fetch("/Capacity")
    const data = await response.json();

    if (data.status === "success") {
        return data.data;
    }
    return;
}

async function createWorkshops(filtered, round) {
    var divStructure = document.createElement('div')
    divStructure.classList.add('workshops');
    divStructure.setAttribute('id', '4');

    divStructure.style.display = "grid";
    var divs = [];
    filtered.forEach(workshop => {
        const emptySpots = workshop.capacity - workshop.bookings_count;
        const emptyText = emptySpots === 0 ? "<div class='emptyDataDiv'>Workshop zit vol!</div>" : `<div class='emptyDataDiv'>${emptySpots}</div>`;

        divStructure.innerHTML += `
        <div class='workshop' id='workshop${workshop.workshop_id - 1}' capacity="${workshop.capacity}" draggable='true' ondragstart='drag(event)'> 
            <div class='info' onclick='info(event)' id='info${workshop.workshop_id - 1}' tabindex='0'>i</div> 
            <div class='popup' id='popup${workshop.workshop_id - 1}' draggable='false'>
                <button class='close' onclick='closePopup(${workshop.workshop_id - 1})'>x</button> 
                <a href='https://xerte.deltion.nl/play.php?template_id=8708#programma' class='popup-header' target='_blank'>Klik <span class='highlight'>hier</span> voor meer informatie</a>  
                <div class='description'>
                    <div class='descriptionText'>${workshop.full_description} </div>
                    <div class='descriptionImage'><img src='${workshop.image_url}'></div>
                </div>
                </div>
            <div class='title showText' id='title${workshop.workshop_id - 1}' workshop='${workshop.workshop_name}'>${workshop.workshop_name}  </div>
            <div class="capacityText title hiddenText" id="capacityText${workshop.workshop_id - 1}"></div>
            <div class="locationWorkshop">Deze workshop vind plaats in: <div id="location">${workshop.location}</div></div>

            ${emptyText}
        </div>
        `;

        divs.push(divStructure);
    })

    insertWorkshops(divs, round);
}

function insertWorkshops(divs, round) {
    var popup = document.querySelector('.popupWrapper');
    
    divs.forEach(div => {
        popup.appendChild(div);
    });

    clickedWorkshopToRound(round)
}

function clickedWorkshopToRound(round) {
    let workshops = document.querySelectorAll('.popupWrapper > .workshops .workshop');

    workshops.forEach(workshop => {
        workshop.addEventListener("click", (e) => {
            
            var iconInfo = workshop.querySelector('.info');
            var infoPopup = workshop.querySelector('.popup');

            var tutPopup = document.querySelector('.tutorial-overlay');
            var popupStyle = window.getComputedStyle(tutPopup)

            if (popupStyle.display === "flex") return;

            if (iconInfo.contains(e.target)) return;
            if (infoPopup.contains(e.target)) return;
            if (round.contains(e.target)) return;
            
            confirmationPopup(workshop, round)
        }, { once: true });
    });
}

function closeWorkshops() {
    if (!popupWrapper) return;
    popupWrapper.style.display = "none";
}

function confirmationPopup(workshop, round) {
    if (!selectedPopupWrapper) return;


    selectedPopupWrapper.style.display = "flex";

    var innerTitle = document.querySelector('.workshopNameInfo')
    innerTitle.innerHTML = workshop.querySelector('.title').innerText;

    var innerCapacity = document.getElementById('viewCapacityRound');
    innerCapacity.innerHTML = workshop.querySelector('.emptyDataDiv').innerHTML;

    var yesButton = document.querySelector('#selectedWorkshopPopup .yes-button')
    yesButton.addEventListener('click', function () {
        addConfirmedWorkshop(workshop, round)
        selectedPopupWrapper.style.display = "none";
    }, { once: true })
}

function removeConfirm() {
    if (!selectedPopupWrapper) return;

    selectedPopupWrapper.style.display = "none";
    return
}

function addConfirmedWorkshop(workshop, round) {
    if (!round) return;

    const targetRound = round.classList.contains("round") ? round : round.closest(".round");
    if (!targetRound) return;

    const draggedElement = workshop;
    
    if (!targetRound.hasChildNodes()) {
        targetRound.appendChild(draggedElement);
        draggedElement.setAttribute("has-been-selected", "true");

        let title = draggedElement.querySelector(".title");
        let xpath = `//input[@value="` + title.getAttribute('workshop') + `"]`;
        let matchingElement = document.evaluate(xpath, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
        if (matchingElement !== null) {
            matchingElement.value = "";
        }
        document.getElementById("save" + targetRound.id).value = title.getAttribute('workshop');
        addCloseButton(draggedElement, targetRound);

    }

    updateSaveButton();

    popupWrapper.style.display = "none";

    ifWorkshopFull()
}

async function ifWorkshopFull() {
    var roundsCheck = document.querySelectorAll('.rounds .round')
    
    roundsCheck.forEach(round => {
        var capacity = round.querySelector('.workshop .emptyDataDiv');
        if (!capacity) return;
        var closeButton = round.querySelector('.close-button');
        if (!closeButton) return;

        if (currentStepIndex !== 0) return;

        if (capacity.innerText.trim() === 'Workshop zit vol!') {
            console.log(capacity.innerText.trim());
            
            showErrorPopup("Deze ronde zit vol!")
            closeButton.click()
            return;
        }
    })
}