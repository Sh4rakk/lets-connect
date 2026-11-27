import { apiSend, errorMessage } from '../api';

// Het workshop overzicht: inschrijvingen open/dicht zetten en wisselen van ronde.
export default ({ signupsOpen, firstRound }) => ({
    signupsOpen: signupsOpen,
    toggling: false,
    message: null,
    round: firstRound,

    async toggleSignups() {
        this.toggling = true;

        const result = await apiSend('POST', '/api/settings/toggle-signups');

        this.toggling = false;

        if (result.ok) {
            this.signupsOpen = result.json.data.signups_open;
            this.message = { color: 'green', text: 'Inschrijvingen status gewijzigd.' };
        } else {
            this.$root.querySelector('[data-signups]').checked = this.signupsOpen;
            this.message = { color: 'red', text: errorMessage(result.json) };
        }
    },
});
