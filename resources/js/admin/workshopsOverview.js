import { apiSend, errorMessage } from '../api';

// Het workshop overzicht: tabs per ronde en inschrijvingen open/dicht zetten.
export default ({ signupsOpen, firstRound }) => ({
    signupsOpen: signupsOpen,
    toggling: false,
    message: null,

    // de gekozen ronde blijft bewaard, dan kom je na bewerken terug op dezelfde tab
    round: (() => {
        try {
            return Number(sessionStorage.getItem('workshopsOverviewRound')) || firstRound;
        } catch {
            return firstRound;
        }
    })(),

    init() {
        this.$watch('round', (value) => {
            try {
                sessionStorage.setItem('workshopsOverviewRound', value);
            } catch {}
        });
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
