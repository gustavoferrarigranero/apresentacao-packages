<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;

final class StoreFeedbackController extends Controller
{
    public function __invoke(StoreFeedbackRequest $request): JsonResponse
    {
        $feedback = Feedback::create($request->validated());

        return response()->json([
            'id' => $feedback->id,
            'message' => config('feedback-widget.success_message'),
        ], 201);
    }
}
