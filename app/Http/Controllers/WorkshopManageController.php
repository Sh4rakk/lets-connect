<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Moment;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

// Workshops handmatig toevoegen, bewerken en verwijderen, zonder de seeder
class WorkshopManageController extends Controller
{
    public function create()
    {
        return view('dashboard.workshop-form', [
            'workshop' => new Workshop(),
            'rounds' => $this->rounds(null),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateWorkshop($request);

        $workshop = DB::transaction(function () use ($request, $data) {
            $workshop = Workshop::create([
                'name' => $data['name'],
                'location' => $data['location'] ?? null,
                'full_description' => $data['full_description'] ?? null,
                'capacity' => $this->defaultCapacity($data['rounds']),
                'is_manual' => true,
            ]);

            if ($request->hasFile('image')) {
                $workshop->update(['image_url' => $this->storeImage($request->file('image'))]);
            }

            $this->syncRounds($workshop, $data['rounds']);

            return $workshop;
        });

        return redirect()->route('workshop-dashboard')
            ->with('status', "Workshop {$workshop->name} is toegevoegd.");
    }

    public function edit(Workshop $workshop)
    {
        return view('dashboard.workshop-form', [
            'workshop' => $workshop,
            'rounds' => $this->rounds($workshop),
        ]);
    }

    public function update(Request $request, Workshop $workshop)
    {
        $data = $this->validateWorkshop($request);

        DB::transaction(function () use ($request, $data, $workshop) {
            $this->syncRounds($workshop, $data['rounds']);

            $workshop->update([
                'name' => $data['name'],
                'location' => $data['location'] ?? null,
                'full_description' => $data['full_description'] ?? null,
                'capacity' => $this->defaultCapacity($data['rounds']),
            ]);

            if ($request->hasFile('image')) {
                $this->deleteImage($workshop);
                $workshop->update(['image_url' => $this->storeImage($request->file('image'))]);
            }
        });

        return redirect()->route('workshop-dashboard')
            ->with('status', "Workshop {$workshop->name} is opgeslagen.");
    }

    public function destroy(Workshop $workshop)
    {
        DB::transaction(function () use ($workshop) {
            // boekingen hebben geen cascade, dus eerst zelf weghalen
            Bookings::whereIn('wm_id', $workshop->workshopMoments()->pluck('id'))->delete();
            $this->deleteImage($workshop);
            $workshop->delete();
        });

        return redirect()->route('workshop-dashboard')
            ->with('status', "Workshop {$workshop->name} is verwijderd.");
    }

    private function validateWorkshop(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'full_description' => ['nullable', 'string', 'max:9999'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'rounds' => ['required', 'array'],
            'rounds.*.enabled' => ['nullable', 'boolean'],
            'rounds.*.capacity' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ], [
            'name.required' => 'Vul een titel in.',
            'image.image' => 'Kies een afbeelding (jpg, png of webp).',
            'image.max' => 'De afbeelding mag maximaal 4 MB zijn.',
            'rounds.*.capacity.min' => 'De capaciteit moet minimaal 1 zijn.',
        ]);

        $momentIds = Moment::pluck('id')->map(fn ($id) => (int) $id)->all();
        $rounds = [];

        foreach ($data['rounds'] as $momentId => $round) {
            if (!in_array((int) $momentId, $momentIds, true) || empty($round['enabled'])) {
                continue;
            }

            if (empty($round['capacity'])) {
                throw ValidationException::withMessages([
                    "rounds.$momentId.capacity" => 'Vul de capaciteit in voor deze ronde.',
                ]);
            }

            $rounds[(int) $momentId] = (int) $round['capacity'];
        }

        if ($rounds === []) {
            throw ValidationException::withMessages(['rounds' => 'Zet minimaal één ronde aan.']);
        }

        $data['rounds'] = $rounds;

        return $data;
    }

    // $rounds is [moment_id => capaciteit] met alleen de rondes die aan staan
    private function syncRounds(Workshop $workshop, array $rounds): void
    {
        $existing = $workshop->workshopMoments()->withCount('bookings')->get()->keyBy('moment_id');

        foreach ($existing as $momentId => $workshopMoment) {
            $capacity = $rounds[$momentId] ?? null;

            if ($capacity === null) {
                if ($workshopMoment->bookings_count > 0) {
                    throw ValidationException::withMessages([
                        "rounds.$momentId.enabled" => "Deze ronde heeft al {$workshopMoment->bookings_count} inschrijving(en) en kan niet uit.",
                    ]);
                }

                $workshopMoment->delete();
                continue;
            }

            if ($capacity < $workshopMoment->bookings_count) {
                throw ValidationException::withMessages([
                    "rounds.$momentId.capacity" => "Er staan al {$workshopMoment->bookings_count} studenten ingeschreven.",
                ]);
            }

            $workshopMoment->update(['capacity' => $capacity]);
        }

        foreach ($rounds as $momentId => $capacity) {
            if (!$existing->has($momentId)) {
                WorkshopMoment::create([
                    'workshop_id' => $workshop->id,
                    'moment_id' => $momentId,
                    'capacity' => $capacity,
                ]);
            }
        }
    }

    // per ronde: tijd, aan/uit, capaciteit en aantal inschrijvingen voor het formulier
    private function rounds(?Workshop $workshop)
    {
        $existing = $workshop
            ? $workshop->workshopMoments()->withCount('bookings')->get()->keyBy('moment_id')
            : collect();

        return Moment::orderBy('id')->get()->map(function (Moment $moment) use ($existing, $workshop) {
            $workshopMoment = $existing->get($moment->id);

            return [
                'id' => $moment->id,
                'time' => $moment->time,
                // nieuwe workshop: standaard in alle rondes
                'enabled' => $workshop ? (bool) $workshopMoment : true,
                'capacity' => $workshopMoment?->capacity ?? $workshop?->capacity ?? 20,
                'bookings' => $workshopMoment->bookings_count ?? 0,
            ];
        });
    }

    // de capaciteit op de workshop zelf blijft de standaard, bijv. voor het QR scherm
    private function defaultCapacity(array $rounds): int
    {
        return max($rounds);
    }

    private function storeImage(UploadedFile $image): string
    {
        return Storage::disk('public')->url($image->store('workshops', 'public'));
    }

    // alleen afbeeldingen die we zelf hebben opgeslagen, niet die van de scraper
    private function deleteImage(Workshop $workshop): void
    {
        $prefix = Storage::disk('public')->url('workshops/');

        if ($workshop->image_url && str_starts_with($workshop->image_url, $prefix)) {
            Storage::disk('public')->delete('workshops/' . basename($workshop->image_url));
        }
    }
}
