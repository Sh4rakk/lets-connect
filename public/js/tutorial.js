let currentStepIndex = 0;
let prevButton = null;
let nextButton = null;
let tutButtons = null;
let tutOverlay = null;
let tutStep = null;
let roundOne = null;
let isDesktopView = false;
let tutorialInfoClickHandlers = [];

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

function sendInfoIcons() {
    return Array.from(document.querySelectorAll('.workshop .info'));
}

function sendInfoIcon() {
    const icons = sendInfoIcons();
    return icons.length ? icons[0] : null;
}

function bindTutorialInfoClick() {
    if (tutorialInfoClickHandlers.length) return;

    const icons = sendInfoIcons();
    icons.forEach((icon) => {
        icon.style.pointerEvents = 'auto';
        icon.style.cursor = 'pointer';
        icon.style.position = 'relative';
        icon.style.zIndex = '1005';
        icon.style.boxShadow = '0 0 0 4px rgba(255, 165, 0, 0.8)';

        const onInfoClick = () => {
            nextButton.disabled = false;
            nextButton.classList.remove('disabledStyle');
            icon.style.boxShadow = '';
            unbindTutorialInfoClick();
        };

        icon.addEventListener('click', onInfoClick, { once: true, capture: true });
        icon.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                onInfoClick();
            }
        }, { once: true });

        tutorialInfoClickHandlers.push({ icon, handler: onInfoClick });
    });
}

function unbindTutorialInfoClick() {
    tutorialInfoClickHandlers.forEach(({ icon, handler }) => {
        icon.removeEventListener('click', handler);
    });
    tutorialInfoClickHandlers = [];
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
    if (workshop) {
        workshop.style.zIndex = 'unset';
        workshop.classList.remove('tutorial-highlight');
    }
    const icons = sendInfoIcons();
    icons.forEach((icon) => {
        icon.style.zIndex = 'unset';
        icon.style.boxShadow = '';
        icon.style.position = 'unset';
        icon.onclick = null;
    });
    unbindTutorialInfoClick();
    const roundX = sendRoundX();
    if (roundX) roundX.style.zIndex = 'unset';
    if (roundOne) roundOne.classList.remove('tutorial-highlight');
    tutStep.style.position = 'unset';
    tutStep.style.top = 'unset';
}

function firstStep() {
    if (!isDesktopView) return;
    defaultStyling();
    nextButton.disabled = true;
    nextButton.classList.add('disabledStyle');
    const icons = sendInfoIcons();
    icons.forEach((icon) => {
        icon.style.position = 'relative';
        icon.style.zIndex = '1005';
        icon.style.boxShadow = '0 0 0 4px rgba(255, 165, 0, 0.8)';
        icon.style.cursor = 'pointer';
    });
    bindTutorialInfoClick();
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
        };

        workshop.addEventListener('dragstart', unlock, { once: true });
        workshop.addEventListener('click', unlock, { once: true });
        workshop.addEventListener('keyup', (e) => {
            if (e.key === 'Enter' || e.key === ' ') unlock();
        }, { once: true });
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
    defaultStyling();
    tutOverlay.style.display = 'none';
}

function skipTutorial() {
    setTutorialCookie();
    defaultStyling();
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
