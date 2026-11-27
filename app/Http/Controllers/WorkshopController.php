<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workshops\SaveWorkshopRequest;
use App\Models\Moment;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class WorkshopController extends Controller
{
    private const IMAGE_PREFIX = '/storage/workshops/';

    public function create(): View
    {
        return $this->form(new Workshop());
    }

    public function store(SaveWorkshopRequest $request): RedirectResponse
    {
        $workshop = new Workshop();
        $workshop->is_manual = true;

        $this->save($workshop, $request);

        return redirect()->route('workshop-dashboard')->with('status', 'Workshop toegevoegd.');
    }

    public function edit(Workshop $workshop): View
    {
        return $this->form($workshop);
    }

    public function update(SaveWorkshopRequest $request, Workshop $workshop): RedirectResponse
    {
        $this->save($workshop, $request);

        return redirect()->route('workshop-dashboard')->with('status', 'Workshop opgeslagen.');
    }

    public function destroy(Workshop $workshop): RedirectResponse
    {
        DB::transaction(function () use ($workshop) {
            $workshop->workshopMoments()->lockForUpdate()->get();

            if ($workshop->bookings()->exists()) {
                throw ValidationException::withMessages([
                    'delete' => 'Deze workshop heeft inschrijvingen en kan niet worden verwijderd.',
                ]);
            }

            if ($workshop->workshopMoments()->has('feedback')->exists()) {
                throw ValidationException::withMessages([
                    'delete' => 'Deze workshop heeft feedback en kan niet worden verwijderd.',
                ]);
            }

            $workshop->workshopMoments()->delete();
            $workshop->delete();
        });

        $this->deleteImage($workshop->image_url);

        return redirect()->route('workshop-dashboard')->with('status', 'Workshop verwijderd.');
    }

    private function form(Workshop $workshop): View
    {
        $existing = $workshop->exists
            ? $workshop->workshopMoments()->with('workshop')->withCount('bookings')->get()->keyBy('moment_id')
            : collect();

        $rounds = Moment::orderBy('id')->get()->map(fn (Moment $moment) => [
            'id' => $moment->id,
            'time' => $moment->time,
            'enabled' => $existing->has($moment->id),
            'capacity' => $existing->get($moment->id)?->maxSpots(),
            'bookings' => $existing->get($moment->id)?->bookings_count ?? 0,
        ]);

        return view('workshops.form', [
            'workshop' => $workshop,
            'rounds' => $rounds,
        ]);
    }

    private function save(Workshop $workshop, SaveWorkshopRequest $request): void
    {
        $oldImage = $workshop->image_url;
        $newImage = $request->hasFile('image') ? $this->storeImage($request->file('image')) : null;

        try {
            DB::transaction(function () use ($workshop, $request, $newImage) {
                $rounds = $request->enabledRounds();

                $workshop->fill($request->safe()->only(['name', 'location', 'full_description']));
                $workshop->capacity = $rounds->max();
                $workshop->leader_id = $this->leaderId($request->validated('leader_email'));

                if ($newImage) {
                    $workshop->image_url = $newImage;
                }

                $workshop->save();

                $this->saveRounds($workshop, $rounds);
            });
        } catch (Throwable $e) {
            $this->deleteImage($newImage);

            throw $e;
        }

        if ($newImage) {
            $this->deleteImage($oldImage);
        }
    }

    private function saveRounds(Workshop $workshop, Collection $rounds): void
    {
        $existing = $workshop->workshopMoments()->lockForUpdate()->get()->keyBy('moment_id');

        foreach ($existing as $momentId => $workshopMoment) {
            $bookings = $workshopMoment->bookings()->count();

            if (! $rounds->has($momentId)) {
                if ($bookings > 0) {
                    throw ValidationException::withMessages([
                        "rounds.$momentId.capacity" => "Ronde $momentId heeft inschrijvingen en kan niet uit.",
                    ]);
                }

                if ($workshopMoment->feedback()->exists()) {
                    throw ValidationException::withMessages([
                        "rounds.$momentId.capacity" => "Ronde $momentId heeft feedback en kan niet uit.",
                    ]);
                }

                $workshopMoment->delete();
                continue;
            }

            if ($rounds[$momentId] < $bookings) {
                throw ValidationException::withMessages([
                    "rounds.$momentId.capacity" => "Er zijn al $bookings studenten ingeschreven voor deze ronde.",
                ]);
            }

            $workshopMoment->update(['capacity' => $rounds[$momentId]]);
        }

        foreach ($rounds->except($existing->keys()->all()) as $momentId => $capacity) {
            WorkshopMoment::create([
                'workshop_id' => $workshop->id,
                'moment_id' => $momentId,
                'capacity' => $capacity,
            ]);
        }
    }

    private function leaderId(?string $email): ?string
    {
        if (! $email) {
            return null;
        }

        return User::firstOrCreate(['email' => $email], [
            'name' => Str::headline(Str::before($email, '@')),
            'class' => User::LEADER_CLASS,
        ])->id;
    }

    private function storeImage(UploadedFile $file): string
    {
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'afbeelding';
        $name = Str::limit($name, 60, '').'-'.Str::lower(Str::random(8)).'.'.$file->extension();

        $file->storeAs('workshops', $name, 'public');

        return self::IMAGE_PREFIX.$name;
    }

    private function deleteImage(?string $url): void
    {
        if ($url && str_starts_with($url, self::IMAGE_PREFIX)) {
            Storage::disk('public')->delete('workshops/'.basename($url));
        }
    }
}
