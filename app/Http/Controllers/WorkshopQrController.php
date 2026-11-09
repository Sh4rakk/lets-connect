<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Auth;

class WorkshopQrController extends Controller
{
    public function show(Workshop $workshop)
    {
        if (! $workshop->qr_token) {
            $workshop->regenerateQrToken();
        }

        return view('workshops.qr', [
            'workshop' => $workshop,
            'qrSvg' => $this->svg($workshop->qrUrl()),
            'scanUrl' => $workshop->qrUrl(),
        ]);
    }

    public function regenerate(Workshop $workshop)
    {
        $workshop->regenerateQrToken();

        return redirect()
            ->route('workshop.qr', $workshop)
            ->with('status', 'Nieuwe QR-code gemaakt. De oude QR-code werkt niet meer.');
    }

    public function scan(string $token)
    {
        $workshop = Workshop::where('qr_token', $token)
            ->with(['workshopMoments.moment'])
            ->firstOrFail();

        $myMoments = collect();
        if (Auth::check()) {
            $myMoments = Auth::user()->bookings()
                ->with('workshopMoments.moment')
                ->get()
                ->pluck('workshopMoments')
                ->filter(fn ($wm) => $wm && $wm->workshop_id === $workshop->id)
                ->map(fn ($wm) => $wm->moment?->time)
                ->filter()
                ->values();
        } else {
            session()->put('url.intended', url()->current());
        }

        return view('workshops.scan', [
            'workshop' => $workshop,
            'myMoments' => $myMoments,
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
