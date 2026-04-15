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
    const overlay = document.getElementById('mobileTutorial');
    if (!overlay) {
        console.warn('mobile tutorial overlay not found');
        return;
    }

    const widthLimit = 1024; // include tablet/large phone
    if ((window.innerWidth <= widthLimit || window.matchMedia('(max-width: 1024px)').matches) && !localStorage.getItem('mobileTutorialSeen')) {
        overlay.style.display = 'flex';
        showMobileTutorial();
        localStorage.setItem('mobileTutorialSeen', 'true');
    }
}

function showMobileTutorial() {
    currentMobileStep = 0;
    updateMobileTutorialContent();
    const overlay = document.getElementById('mobileTutorial');
    if (overlay) overlay.style.display = 'flex';
}

function closeMobileTutorial() {
    const overlay = document.getElementById('mobileTutorial');
    if (overlay) overlay.style.display = 'none';
}

function skipMobileTutorial() {
    localStorage.setItem('mobileTutorialSeen', 'true');
    closeMobileTutorial();
}

function updateMobileTutorialContent() {
    const step = mobileTutorialSteps[currentMobileStep];
    const titleElement = document.getElementById('mobileTutorialTitle');
    const textElement = document.getElementById('mobileTutorialText');
    const stepInfo = `${currentMobileStep + 1} van ${mobileTutorialSteps.length}`;

    if (titleElement) titleElement.textContent = step.title;

    if (textElement) textElement.innerHTML = `<span class="block text-sm text-blue-600 mb-2">Stap ${stepInfo}</span>${step.text}`;

    updateMobileTutorialButtons();
}

function updateMobileTutorialButtons() {
    const prevBtn = document.getElementById('mobilePrevBtn');
    const nextBtn = document.getElementById('mobileNextBtn');

    if (prevBtn) prevBtn.style.display = currentMobileStep === 0 ? 'none' : 'block';
    if (nextBtn) nextBtn.textContent = currentMobileStep === mobileTutorialSteps.length - 1 ? 'Klaar' : 'Volgende';
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

function mobileTutorialInit() {
    showMobileTutorialIfNeeded();
}

document.addEventListener('DOMContentLoaded', mobileTutorialInit);
window.addEventListener('load', mobileTutorialInit);
window.addEventListener('resize', mobileTutorialInit);

document.addEventListener('keydown', function(e) {
    const overlay = document.getElementById('mobileTutorial');
    if (!overlay || overlay.style.display === 'none') return;
    if (e.key === 'ArrowRight') nextMobileStep();
    if (e.key === 'ArrowLeft') prevMobileStep();
    if (e.key === 'Escape') closeMobileTutorial();
});
