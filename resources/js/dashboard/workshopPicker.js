import Alpine from 'alpinejs';
import { fetchWorkshopCapacities, saveWorkshopChoice } from './api';

// Het student dashboard: workshops kiezen voor ronde 1, 2 en 3.
// Desktop (slepen) en mobiel (klikken) gebruiken dezelfde state.
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
    draggingId: null,
    hoverId: null,
    openInfoId: null,
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

    // ---------- desktop: slepen ----------

    startDrag(event, id) {
        if (this.registrationsClosed) {
            event.preventDefault();
            return;
        }

        this.draggingId = id;
        this.openInfoId = null;
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text', id);
    },

    dropOnRound(round) {
        const id = this.draggingId;
        this.draggingId = null;

        if (!id || this.selected[round] === id) {
            return;
        }

        const problem = this.roundProblem(id, round);
        if (problem) {
            this.showError(problem);
            return;
        }

        const fromRound = this.roundOf(id);
        const current = this.selected[round];

        // van ronde naar ronde slepen: de workshops wisselen van plek
        if (fromRound) {
            const swapProblem = current ? this.roundProblem(current, fromRound) : null;
            if (swapProblem) {
                this.showError(swapProblem);
                return;
            }

            this.selected[fromRound] = current;
        }

        // een workshop die al in deze ronde stond gaat vanzelf terug naar de lijst
        this.selected[round] = id;

        Alpine.store('tutorial').event('dropped');

        if (this.allRoundsSelected) {
            this.confirmOpen = true;
        }
    },

    // een workshop uit een ronde terug naar de lijst slepen
    dropOnList() {
        const round = this.roundOf(this.draggingId);
        this.draggingId = null;

        if (round) {
            this.removeFromRound(round);
        }
    },

    removeFromRound(round) {
        this.selected[round] = null;
        this.confirmOpen = false;

        Alpine.store('tutorial').event('removed');
    },

    // ---------- desktop: info popup ----------

    toggleInfo(id) {
        this.openInfoId = this.openInfoId === id ? null : id;

        if (this.openInfoId) {
            Alpine.store('tutorial').event('info-opened');
        }
    },

    closeInfo() {
        this.openInfoId = null;

        Alpine.store('tutorial').event('info-closed');
    },

    // klikken naast een info popup sluit hem (telt ook voor de tutorial, anders zit je vast)
    closeInfoOnOutsideClick(event) {
        if (this.openInfoId && !event.target.closest('.info, .popup')) {
            this.closeInfo();
        }
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
