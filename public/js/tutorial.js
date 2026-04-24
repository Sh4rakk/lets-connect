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
    { text: "Klik op het kruisje om de info pagina te verlaten.", highlight: ".round:nth-child(1)" },
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
    if (currentStepIndex === 6) sixthStep();
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
    if (currentStepIndex === 5) fifthStep();
    if (currentStepIndex === 6) sixthStep();
}

function sendWorkshop() {
    return document.querySelector('.workshops .workshop:first-child');
}

function sendInfoIcons() {
    return Array.from(document.querySelectorAll('.workshop .info'));
}

function sendInfoIcon() {
    const workshop = sendWorkshop();
    return workshop ? workshop.querySelector('.info') : null;
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
    if (roundOne) {
        roundOne.style.zIndex = 'unset';
        roundOne.style.pointerEvents = 'unset';

        // Reset drop target
        const dropTarget = roundOne.querySelector('.round:not(.placeholder)');
        if (dropTarget) {
            dropTarget.style.pointerEvents = 'unset';
            dropTarget.style.zIndex = 'unset';
        }

        const placeholder = roundOne.querySelector('.placeholder');
        if (placeholder) {
            placeholder.style.backgroundColor = '#ffff';
            placeholder.style.outline = 'unset';
            placeholder.style.zIndex = 'unset';
            placeholder.style.position = 'unset';
            placeholder.style.pointerEvents = 'unset';
        }
    }
    const workshop = sendWorkshop();
    if (workshop) {
        workshop.style.zIndex = 'unset';
        workshop.style.outline = 'unset';
        workshop.style.outlineOffset = 'unset';
        workshop.classList.remove('tutorial-highlight');
        // Clear styling from first workshop's info icon
        const icon = workshop.querySelector('.info');
        if (icon) {
            icon.style.zIndex = 'unset';
            icon.style.boxShadow = '';
            icon.style.position = 'unset';
            icon.style.cursor = 'unset';
            icon.style.pointerEvents = 'unset';
            icon.onclick = null;
        }
    }
    unbindTutorialInfoClick();
    const roundX = sendRoundX();
    if (roundX) roundX.style.zIndex = 'unset';
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
        icon.style.cursor = 'pointer';

        const onInfoClick = () => {
            // Find the workshop element and its corresponding popup
            const workshop = sendWorkshop();
            if (workshop) {
                const workshopId = workshop.id.replace('workshop', '');
                const popup = document.getElementById('popup' + workshopId);

                if (popup) {
                    // Show the popup
                    popup.style.display = 'flex';
                }
            }

            // Move to next step after a short delay to let popup render
            setTimeout(() => {
                nextButton.disabled = false;
                nextButton.classList.remove('disabledStyle');
                icon.style.boxShadow = '';
                nextStep();
            }, 300);
        };

        icon.addEventListener('click', onInfoClick, { once: true });
        icon.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                onInfoClick();
            }
        }, { once: true });
    }
    tutStep.style.position = 'relative';
    tutStep.style.top = '25%';
}

function secondStep() {
    if (!isDesktopView) return;
    defaultStyling();
    nextButton.disabled = true;
    nextButton.classList.add('disabledStyle');

    // Find the close button in the info popup
    const popup = document.querySelector('.popup[style*="display: flex"]') || document.querySelector('.popup');
    const closeButton = popup ? popup.querySelector('.close') : null;

    if (closeButton && popup && popup.style.display !== 'none') {
        closeButton.style.zIndex = '1005';
        closeButton.style.boxShadow = '0 0 0 4px rgba(255, 165, 0, 0.8)';
        closeButton.style.position = 'relative';

        const onCloseClick = () => {
            nextButton.disabled = false;
            nextButton.classList.remove('disabledStyle');
            closeButton.style.boxShadow = '';
            closeButton.style.zIndex = 'unset';
            closeButton.style.position = 'unset';
            nextStep();
        };

        closeButton.addEventListener('click', onCloseClick, { once: true });
    } else {
        // If popup is not visible, auto-advance after delay
        setTimeout(() => {
            nextButton.disabled = false;
            nextButton.classList.remove('disabledStyle');
        }, 1000);
    }

    tutStep.style.position = 'relative';
    tutStep.style.top = '25%';
}

function thirdStep() {
    if (!isDesktopView) return;
    defaultStyling();
    nextButton.disabled = true;
    nextButton.classList.add('disabledStyle');

    // Highlight the first workshop card
    const workshop = sendWorkshop();
    if (workshop) {
        console.log(workshop)
        workshop.style.zIndex = '1004';
        workshop.style.outline = '2px solid rgb(245, 130, 32)';
        workshop.style.outlineOffset = '2px';
    }

    // Highlight Round 1 - but keep it accessible for dragging
    if (roundOne) {
        roundOne.style.zIndex = '1003';
        roundOne.style.pointerEvents = 'auto';

        // Make sure the drop target is accessible
        const dropTarget = roundOne.querySelector('.round:not(.placeholder)');
        if (dropTarget) {
            dropTarget.style.pointerEvents = 'auto';
            dropTarget.style.zIndex = '1003';
        }

        // Also highlight the placeholder
        const placeholder = roundOne.querySelector('.placeholder');
        if (placeholder) {
            placeholder.style.backgroundColor = '#fffff';
            placeholder.style.outline = '2px solid rgb(245, 130, 32)';
            placeholder.style.zIndex = '1004';
            placeholder.style.position = 'relative';
            placeholder.style.pointerEvents = 'none';
        }
    }

    if (workshop) {
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
    if (roundOne) {
        roundOne.style.zIndex = '1001';
        roundOne.classList.add('tutorial-highlight');
    }
    tutStep.style.position = 'relative';
    tutStep.style.top = '25%';
}

function fifthStep() {
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

function sixthStep() {
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

function restartTutorial() {
    if (!isDesktopView) return;
    currentStepIndex = 0;
    startTutorial();
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
