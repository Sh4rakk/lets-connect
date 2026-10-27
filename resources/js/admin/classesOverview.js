import { apiGet } from '../api';

// Het klassen overzicht: kies een klas en zie hoeveel studenten er een account hebben en ingeschreven zijn.
export default () => ({
    classes: [],
    selectedName: '',
    users: [],
    loadingClass: false,
    exporting: false,

    async init() {
        const json = await apiGet('/api/classes', { orderBy: 'name' });
        this.classes = json.data;

        // de gekozen klas staat in de url, zodat je de pagina kan verversen of delen
        this.selectedName = new URLSearchParams(window.location.search).get('class') ?? '';
        if (this.selectedName) {
            this.loadClass();
        }
    },

    // { 'Software developer': [klas, klas], ... }
    get groups() {
        const groups = {};
        this.classes.forEach((schoolClass) => {
            (groups[schoolClass.class_group] ??= []).push(schoolClass);
        });
        return groups;
    },

    get selectedClass() {
        return this.classes.find((schoolClass) => schoolClass.name === this.selectedName) ?? null;
    },

    // net als vroeger: ingeschreven = precies 3 workshops gekozen
    get registeredStudents() {
        return this.users.filter((user) => user.bookings_count === 3).length;
    },

    get isComplete() {
        return this.selectedClass && Number(this.selectedClass.total_amount) === this.registeredStudents;
    },

    async loadClass() {
        window.history.replaceState(null, '', this.selectedName ? `?class=${encodeURIComponent(this.selectedName)}` : window.location.pathname);

        if (!this.selectedName) {
            this.users = [];
            return;
        }

        this.loadingClass = true;
        const json = await apiGet('/api/users', { where: { class: this.selectedName }, withCount: ['bookings'], orderBy: 'name' });
        this.users = json.data;
        this.loadingClass = false;
    },

    // de export duurt even, laat een melding zien en ga dan naar de download
    startExport(url) {
        this.exporting = true;
        setTimeout(() => window.location.href = url, 100);
    },
});
