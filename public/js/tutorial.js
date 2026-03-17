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

document.addEventListener("DOMContentLoaded", () => {
    prevButton = document.getElementById('prevButton') ?? null;
    nextButton = document.getElementById('nextButton') ?? null;
    tutButtons = document.querySelector('.tutorial-buttons') ?? null;
    tutOverlay = document.querySelector('.tutorial-overlay') ?? null;
    tutStep = document.getElementById('tutorial-step') ?? null;
    roundOne = document.getElementById('round1') ?? null;

    isDesktopView = !!(tutOverlay && tutStep && tutButtons && prevButton && nextButton);

    if (isDesktopView) {
        const cookieStatus = cookieState();
        if (!cookieStatus) {
            startTutorial();
            document.cookie = "tutorialSeen=true; path=/";
        }
    }
});

function startTutorial() {
    if (!isDesktopView) return;
    tutOverlay.style.display = "flex";
    document.getElementById('tutorial-text').textContent = tutorialSteps[currentStepIndex].text;

    if (currentStepIndex === 0) {
        prevButton.style.display = "none";
        tutButtons.style.justifyContent = "center";
    }
}

function nextStep() {
    if (!isDesktopView) return;

    currentStepIndex = currentStepIndex < tutorialLength ? currentStepIndex + 1 : tutorialLength;
    document.getElementById('tutorial-text').textContent = tutorialSteps[currentStepIndex].text;

    prevButton.style.display = currentStepIndex === 0 ? "none" : "flex";
    tutButtons.style.justifyContent = currentStepIndex === 0 ? "center" : "space-between";

    switch (currentStepIndex) {
        case 0:
            defaultStyling();
            break;
        case 1:
            firstStep();
            break;
        case 2:
            secondStep();
            break;
        case 3:
            thirdStep();
            break;
        case 4:
            fourthStep();
            break;
        case 5:
            fifthStep();
            break;
        default:
            endTutorial();
            break;
    }
}

function prevStep() {
    if (!isDesktopView) return;

    currentStepIndex = currentStepIndex > 0 ? currentStepIndex - 1 : 0;
    document.getElementById('tutorial-text').textContent = tutorialSteps[currentStepIndex].text;

    prevButton.style.display = currentStepIndex === 0 ? "none" : "flex";
    tutButtons.style.justifyContent = currentStepIndex === 0 ? "center" : "space-between";

    switch (currentStepIndex) {
        case 0:
            defaultStyling();
            break;
        case 1:
            firstStep();
            break;
        case 2:
            secondStep();
            break;
        case 3:
            thirdStep();
            break;
        case 4:
            fourthStep();
            break;
        case 5:
            fifthStep();
            break;
        default:
            defaultStyling();
            break;
    }
}

function sendWorkshop() {
    return document.querySelector('.workshops .workshop:first-child') ?? null;
}

function sendInfoIcon() {
    const workshop = sendWorkshop();
    return workshop?.querySelector('.info') ?? null;
}

function sendRoundX() {
    const workshopInRound = roundOne?.querySelector('.workshop') ?? null;
    return workshopInRound?.querySelector('.close-button') ?? null;
}

function defaultStyling() {
    if (!isDesktopView) return;

    nextButton.disabled = false;
    nextButton.classList.remove("disabledStyle");
    roundOne.style.zIndex = "unset";

    const workshop = sendWorkshop();
    if (workshop) {
        workshop.style.zIndex = "unset";
    }

    const icon = sendInfoIcon();
    if (icon) {
        icon.style.zIndex = "unset";
    }

    const roundX = sendRoundX();
    if (roundX && currentStepIndex !== 4) {
        roundX.style.zIndex = "unset";
    }

    roundOne.classList.remove("tutorial-highlight");
    workshop?.classList.remove("tutorial-highlight");

    tutStep.style.position = "unset";
    tutStep.style.top = "unset";
}

function firstStep() {
    if (!isDesktopView) return;

    defaultStyling();
    nextButton.disabled = true;
    nextButton.classList.add("disabledStyle");

    const icon = sendInfoIcon();
    if (icon) {
        icon.style.zIndex = "1001";
    }

    tutStep.style.position = "relative";
    tutStep.style.top = "25%";
}

function secondStep() {
    if (!isDesktopView) return;

    defaultStyling();
    roundOne.style.zIndex = "1001";
    roundOne.classList.add("tutorial-highlight");

    tutStep.style.position = "relative";
    tutStep.style.top = "25%";
}

function thirdStep() {
    if (!isDesktopView) return;

    defaultStyling();
    nextButton.disabled = true;
    nextButton.classList.add("disabledStyle");

    const workshop = sendWorkshop();
    roundOne.style.zIndex = "1001";

    if (workshop) {
        workshop.style.zIndex = "1001";
        workshop.classList.add("tutorial-highlight");
    }

    tutStep.style.position = "relative";
    tutStep.style.top = "25%";
}

function fourthStep() {
    if (!isDesktopView) return;

    defaultStyling();
    nextButton.disabled = true;
    nextButton.classList.add("disabledStyle");

    const roundX = sendRoundX();
    if (roundX) {
        roundX.style.zIndex = "1001";
    }

    tutStep.style.position = "relative";
    tutStep.style.top = "25%";
}

function fifthStep() {
    if (!isDesktopView) return;

    defaultStyling();
    tutOverlay.style.display = "none";
}

function endTutorial() {
    if (!isDesktopView) return;

    tutOverlay.style.display = "none";
}

function skipTutorial() {
    document.cookie = "tutorialSeen=true; path=/";
    const overlay = document.querySelector('.tutorial-overlay');
    if (overlay) overlay.style.display = "none";
}

function cookieState() {
    return document.cookie.includes("tutorialSeen=true") ?? false;
}
