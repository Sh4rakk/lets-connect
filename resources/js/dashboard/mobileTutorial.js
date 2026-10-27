// De tutorial op mobiel: een paar uitleg schermen achter elkaar.
export default () => ({
    open: false,
    step: 0,

    steps: [
        { title: 'Welkom!', text: "Welkom bij Let's Connect! Dit is je workshop selectie interface." },
        { title: 'Workshops', text: 'Scroll door de beschikbare workshops. Elk kaartje toont de workshop naam en korte beschrijving.' },
        { title: 'Workshop Kiezen', text: 'Klik op een workshop kaartje om deze toe te voegen aan een ronde.' },
        { title: 'Ronde Selecteren', text: 'Kies de ronde waar je deze workshop wilt toevoegen: Ronde 1, 2 of 3.' },
        { title: 'Overzicht', text: 'Bovenaan zie je welke workshops je hebt geselecteerd voor elke ronde.' },
        { title: 'Opslaan', text: "Wanneer je alle drie rondes hebt ingevuld, klik 'Opslaan' onderin het scherm om je keuzes op te slaan." },
        { title: 'Gereed!', text: 'Je bent nu klaar! Je keuzes zijn opgeslagen.' },
    ],

    init() {
        let seen = false;

        try {
            seen = localStorage.getItem('mobileTutorialSeen') !== null;
            localStorage.setItem('mobileTutorialSeen', 'true');
        } catch (e) {
            // localStorage kan geblokkeerd zijn, dan laten we de tutorial gewoon zien
        }

        if (window.innerWidth < 768 && !seen) {
            this.open = true;
        }
    },

    get current() {
        return this.steps[this.step];
    },

    get isLastStep() {
        return this.step === this.steps.length - 1;
    },

    next() {
        if (this.isLastStep) {
            this.open = false;
            return;
        }

        this.step++;
    },

    prev() {
        this.step = Math.max(this.step - 1, 0);
    },

    // pijltjes en escape werken ook, net als vroeger
    onKeydown(event) {
        if (!this.open) return;
        if (event.key === 'ArrowRight') this.next();
        if (event.key === 'ArrowLeft') this.prev();
        if (event.key === 'Escape') this.open = false;
    },
});
