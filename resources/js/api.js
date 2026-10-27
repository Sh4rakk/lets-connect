// Kleine helpers om de api aan te roepen. Alle Alpine componenten gebruiken deze.
// De api gebruikt de gewone sessie login, dus we hoeven alleen het CSRF token mee te sturen.

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

// Maakt van { where: { name: { in: 'jan' } }, withCount: ['bookings'] }
// de query string die het package verwacht: where[name][in]=jan&withCount[0]=bookings
function toQueryString(params, prefix = '') {
    const parts = [];

    Object.entries(params).forEach(([key, value]) => {
        const name = prefix ? `${prefix}[${key}]` : key;

        if (value === null || value === undefined || value === '') {
            return;
        }

        if (typeof value === 'object') {
            const nested = toQueryString(value, name);
            if (nested) parts.push(nested);
        } else {
            parts.push(`${encodeURIComponent(name)}=${encodeURIComponent(value)}`);
        }
    });

    return parts.join('&');
}

// De eerste foutmelding uit een laravel/api antwoord
export function errorMessage(json, fallback = 'Er ging iets mis, probeer het opnieuw.') {
    if (json?.errors) {
        return Object.values(json.errors)[0][0];
    }

    return json?.data?.message ?? json?.message ?? fallback;
}

export async function apiGet(url, params = {}) {
    const query = toQueryString(params);
    const response = await fetch(query ? `${url}?${query}` : url, {
        headers: { 'Accept': 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`GET ${url} gaf status ${response.status}`);
    }

    return response.json();
}

// Voor POST/PATCH/DELETE. Geeft altijd { ok, status, json } terug, ook bij een fout.
export async function apiSend(method, url, body = {}) {
    const response = await fetch(url, {
        method: method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify(body),
    });

    let json = null;

    try {
        json = await response.json();
    } catch (e) {
        // geen json terug gekregen
    }

    return { ok: response.ok, status: response.status, json: json };
}
