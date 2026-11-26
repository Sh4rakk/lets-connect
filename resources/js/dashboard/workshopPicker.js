import { fetchWorkshopCapacities, saveWorkshopChoice } from './api';

// Het student dashboard: workshops kiezen voor ronde 1, 2 en 3.
// Desktop en mobiel gebruiken dezelfde state.
export default ({ workshops, registrationsClosed }) => ({
    workshops: workshops,
    registrationsClosed: registrationsClosed,

    // { workshopId: { ronde: plekken over } }, een ronde die ontbreekt heeft die workshop niet
    capacities: {},
    capacitiesLoaded: false,

    // per ronde het id van de gekozen workshop
    selected: { 1: null, 2: null, 3: null },
    roundTimes: { 1: '13:00 - 13:45', 2: '13:45 - 14:30', 3: '15:00 - 15:45' },

    // desktop
    confirmOpen: false,

    // mobiel
    modalWorkshopId: null,
    infoWorkshop: null,
    mobileConfirmOpen: false,

    saving: false,
    error: null,

    async init() {
        try {
            this.capacities = await fetchWorkshopCapacities();
        } catch (e) {
            // zonder capaciteit kan je nog steeds kiezen, de server controleert het bij opslaan
        }

        this.capacitiesLoaded = true;
    },

    // ---------- algemeen ----------

    workshop(id) {
        return this.workshops.find((workshop) => workshop.id === id);
    },

    // de workshops die nog niet in een ronde staan, in de originele volgorde
    get availableWorkshops() {
        const chosen = Object.values(this.selected);
        return this.workshops.filter((workshop) => !chosen.includes(workshop.id));
    },

    get allRoundsSelected() {
        return [1, 2, 3].every((round) => this.selected[round]);
    },

    roundOf(id) {
        return [1, 2, 3].find((round) => this.selected[round] === id) ?? null;
    },

    spotsLeft(id, round) {
        return this.capacities[id]?.[round];
    },

    // de regels onder een workshop kaartje, bijvoorbeeld "Ronde 1: 5 plek(ken) over"
    capacityLines(id) {
        const rounds = this.capacities[id] ?? {};

        return Object.keys(rounds).sort().map((round) => {
            const spots = rounds[round];
            return `Ronde ${round}: ${spots > 0 ? spots + ' plek(ken) over' : 'workshop zit vol!'}`;
        });
    },

    // waarom een workshop niet in deze ronde mag, of null als het wel mag
    roundProblem(id, round) {
        if (!this.capacitiesLoaded) {
            return null;
        }

        const spots = this.spotsLeft(id, round);

        if (spots === undefined) {
            return 'De geselecteerde workshop heeft niet deze ronde!';
        }

        if (spots <= 0) {
            return 'Deze ronde zit vol!';
        }

        return null;
    },

    showError(message, title = 'Foutmelding') {
        this.error = { title: title, message: message };
    },

    async save() {
        if (!this.allRoundsSelected || this.saving) {
            return;
        }

        this.saving = true;

        const result = await saveWorkshopChoice({ ...this.selected });

        if (result.ok) {
            window.location.href = '/success';
            return;
        }

        this.saving = false;
        this.confirmOpen = false;
        this.mobileConfirmOpen = false;
        this.showError(result.message);
    },

    // ---------- desktop: eerst een ronde, dan een workshop aanklikken ----------

    // in welke ronde de workshop gezet wordt als je erop klikt
    activeRound: 1,
    infoCard: null,

    // de tekst onder een kaartje voor de actieve ronde
    spotsText(id) {
        if (!this.capacitiesLoaded) {
            return '';
        }

        const spots = this.spotsLeft(id, this.activeRound);

        if (spots === undefined) {
            return 'Niet in deze ronde';
        }

        return spots > 0 ? `${spots} plek${spots === 1 ? '' : 'ken'} over` : 'Vol';
    },

    // de regel onderaan een kaartje
    cardStatus(id) {
        if (this.selected[this.activeRound] === id) {
            return { text: '✓ Gekozen', class: 'text-deltion-blue-900' };
        }

        const round = this.roundOf(id);
        if (round) {
            return { text: `Gekozen voor ronde ${round}`, class: 'text-gray-500' };
        }

        const text = this.spotsText(id);
        return { text: text, class: text === 'Vol' ? 'text-red-700' : 'text-gray-600' };
    },

    // kan deze workshop nu in de actieve ronde? (al gekozen in een andere ronde telt als nee)
    canChoose(id) {
        const round = this.roundOf(id);

        return !this.registrationsClosed
            && (round === null || round === this.activeRound)
            && this.roundProblem(id, this.activeRound) === null;
    },

    choose(id) {
        if (this.registrationsClosed) {
            return;
        }

        // nog een keer klikken op de gekozen workshop haalt hem weer weg
        if (this.selected[this.activeRound] === id) {
            this.selected[this.activeRound] = null;
            return;
        }

        const otherRound = this.roundOf(id);
        if (otherRound) {
            this.showError(`Deze workshop heb je al gekozen voor ronde ${otherRound}.`);
            return;
        }

        const problem = this.roundProblem(id, this.activeRound);
        if (problem) {
            this.showError(problem);
            return;
        }

        this.selected[this.activeRound] = id;
        this.infoCard = null;

        // door naar de eerstvolgende ronde die nog leeg is
        const next = [1, 2, 3].find((round) => !this.selected[round]);
        if (next) {
            this.activeRound = next;
        }
    },

    removeFromRound(round) {
        this.selected[round] = null;
        this.activeRound = round;
        this.confirmOpen = false;
    },

    // ---------- mobiel ----------

    pickMobile(id) {
        if (this.registrationsClosed) {
            return;
        }

        this.modalWorkshopId = id;
    },

    modalRoundDisabled(round) {
        return Boolean(this.selected[round]) || this.roundProblem(this.modalWorkshopId, round) !== null;
    },

    modalRoundText(round) {
        let text = `Ronde ${round} (${this.roundTimes[round]})`;

        if (!this.capacitiesLoaded) {
            text += ' - capaciteit onbekend';
        } else {
            const spots = this.spotsLeft(this.modalWorkshopId, round);

            if (spots === undefined) {
                text += ' - niet in deze ronde';
            } else {
                text += spots > 0 ? ` - ${spots} plekken over` : ' - workshop zit vol!';
            }
        }

        if (this.selected[round]) {
            text += ` - Gekozen: ${this.workshop(this.selected[round]).name}`;
        }

        return text;
    },

    addToRoundMobile(round) {
        if (this.modalWorkshopId === null || this.modalRoundDisabled(round)) {
            return;
        }

        this.selected[round] = this.modalWorkshopId;
        this.modalWorkshopId = null;
    },

    openMobileSave() {
        if (!this.allRoundsSelected) {
            this.showError('Selecteer eerst een workshop voor alle rondes.');
            return;
        }

        this.mobileConfirmOpen = true;
    },
});
