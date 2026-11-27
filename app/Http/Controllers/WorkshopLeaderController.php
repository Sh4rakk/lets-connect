<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class WorkshopLeaderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');

        abort_unless($isAdmin || $user->isWorkshopLeader(), 403);

        $workshops = Workshop::query()
            ->when(! $isAdmin, fn ($query) => $query->where('leader_id', $user->id))
            ->with([
                'leader',
                'workshopMoments' => fn ($query) => $query->with(['moment', 'workshop'])->withCount(['bookings', 'feedback'])->orderBy('moment_id'),
            ])
            ->orderBy('name')
            ->get();

        return view('leader.dashboard', [
            'workshops' => $workshops,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function expected(WorkshopMoment $wsm): View
    {
        Gate::authorize('manage', $wsm);

        $wsm->load(['workshop', 'moment']);

        $students = $wsm->bookings()
            ->with('student:id,name,class')
            ->get()
            ->pluck('student')
            ->filter()
            ->sortBy(fn ($student) => mb_strtolower($student->name))
            ->values();

        return view('leader.expected', [
            'wsm' => $wsm,
            'rounds' => $wsm->workshop->workshopMoments()->with('moment')->orderBy('moment_id')->get(),
            'students' => $students,
        ]);
    }

    public function feedback(WorkshopMoment $wsm): View
    {
        Gate::authorize('manage', $wsm);

        $wsm->load(['workshop', 'moment']);

        $feedback = $wsm->feedback()->get();
        $answered = $feedback->whereNotNull('learned');

        return view('leader.feedback', [
            'wsm' => $wsm,
            'rounds' => $wsm->workshop->workshopMoments()->orderBy('moment_id')->get(),
            'total' => $feedback->count(),
            'answered' => $answered->count(),
            'average' => number_format((float) $feedback->avg('rating'), 1, ','),
            'ratings' => collect(range(5, 1))
                ->mapWithKeys(fn (int $rating) => [$rating => $feedback->where('rating', $rating)->count()])
                ->all(),
            'questions' => [
                'Was de workshop leerzaam?' => $this->countScale($answered, 'learned'),
                'Was de workshop leuk?' => $this->countScale($answered, 'fun'),
                'Zou je hem aanraden?' => [
                    'Ja' => $answered->where('recommend', true)->count(),
                    'Nee' => $answered->where('recommend', false)->count(),
                ],
            ],
            'comments' => [
                'Wat vond je goed?' => $feedback->pluck('good')->filter()->shuffle(),
                'Wat kan beter?' => $feedback->pluck('better')->filter()->shuffle(),
            ],
        ]);
    }

    private function countScale(Collection $answered, string $column): array
    {
        return collect(Feedback::SCALE)
            ->mapWithKeys(fn (string $label, string $value) => [$label => $answered->where($column, $value)->count()])
            ->all();
    }
}
