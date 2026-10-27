import { apiGet } from '../api';

// De cijfers op het admin dashboard, uit /api/statistics.
export default () => ({
    stats: null,
    failed: false,

    async init() {
        try {
            const json = await apiGet('/api/statistics');
            this.stats = json.data;
        } catch (e) {
            this.failed = true;
        }
    },

    get latestUser() {
        const user = this.stats?.latest_user;

        if (!user?.name) {
            return 'N/A';
        }

        return `${user.name} (${new Date(user.created_at).toLocaleDateString('nl-NL', { day: '2-digit', month: '2-digit', year: 'numeric' })})`;
    },

    get popularWorkshop() {
        const workshop = this.stats?.popular_workshop;

        if (!workshop?.name) {
            return 'N/A (0 bookings)';
        }

        return `${workshop.name} (${workshop.bookings_count ?? 0} bookings)`;
    },

    // laat '...' zien zolang de cijfers nog laden
    value(key) {
        return this.stats ? this.stats[key] : '...';
    },
});
