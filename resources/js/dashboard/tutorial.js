// De tutorial op desktop. Het is een store zodat de workshop kaartjes en rondes
// in de html kunnen zien welke stap actief is (voor het oplichten).
//
// Stappen:
// 0 welkom
// 1 klik op de i van de eerste workshop  -> gaat vanzelf verder
// 2 sluit het info venster              -> gaat vanzelf verder
// 3 sleep een workshop naar ronde 1     -> gaat vanzelf verder
// 4 haal de workshop weer uit de ronde  -> gaat vanzelf verder
// 5 klaar, sluit na 5 seconden
export default {
    active: false,
    step: 0,
    autoCloseTimeout: null,

    texts: [
        'Welkom op het dashboard! Laten we leren hoe je deze pagina gebruikt.',
        'Klik op het informatie-icoon (i) van een workshop om de workshopinfo te openen. De knop ‘Volgende’ wordt daarna actief.',
        'Klik op het rode kruisje rechtsboven in het info-venster om het te sluiten.',
        'Pak het workshopkaartje vast en sleep het naar ronde 1.',
        'Klik op het kleine rode kruisje (x) op de workshop in de ronde om deze weer te verwijderen.',
        'Tutorial voltooid! Je kunt nu zelf workshops slepen.',
    ],

    init() {
        // app.js laadt op elke pagina, de tutorial hoort alleen bij het dashboard
        if (!document.getElementById('tutorial-overlay')) {
            return;
        }

        const isDesktop = window.innerWidth >= 768;
        const seen = document.cookie.includes('tutorialSeen=true');

        if (isDesktop && !seen) {
            this.active = true;
            document.cookie = 'tutorialSeen=true; path=/';
        }
    },

    get isLastStep() {
        return this.step === this.texts.length - 1;
    },

    // bij deze stappen moet de student eerst iets doen
    get nextDisabled() {
        return [1, 2, 4].includes(this.step);
    },

    get nextLabel() {
        if (this.step === 1) return 'Klik eerst op i';
        if (this.isLastStep) return 'Sluit tutorial';
        return 'Volgende';
    },

    is(step) {
        return this.active && this.step === step;
    },

    next() {
        if (this.isLastStep) {
            this.end();
            return;
        }

        this.step++;

        if (this.isLastStep) {
            this.autoCloseTimeout = setTimeout(() => this.end(), 5000);
        }
    },

    prev() {
        this.step = Math.max(this.step - 1, 0);
    },

    end() {
        clearTimeout(this.autoCloseTimeout);
        this.active = false;
        this.step = 0;
    },

    skip() {
        document.cookie = 'tutorialSeen=true; path=/';
        this.end();
    },

    // de workshop picker meldt wat de student doet
    event(name) {
        if (!this.active) {
            return;
        }

        if (this.step === 1 && name === 'info-opened') {
            setTimeout(() => this.next(), 300);
        } else if (this.step === 2 && name === 'info-closed') {
            this.next();
        } else if (this.step === 3 && name === 'dropped') {
            this.next();
        } else if (this.step === 4 && name === 'removed') {
            this.next();
        }
    },
};
