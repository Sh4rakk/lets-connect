let currentStepIndex = 0;
let prevButton = null;
let nextButton = null;
let tutButtons = null;
let tutOverlay = null;
let tutStep = null;
let roundOne = null;
let isDesktopView = false;

const tutorialSteps = [
    { text: "Welkom op het dashboard! Laten we leren hoe je deze pagina gebruikt.", highlight: ".round:nth-child(1)" },
    { text: "Klik op het 'i' tje om meer info te krijgen over een workshop zelf.", highlight: ".round:nth-child(1)" },
    { text: "Dit is Ronde 1. Sleep een workshop hierheen om het toe te wijzen aan deze ronde.", highlight: ".round:nth-child(1)" },
    { text: "Hier is een workshop. Klik en sleep het naar een ronde.", highlight: ".workshop:nth-child(1)" },
    { text: "Je kunt ook de workshop weer verwijderen met de 'x'.", highlight: ".round:nth-child(1)" },
    { text: "Goed gedaan! Laat de workshop vallen in een ronde om je planning te voltooien.", highlight: ".round:nth-child(1)" },
    { text: "Tutorial voltooid! Je kunt nu zelf workshops slepen.", highlight: ".round:nth-child(1)" }
];

let tutorialLength = tutorialSteps.length - 1;

function setTutorialCookie() {
    document.cookie = "tutorialSeen=true; path=/";
}

function isTutorialSeen() {
    return document.cookie.includes("tutorialSeen=true");
}

function startTutorial() {
    if (!isDesktopView) return;
    tutOverlay.style.display = "flex";
    tutStep.querySelector('#tutorial-text').textContent = tutorialSteps[currentStepIndex].text;
    prevButton.style.display = "none";
    tutButtons.style.justifyContent = "center";
}

function updateStepButtonState() {
    if (!isDesktopView) return;
    prevButton.style.display = currentStepIndex === 0 ? "none" : "flex";
    tutButtons.style.justifyContent = currentStepIndex === 0 ? "center" : "space-between";
}

function nextStep() {
    if (!isDesktopView) return;
    currentStepIndex = Math.min(currentStepIndex + 1, tutorialLength);
    tutStep.querySelector('#tutorial-text').textContent = tutorialSteps[currentStepIndex].text;
    updateStepButtonState();
    if (currentStepIndex === 0) defaultStyling();
    if (currentStepIndex === 1) firstStep();
    if (currentStepIndex === 2) secondStep();
    if (currentStepIndex === 3) thirdStep();
    if (currentStepIndex === 4) fourthStep();
    if (currentStepIndex === 5) fifthStep();
    if (currentStepIndex === tutorialLength) endTutorial();
}

function prevStep() {
    if (!isDesktopView) return;
    currentStepIndex = Math.max(currentStepIndex - 1, 0);
    tutStep.querySelector('#tutorial-text').textContent = tutorialSteps[currentStepIndex].text;
    updateStepButtonState();
    if (currentStepIndex === 0) defaultStyling();
    if (currentStepIndex === 1) firstStep();
    if (currentStepIndex === 2) secondStep();
    if (currentStepIndex === 3) thirdStep();
    if (currentStepIndex === 4) fourthStep();
}

function sendWorkshop() {
    return document.querySelector('.workshops .workshop:first-child');
}

function sendInfoIcon() {
    const workshop = sendWorkshop();
    return workshop ? workshop.querySelector('.info') : null;
}

function sendRoundX() {
    const workshopInRound = roundOne ? roundOne.querySelector('.workshop') : null;
    return workshopInRound ? workshopInRound.querySelector('.close-button') : null;
}

function defaultStyling() {
    if (!isDesktopView) return;
    nextButton.disabled = false;
    nextButton.classList.remove('disabledStyle');
    if (roundOne) roundOne.style.zIndex = 'unset';
    const workshop = sendWorkshop();
    if (workshop) workshop.style.zIndex = 'unset';
    const icon = sendInfoIcon();
    if (icon) icon.style.zIndex = 'unset';
    const roundX = sendRoundX();
    if (roundX) roundX.style.zIndex = 'unset';
    if (roundOne) roundOne.classList.remove('tutorial-highlight');
    if (workshop) workshop.classList.remove('tutorial-highlight');
    tutStep.style.position = 'unset';
    tutStep.style.top = 'unset';
}

function firstStep() {
    if (!isDesktopView) return;
    defaultStyling();
    nextButton.disabled = true;
    nextButton.classList.add('disabledStyle');
    const icon = sendInfoIcon();
    if (icon) {
        icon.style.position = 'relative';
        icon.style.zIndex = '1005';
        icon.style.boxShadow = '0 0 0 4px rgba(255, 165, 0, 0.8)';

        const onInfoClick = () => {
            nextButton.disabled = false;
            nextButton.classList.remove('disabledStyle');
            icon.removeEventListener('click', onInfoClick);
            icon.style.boxShadow = '';
        };

        icon.removeEventListener('click', onInfoClick);
        icon.addEventListener('click', onInfoClick);
    }
    tutStep.style.position = 'relative';
    tutStep.style.top = '25%';
}

function secondStep() {
    if (!isDesktopView) return;
    defaultStyling();
    if (roundOne) {
        roundOne.style.zIndex = '1001';
        roundOne.classList.add('tutorial-highlight');
    }
    tutStep.style.position = 'relative';
    tutStep.style.top = '25%';
}

function thirdStep() {
    if (!isDesktopView) return;
    defaultStyling();
    nextButton.disabled = true;
    nextButton.classList.add('disabledStyle');
    const workshop = sendWorkshop();
    if (workshop) {
        workshop.style.zIndex = '1001';
        workshop.classList.add('tutorial-highlight');

        const unlock = () => {
            nextButton.disabled = false;
            nextButton.classList.remove('disabledStyle');
            workshop.removeEventListener('dragstart', unlock);
            workshop.removeEventListener('click', unlock);
            workshop.removeEventListener('keyup', unlock);
        };

        workshop.addEventListener('dragstart', unlock);
        workshop.addEventListener('click', unlock);
        workshop.addEventListener('keyup', (e) => {
            if (e.key === 'Enter' || e.key === ' ') unlock();
        });
    } else {
        // nothing to drag, just enable after brief delay
        setTimeout(() => {
            nextButton.disabled = false;
            nextButton.classList.remove('disabledStyle');
        }, 2000);
    }
    tutStep.style.position = 'relative';
    tutStep.style.top = '25%';
}

function fourthStep() {
    if (!isDesktopView) return;
    defaultStyling();
    nextButton.disabled = true;
    nextButton.classList.add('disabledStyle');
    const roundX = sendRoundX();
    if (roundX) {
        roundX.style.zIndex = '1001';
        roundX.addEventListener('click', function onRoundXClick() {
            nextButton.disabled = false;
            nextButton.classList.remove('disabledStyle');
            roundX.removeEventListener('click', onRoundXClick);
        });
    }
    tutStep.style.position = 'relative';
    tutStep.style.top = '25%';
}

function fifthStep() {
    if (!isDesktopView) return;
    defaultStyling();
    tutOverlay.style.display = 'none';
}

function endTutorial() {
    if (!isDesktopView) return;
    tutOverlay.style.display = 'none';
}

function skipTutorial() {
    setTutorialCookie();
    if (tutOverlay) tutOverlay.style.display = 'none';
}

function initTutorial() {
    prevButton = document.getElementById('prevButton');
    nextButton = document.getElementById('nextButton');
    tutButtons = document.querySelector('.tutorial-buttons');
    tutOverlay = document.querySelector('.tutorial-overlay');
    tutStep = document.getElementById('tutorial-step');
    roundOne = document.getElementById('round1');
    isDesktopView = window.innerWidth >= 768 && tutOverlay && tutStep && tutButtons && prevButton && nextButton;
    if (isDesktopView && !isTutorialSeen()) {
        startTutorial();
        setTutorialCookie();
    }
}

document.addEventListener('DOMContentLoaded', initTutorial);
