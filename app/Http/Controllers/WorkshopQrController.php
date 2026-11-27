<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopMoment;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class WorkshopQrController extends Controller
{
    public function show(Workshop $workshop)
    {
        $workshop->ensureQrToken();

        return $this->qrPage($workshop, null);
    }

    public function showRound(WorkshopMoment $wsm)
    {
        Gate::authorize('manage', $wsm);

        $wsm->ensureQrToken();

        return $this->qrPage($wsm->workshop, $wsm);
    }

    public function regenerate(Workshop $workshop)
    {
        $workshop->regenerateQrToken();

        return redirect()
            ->route('workshop.qr', $workshop)
            ->with('status', 'Nieuwe QR-code gemaakt. De oude QR-code werkt niet meer.');
    }

    public function regenerateRound(WorkshopMoment $wsm)
    {
        Gate::authorize('manage', $wsm);

        $wsm->regenerateQrToken();

        return redirect()
            ->route('workshop-moment.qr', $wsm)
            ->with('status', 'Nieuwe QR-code gemaakt. De oude QR-code werkt niet meer.');
    }

    public function scan(string $token)
    {
        if (WorkshopMoment::where('qr_token', $token)->exists()) {
            return redirect()->route('feedback.show', $token);
        }

        $workshop = Workshop::where('qr_token', $token)->firstOrFail();
        $workshop->load(['workshopMoments' => fn ($query) => $query->with('moment')->orderBy('moment_id')]);
        $workshop->workshopMoments->each->ensureQrToken();

        $myRounds = collect();
        if (Auth::check()) {
            $myRounds = Auth::user()->bookings()
                ->with('workshopMoments.moment')
                ->get()
                ->pluck('workshopMoments')
                ->filter(fn ($wm) => $wm && $wm->workshop_id === $workshop->id)
                ->sortBy('moment_id')
                ->values();
        } else {
            session()->put('url.intended', url()->current());
        }

        return view('workshops.scan', [
            'workshop' => $workshop,
            'myMoments' => $myRounds->map(fn ($wm) => $wm->moment?->time)->filter()->values(),
        ]);
    }

    private function qrPage(Workshop $workshop, ?WorkshopMoment $round)
    {
        $scanUrl = ($round ?? $workshop)->qrUrl();

        return view('workshops.qr', [
            'workshop' => $workshop,
            'round' => $round,
            'rounds' => $workshop->workshopMoments()->with('moment')->orderBy('moment_id')->get(),
            'qrSvg' => $this->svg($scanUrl),
            'scanUrl' => $scanUrl,
        ]);
    }

    private function svg(string $data): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::MARKUP_SVG,
            'outputBase64' => false,
            'eccLevel' => QRCode::ECC_M,
            'addQuietzone' => true,
            'svgAddXmlHeader' => false,
        ]);

        return (new QRCode($options))->render($data);
    }
}
