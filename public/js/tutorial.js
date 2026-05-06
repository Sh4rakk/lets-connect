let currentStepIndex = 0;
let prevButton = null;
let nextButton = null;
let skipButton = null;
let tutButtons = null;
let tutOverlay = null;
let tutStep = null;
let roundOne = null;
let isDesktopView = false;
let tutorialInfoClickHandlers = [];
let autoCloseTimeout = null;

const tutorialSteps = [
    { text: "Welkom op het dashboard! Laten we leren hoe je deze pagina gebruikt.", highlight: ".round:nth-child(1)" },
    { text: "Klik op het informatie-icoon (i) van een workshop om de workshopinfo te openen. De knop ‘Volgende’ wordt daarna actief.", highlight: ".round:nth-child(1)" },
    { text: "Klik op het rode kruisje rechtsboven in het info-venster om het te sluiten.", highlight: ".round:nth-child(1)" },
    { text: "Pak het workshopkaartje vast en sleep het naar ronde 1.", highlight: ".workshop:nth-child(1)" },
    { text: "Klik op het kleine rode kruisje (x) op de workshop in de ronde om deze weer te verwijderen.", highlight: ".round:nth-child(1)" },
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
    // At the last step, close the tutorial instead of trying to advance further
    if (currentStepIndex >= tutorialLength) {
        endTutorial();
        return;
    }
    currentStepIndex++;
    tutStep.querySelector('#tutorial-text').textContent = tutorialSteps[currentStepIndex].text;
    updateStepButtonState();
    if (currentStepIndex === 0) defaultStyling();
    if (currentStepIndex === 1) firstStep();
    if (currentStepIndex === 2) secondStep();
    if (currentStepIndex === 3) thirdStep();
    if (currentStepIndex === 4) fourthStep();
    if (currentStepIndex === tutorialLength) lastStep();
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
    const workshop = sendWorkshop();
    return workshop ? workshop.querySelector('.info') : null;
}

function setWorkshopCloseButtonsDisabled(disabled, label) {
    document.querySelectorAll('.close-button').forEach((button) => {
        button.disabled = disabled;
        button.style.pointerEvents = disabled ? 'none' : 'auto';
        button.style.opacity = disabled ? '0.45' : '';
        if (disabled) {
            button.title = label;
            button.setAttribute('aria-label', label);
        } else {
            button.title = '';
            button.removeAttribute('aria-label');
        }
    });
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

function defaultStyling() {
    if (!isDesktopView) return;
    // Cancel any pending auto-close from the last step
    if (autoCloseTimeout) {
        clearTimeout(autoCloseTimeout);
        autoCloseTimeout = null;
    }
    nextButton.disabled = false;
    nextButton.classList.remove('disabledStyle');
    nextButton.textContent = 'Volgende';
    if (skipButton) skipButton.style.display = '';

    // Reset all round containers (z-index may have been set on any of them)
    ['round1', 'round2', 'round3'].forEach(id => {
        const container = document.getElementById(id);
        if (container) {
            container.style.zIndex = 'unset';
            container.style.pointerEvents = 'unset';
            container.classList.remove('tutorial-highlight');
        }
    });

    // Reset internal elements of roundOne (placeholder, drop target)
    if (roundOne) {
        const dropTarget = roundOne.querySelector('.round:not(.placeholder)');
        if (dropTarget) {
            dropTarget.style.pointerEvents = 'unset';
            dropTarget.style.zIndex = 'unset';
            dropTarget.style.outline = 'unset';
        }

        const placeholder = roundOne.querySelector('.placeholder');
        if (placeholder) {
            placeholder.style.backgroundColor = '';
            placeholder.style.outline = 'unset';
            placeholder.style.zIndex = 'unset';
            placeholder.style.position = 'unset';
            placeholder.style.pointerEvents = 'unset';
        }
    }

    // Reset the first workshop in .workshops
    const workshop = sendWorkshop();
    if (workshop) {
        workshop.style.zIndex = 'unset';
        workshop.style.outline = 'unset';
        workshop.style.outlineOffset = 'unset';
        workshop.classList.remove('tutorial-highlight');
        const icon = workshop.querySelector('.info');
        if (icon) {
            icon.style.zIndex = 'unset';
            icon.style.boxShadow = '';
            icon.style.position = 'unset';
            icon.style.cursor = 'unset';
            icon.style.pointerEvents = 'unset';
            icon.classList.remove('tutorial-info-highlight');
            icon.onclick = null;
        }
    }

    // Reset close buttons in all rounds
    ['1', '2', '3'].forEach(id => {
        const roundEl = document.getElementById(id);
        if (roundEl) {
            const workshopInRound = roundEl.querySelector('.workshop');
            if (workshopInRound) {
                const closeBtn = workshopInRound.querySelector('.close-button');
                if (closeBtn) {
                    closeBtn.style.zIndex = 'unset';
                    closeBtn.style.boxShadow = '';
                }
            }
        }
    });

    setWorkshopCloseButtonsDisabled(false);

    unbindTutorialInfoClick();
    tutStep.style.position = 'unset';
    tutStep.style.top = 'unset';
}

function firstStep() {
    if (!isDesktopView) return;
    defaultStyling();
    nextButton.disabled = true;
    nextButton.classList.add('disabledStyle');
    nextButton.textContent = 'Klik eerst op i';
    const icon = sendInfoIcon();
    if (icon) {
        icon.style.position = 'relative';
        icon.style.zIndex = '1005';
        icon.style.cursor = 'pointer';
        icon.title = 'Klik op dit informatie-icoon om verder te gaan';
        icon.setAttribute('aria-label', 'Klik op dit informatie-icoon om verder te gaan');
        icon.classList.add('tutorial-info-highlight');

        const onInfoClick = () => {
            const workshop = sendWorkshop();
            if (workshop) {
                const workshopId = workshop.id.replace('workshop', '');
                const popup = document.getElementById('popup' + workshopId);
                if (popup) {
                    popup.style.display = 'flex';
                }
            }

            setTimeout(() => {
                nextButton.disabled = false;
                nextButton.classList.remove('disabledStyle');
                nextButton.textContent = 'Volgende';
                icon.style.boxShadow = '';
                icon.classList.remove('tutorial-info-highlight');
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

    const popup = document.querySelector('.popup[style*="display: flex"]') || document.querySelector('.popup');
    const closeButton = popup ? popup.querySelector('.close') : null;

    if (closeButton && popup && popup.style.display !== 'none') {
        closeButton.style.zIndex = '1005';
        closeButton.style.boxShadow = '0 0 0 4px rgba(255, 165, 0, 0.8)';
        closeButton.style.position = 'relative';
        closeButton.title = 'Klik op dit kruisje om het info-venster te sluiten';
        closeButton.setAttribute('aria-label', 'Klik op dit kruisje om het info-venster te sluiten');

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
    setWorkshopCloseButtonsDisabled(true, 'Wacht eerst met dit kruisje; sleep de workshop naar een ronde om verder te gaan');

    // Highlight the workshop card
    const workshop = sendWorkshop();
    if (workshop) {
        workshop.style.zIndex = '1004';
        workshop.style.outline = '2px solid rgb(245, 130, 32)';
        workshop.style.outlineOffset = '2px';
        workshop.title = 'Sleep dit workshopkaartje naar een ronde';
        workshop.setAttribute('aria-label', 'Sleep dit workshopkaartje naar een ronde');
    }

    // Highlight round 1 — bring it above the overlay and outline the drop zone card
    if (roundOne) {
        roundOne.style.zIndex = '1003';
        roundOne.style.pointerEvents = 'auto';

        const dropTarget = roundOne.querySelector('.round:not(.placeholder)');
        if (dropTarget) {
            dropTarget.style.zIndex = '1003';
            dropTarget.style.pointerEvents = 'auto';
            dropTarget.style.outline = '2px solid rgb(245, 130, 32)';
        }

        const placeholder = roundOne.querySelector('.placeholder');
        if (placeholder) {
            placeholder.style.backgroundColor = 'white';
            placeholder.style.pointerEvents = 'none';
        }
    }

    tutStep.style.position = 'relative';
    tutStep.style.top = '25%';
}

function fourthStep() {
    if (!isDesktopView) return;
    defaultStyling();
    setWorkshopCloseButtonsDisabled(false);
    nextButton.disabled = true;
    nextButton.classList.add('disabledStyle');

    // Find the first workshop placed in any round
    let closeBtn = null;
    let roundContainer = null;
    for (const roundNum of [1, 2, 3]) {
        const roundEl = document.getElementById(String(roundNum));
        if (roundEl) {
            const workshopInRound = roundEl.querySelector('.workshop');
            if (workshopInRound) {
                closeBtn = workshopInRound.querySelector('.close-button');
                if (closeBtn) {
                    roundContainer = roundEl.parentElement;
                    break;
                }
            }
        }
    }

    if (closeBtn && roundContainer) {
        // Bring the round above the overlay so the workshop is clearly visible
        roundContainer.style.zIndex = '1003';
        closeBtn.style.zIndex = '1005';
        closeBtn.style.boxShadow = '0 0 0 4px rgba(255, 165, 0, 0.8)';
        closeBtn.title = 'Klik op dit kleine kruisje om de workshop uit de ronde te verwijderen';
        closeBtn.setAttribute('aria-label', 'Klik op dit kleine kruisje om de workshop uit de ronde te verwijderen');

        closeBtn.addEventListener('click', function onCloseClick() {
            closeBtn.removeEventListener('click', onCloseClick);
            nextStep();
        });
    } else {
        // No workshop in any round yet — enable next as fallback
        nextButton.disabled = false;
        nextButton.classList.remove('disabledStyle');
    }

    tutStep.style.position = 'relative';
    tutStep.style.top = '25%';
}

function lastStep() {
    if (!isDesktopView) return;
    defaultStyling();
    // Hide all navigation except a single close button
    prevButton.style.display = 'none';
    if (skipButton) skipButton.style.display = 'none';
    nextButton.textContent = 'Sluit tutorial';
    nextButton.style.display = 'flex';
    // Auto-close after 5 seconds
    autoCloseTimeout = setTimeout(() => {
        endTutorial();
    }, 5000);
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
    skipButton = document.querySelector('.skip-button');
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
