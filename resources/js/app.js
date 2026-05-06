import './bootstrap';

import Alpine from 'alpinejs';
import * as Sentry from "@sentry/browser";

Sentry.init({
    dsn: "https://7781e93829890a07687c40a296a1deba@o4511341734395904.ingest.de.sentry.io/4511341749076048",
});

const originalWarn = console.warn;
console.warn = function(...args) {
    Sentry.captureMessage(args.join(" "), "warning");
    originalWarn.apply(console, args);
};

window.Alpine = Alpine;

Alpine.start();
