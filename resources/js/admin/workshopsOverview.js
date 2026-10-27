import { apiSend, errorMessage } from '../api';

// Het workshop overzicht: inschrijvingen open/dicht zetten en de lijst in- en uitklappen.
export default ({ signupsOpen, workshopNames }) => ({
    signupsOpen: signupsOpen,
    toggling: false,
    message: null,

    // per workshop of hij open is, standaard alles open
    open: Object.fromEntries(workshopNames.map((name) => [name, true])),

    get anyOpen() {
        return Object.values(this.open).some((isOpen) => isOpen);
    },

    toggle(name) {
        this.open[name] = !this.open[name];
    },

    toggleAll() {
        const newState = !this.anyOpen;
        Object.keys(this.open).forEach((name) => this.open[name] = newState);
    },

    async toggleSignups() {
        this.toggling = true;

        const result = await apiSend('POST', '/api/settings/toggle-signups');

        this.toggling = false;

        if (result.ok) {
            this.signupsOpen = result.json.data.signups_open;
            this.message = { color: 'green', text: 'Inschrijvingen status gewijzigd.' };
        } else {
            this.message = { color: 'red', text: errorMessage(result.json) };
        }
    },
});
