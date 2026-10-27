import { apiGet, apiSend, errorMessage } from '../api';

// Haalt per workshop per ronde op hoeveel plekken er nog over zijn.
// Geeft terug: { workshopId: { 1: spotsLeft, 2: spotsLeft, 3: spotsLeft } }
// Een ronde die er niet in staat, heeft die workshop niet.
export async function fetchWorkshopCapacities() {
    const json = await apiGet('/api/workshop-moments');
    const capacities = {};

    json.data.forEach((workshopMoment) => {
        const workshopId = String(workshopMoment.workshop_id);

        if (!capacities[workshopId]) {
            capacities[workshopId] = {};
        }

        capacities[workshopId][workshopMoment.moment_id] = workshopMoment.spots_left;
    });

    return capacities;
}

// Slaat de keuze op. rounds = { 1: workshopId, 2: workshopId, 3: workshopId }
// Geeft terug: { ok: true } of { ok: false, message: 'foutmelding' }
export async function saveWorkshopChoice(rounds) {
    const result = await apiSend('POST', '/api/bookings', { rounds: rounds });

    if (result.ok) {
        return { ok: true };
    }

    return { ok: false, message: errorMessage(result.json, 'Er ging iets mis tijdens het opslaan, probeer het opnieuw.') };
}
