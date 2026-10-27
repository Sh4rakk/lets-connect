// Het registratie formulier: klassen per opleiding en een "kloppen deze gegevens?" popup voor het versturen.
// (Was public/js/register.js en public/js/registerConfirm.js)

const klassen = {
    opleiding1: ['MV2A', 'MV2B', 'MV2C'],
    opleiding2: ['BOW3B', 'BOW3C', 'BOW2V', 'BOW3D', 'BOW2A', 'BOW2B', 'BOW2C'],
    opleiding3: ['P2.1', 'P2.2'],
    opleiding4: ['AR2MZ'],
    opleiding5: ['AR2AC'],
    opleiding6: ['IT2A'],
    opleiding7: ['SD2A', 'SD2B'],
    opleiding8: ['AV3B'],
    opleiding9: ['AV3A'],
    opleiding10: ['2FR', '2FC', '2AFT', '2FT', '2FDA', '2FDB'],
    opleiding11: ['CD2A', 'CD2B'],
};

// de eerste 8-9 tekens van het e-mailadres moeten cijfers zijn (de server controleert dit ook)
function heeftStudentnummer(email) {
    const firstPart = email.trim().split('@')[0].substring(0, 9);
    return /^\d{8,9}$/.test(firstPart);
}

export default ({ opleiding = '', klas = '' } = {}) => ({
    opleiding: opleiding,
    klas: klas,
    confirmOpen: false,
    confirmed: false,
    submitting: false,
    summary: '',

    get klassen() {
        return klassen[this.opleiding] ?? [];
    },

    // net als vroeger: een andere opleiding kiezen maakt de klas weer leeg
    opleidingChanged() {
        this.klas = '';
    },

    submit(event) {
        if (this.submitting) {
            event.preventDefault();
            return;
        }

        // na "Ja" in de popup mag het formulier echt verstuurd worden
        if (this.confirmed) {
            this.submitting = true;
            return;
        }

        event.preventDefault();

        if (!heeftStudentnummer(this.$refs.email.value)) {
            alert('De eerste 8-9 tekens van je e-mailadres moeten alleen cijfers zijn.');
            this.$refs.email.focus();
            return;
        }

        const course = this.$refs.opleiding.options[this.$refs.opleiding.selectedIndex].text;
        this.summary = `Naam: ${this.$refs.name.value}\nEmail: ${this.$refs.email.value}\nOpleiding: ${course}\nKlas: ${this.klas}`;
        this.confirmOpen = true;
    },

    confirm() {
        if (this.submitting) {
            return;
        }

        this.confirmed = true;
        this.confirmOpen = false;
        this.$refs.form.requestSubmit();
    },
});
