import { apiGet } from '../api';

// Studenten zoeken op naam, email en klas via /api/users.
// Zoekt meteen tijdens het typen, de filters staan ook in de url zodat je de pagina kan delen.
export default () => ({
    filters: { name: '', email: '', class: '' },
    users: [],
    loading: true,
    failed: false,

    init() {
        const url = new URLSearchParams(window.location.search);
        this.filters.name = url.get('name') ?? '';
        this.filters.email = url.get('email') ?? '';
        this.filters.class = url.get('class') ?? '';

        this.search();
    },

    async search() {
        this.loading = true;
        this.failed = false;

        // "in" betekent bij het package: de kolom bevat deze tekst
        const where = {};
        if (this.filters.name) where.name = { in: this.filters.name };
        if (this.filters.email) where.email = { in: this.filters.email };
        if (this.filters.class) where.class = this.filters.class;

        try {
            const json = await apiGet('/api/users', { where: where, orderBy: 'created_at' });
            this.users = json.data;
        } catch (e) {
            this.failed = true;
        }

        this.loading = false;
        this.updateUrl();
    },

    clearFilters() {
        this.filters = { name: '', email: '', class: '' };
        this.search();
    },

    updateUrl() {
        const params = new URLSearchParams();
        Object.entries(this.filters).forEach(([key, value]) => {
            if (value) params.set(key, value);
        });

        const query = params.toString();
        window.history.replaceState(null, '', query ? `?${query}` : window.location.pathname);
    },
});
