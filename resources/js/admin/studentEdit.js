import { apiSend } from '../api';

// De workshops van een student aanpassen (per ronde), opslaan via PATCH /api/users/{id}/bookings.
// bookingsByRound en optionsByRound komen uit UserController::edit.
export default ({ userId, bookingsByRound, optionsByRound }) => ({
    userId: userId,
    bookingsByRound: bookingsByRound,
    optionsByRound: optionsByRound,

    // de gekozen workshop moment ids in de selects, '' = niet ingeschreven
    rounds: {
        1: bookingsByRound[1]?.wm_id ?? '',
        2: bookingsByRound[2]?.wm_id ?? '',
        3: bookingsByRound[3]?.wm_id ?? '',
    },

    errors: {},
    saving: false,
    message: null,

    async save() {
        this.saving = true;
        this.errors = {};
        this.message = null;

        const result = await apiSend('PATCH', `/api/users/${this.userId}/bookings`, { rounds: this.rounds });

        this.saving = false;

        if (!result.ok) {
            if (result.status === 422 && result.json?.errors) {
                // 'rounds.2' => ['...'] wordt { 2: '...' }
                Object.entries(result.json.errors).forEach(([key, messages]) => {
                    this.errors[key.replace('rounds.', '')] = messages[0];
                });
            } else {
                this.message = { color: 'red', text: 'Opslaan is mislukt, probeer het opnieuw.' };
            }
            return;
        }

        // de "Huidig:" regels bijwerken met wat er nu echt is opgeslagen
        const saved = { 1: null, 2: null, 3: null };
        result.json.data.forEach((booking) => {
            const workshopMoment = booking.workshopMoments;
            saved[workshopMoment.moment_id] = {
                wm_id: workshopMoment.id,
                workshop: workshopMoment.workshop?.name ?? 'Onbekende workshop',
                time: workshopMoment.moment?.time ?? '',
            };
        });

        this.bookingsByRound = saved;
        this.message = { color: 'green', text: 'Inschrijvingen bijgewerkt.' };
    },
});
