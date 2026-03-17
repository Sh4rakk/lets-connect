// Mobile Tutorial System
const mobileTutorialSteps = [
    {
        title: "Welkom!",
        text: "Welkom bij Let's Connect! Dit is je workshop selectie interface."
    },
    {
        title: "Workshops",
        text: "Scroll door de beschikbare workshops. Elk kaartje toont de workshop naam en korte beschrijving."
    },
    {
        title: "Workshop Kiezen",
        text: "Klik op een workshop kaartje om deze toe te voegen aan een ronde."
    },
    {
        title: "Ronde Selecteren",
        text: "Kies de ronde waar je deze workshop wilt toevoegen: Ronde 1, 2 of 3."
    },
    {
        title: "Overzicht",
        text: "Bovenaan zie je welke workshops je hebt geselecteerd voor elke ronde."
    },
    {
        title: "Opslaan",
        text: "Wanneer je alle drie rondes hebt ingevuld, klik 'Opslaan' onderin het scherm om je keuzes op te slaan."
    },
    {
        title: "Gereed!",
        text: "Je bent nu klaar! Je keuzes zijn opgeslagen."
    }
];

let currentMobileStep = 0;

function showMobileTutorialIfNeeded() {
    // Check if user has seen tutorial before
    if (!localStorage.getItem('mobileTutorialSeen')) {
        showMobileTutorial();
        localStorage.setItem('mobileTutorialSeen', 'true');
    }
}

function showMobileTutorial() {
    currentMobileStep = 0;
    updateMobileTutorialContent();
    const overlay = document.getElementById('mobileTutorialOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
    }
}

function closeMobileTutorial() {
    const overlay = document.getElementById('mobileTutorialOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function updateMobileTutorialContent() {
    const step = mobileTutorialSteps[currentMobileStep];
    const titleElement = document.getElementById('mobileTutorialTitle');
    const textElement = document.getElementById('mobileTutorialText');
    
    if (titleElement) {
        titleElement.textContent = step.title;
    }
    
    if (textElement) {
        textElement.innerHTML = `<span class="block text-sm text-blue-600 mb-2">Stap ${currentMobileStep + 1} van ${mobileTutorialSteps.length}</span>${step.text}`;
    }
    
    updateMobileTutorialButtons();
}

function updateMobileTutorialButtons() {
    const prevBtn = document.getElementById('mobilePrevBtn');
    const nextBtn = document.getElementById('mobileNextBtn');
    
    if (prevBtn) {
        prevBtn.style.display = currentMobileStep === 0 ? 'none' : 'block';
    }
    
    if (nextBtn) {
        if (currentMobileStep === mobileTutorialSteps.length - 1) {
            nextBtn.textContent = 'Klaar';
        } else {
            nextBtn.textContent = 'Volgende';
        }
    }
}

function nextMobileStep() {
    if (currentMobileStep < mobileTutorialSteps.length - 1) {
        currentMobileStep++;
        updateMobileTutorialContent();
    } else {
        closeMobileTutorial();
    }
}

function prevMobileStep() {
    if (currentMobileStep > 0) {
        currentMobileStep--;
        updateMobileTutorialContent();
    }
}

// Initialize tutorial on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(showMobileTutorialIfNeeded, 500);
});

// Add keyboard support
document.addEventListener('keydown', function(e) {
    const overlay = document.getElementById('mobileTutorialOverlay');
    if (overlay && overlay.style.display !== 'none' && overlay.style.display !== '') {
        if (e.key === 'ArrowRight') {
            nextMobileStep();
        } else if (e.key === 'ArrowLeft') {
            prevMobileStep();
        } else if (e.key === 'Escape') {
            closeMobileTutorial();
        }
    }
});
