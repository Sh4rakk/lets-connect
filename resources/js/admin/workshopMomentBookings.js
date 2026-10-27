import { apiGet } from '../api';

// De studenten die in een workshop ronde zitten, eventueel alleen van één klas.
export default ({ workshopMomentId, className }) => ({
    bookings: [],
    className: className,
    loading: true,
    error: null,

    async init() {
        // als de api faalt willen we een melding, niet voor altijd "laden..."
        try {
            const json = await apiGet(`/api/workshop-moments/${workshopMomentId}`, { with: ['bookings.student'] });
            this.bookings = json.data.bookings;
        } catch (e) {
            this.error = 'De inschrijvingen konden niet geladen worden, ververs de pagina.';
        } finally {
            this.loading = false;
        }
    },

    get visibleBookings() {
        if (!this.className) {
            return this.bookings;
        }

        return this.bookings.filter((booking) => booking.student?.class === this.className);
    },
});
