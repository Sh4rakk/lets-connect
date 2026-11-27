<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopMoment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class FeedbackTestController extends Controller
{
    public function index(): View
    {
        $this->onlyLocal();

        return view('feedback.test', [
            'workshops' => Workshop::query()
                ->with(['workshopMoments' => fn ($query) => $query->with('moment')->withCount('feedback')->orderBy('moment_id')])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function start(WorkshopMoment $wsm): RedirectResponse
    {
        $this->onlyLocal();

        $wsm->ensureQrToken();

        Cookie::queue(Cookie::forget(FeedbackController::cookieName($wsm)));

        return redirect()->route('feedback.show', $wsm->qr_token);
    }

    private function onlyLocal(): void
    {
        abort_unless(app()->environment('local', 'testing'), 404);
    }
}
