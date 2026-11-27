<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\WorkshopMoment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    private const COOKIE_MINUTES = 60 * 24 * 30;

    public function show(Request $request, string $token): View
    {
        $round = $this->round($token);

        if ($this->alreadyGiven($request, $round)) {
            return view('feedback.thanks', ['round' => $round]);
        }

        return view('feedback.rating', ['round' => $round, 'token' => $token]);
    }

    public function storeRating(Request $request, string $token): View|RedirectResponse
    {
        $round = $this->round($token);

        if ($this->alreadyGiven($request, $round)) {
            return redirect()->route('feedback.show', $token);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'good' => ['nullable', 'string', 'max:1000'],
            'better' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.*' => 'Kies een cijfer van 1 tot en met 5.',
            'good.*' => 'Gebruik maximaal 1000 tekens.',
            'better.*' => 'Gebruik maximaal 1000 tekens.',
        ]);

        $feedback = Feedback::create([
            ...$validated,
            'workshop_moment_id' => $round->id,
            'step_token' => Str::random(40),
        ]);

        Cookie::queue(self::cookieName($round), '1', self::COOKIE_MINUTES);

        return $this->questions($round, $token, $feedback->step_token);
    }

    public function storeQuestions(Request $request, string $token): View|Response
    {
        $round = $this->round($token);
        $stepToken = $request->input('step_token');

        $feedback = is_string($stepToken)
            ? Feedback::where('workshop_moment_id', $round->id)->where('step_token', $stepToken)->first()
            : null;

        if (! $feedback) {
            return view('feedback.thanks', ['round' => $round]);
        }

        $validator = Validator::make($request->all(), [
            'learned' => ['required', Rule::in(array_keys(Feedback::SCALE))],
            'fun' => ['required', Rule::in(array_keys(Feedback::SCALE))],
            'recommend' => ['required', Rule::in(['0', '1'])],
        ]);

        if ($validator->fails()) {
            return response($this->questions($round, $token, $stepToken, $request->only('learned', 'fun', 'recommend'), 'Beantwoord alle 3 de vragen.'), 422);
        }

        $feedback->update([
            ...$validator->validated(),
            'step_token' => null,
        ]);

        return view('feedback.thanks', ['round' => $round]);
    }

    private function questions(WorkshopMoment $round, string $token, string $stepToken, array $answers = [], ?string $error = null): View
    {
        return view('feedback.questions', [
            'round' => $round,
            'token' => $token,
            'stepToken' => $stepToken,
            'answers' => $answers,
            'error' => $error,
        ]);
    }

    private function round(string $token): WorkshopMoment
    {
        return WorkshopMoment::where('qr_token', $token)->with('workshop')->firstOrFail();
    }

    private function alreadyGiven(Request $request, WorkshopMoment $round): bool
    {
        return $request->cookie(self::cookieName($round)) === '1';
    }

    public static function cookieName(WorkshopMoment $round): string
    {
        return 'feedback_'.$round->id;
    }
}
