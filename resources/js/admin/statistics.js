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

    get latestUserDate() {
        const createdAt = this.stats?.latest_user?.created_at;

        if (!createdAt) {
            return '';
        }

        return new Date(createdAt).toLocaleDateString('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' });
    },

    // hoeveel procent van de studenten minstens 1 workshop heeft
    get signedUpPercentage() {
        const total = this.stats?.total_students ?? 0;

        return total > 0 ? Math.min(100, Math.round((this.stats.total_bookings / total) * 100)) : 0;
    },

    get workshopsTotal() {
        return (this.stats?.rounds ?? []).reduce((sum, round) => sum + round.workshops, 0);
    },

    get fullTotal() {
        return (this.stats?.rounds ?? []).reduce((sum, round) => sum + round.full, 0);
    },

    // hoe vol een ronde zit, in procenten
    fill(round) {
        return round.capacity > 0 ? Math.min(100, Math.round((round.booked / round.capacity) * 100)) : 0;
    },

    // laat '...' zien zolang de cijfers nog laden
    value(key) {
        return this.stats ? this.stats[key] : '...';
    },
});
