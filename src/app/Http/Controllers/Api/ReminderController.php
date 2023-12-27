<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReminderRequest;
use App\Models\Reminder;
use App\Services\ReminderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReminderController extends Controller
{
    /*
     * ReminderService
     */
    private $reminderService;

    /*
     * @var ReminderService $reminderService
     */
    public function __construct(
        ReminderService $reminderService
    ) {
        $this->reminderService = $reminderService;
    }

    /**
     * Display a listing of the reminder.
     *
     * @var Request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth()->user();
        $limit = $request->limit ?? 10;
        $reminders = $this->reminderService->list($user, $limit);

        return response()->json([
            'ok' => true,
            'data' => [
                'reminders' => $reminders,
            ],
        ]);
    }

    /**
     * Store a newly created reminder in database.
     *
     * @var ReminderRequest
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ReminderRequest $request)
    {
        $data = $request->validated();
        try {
            $reminder = $this->reminderService->create($data);

            return response()->json([
                'ok' => true,
                'data' => $reminder,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort($e->getCode());
        }
    }

    /**
     * Display the specified reminder.
     *
     * @return \Illuminate\Http\JsonResponse
     * @var Reminder
     *
     */
    public function show(Reminder $reminder)
    {
        return response()->json([
            'ok' => true,
            'data' => $reminder,
        ]);
    }

    /**
     * Update the specified reminder in database.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @var Reminder
     * @var ReminderRequest
     */
    public function update(ReminderRequest $request, Reminder $reminder)
    {
        $data = $request->validated();
        try {
            $reminder->update($data);

            return response()->json([
                'ok' => true,
                'data' => $reminder,
            ]);
        } catch (\Exception $e) {
            abort($e->getCode());
        }
    }

    /**
     * Remove the specified reminder from database.
     *
     * @var Reminder
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Reminder $reminder)
    {
        try {
            $reminder->delete();

            return response()->json([
                'ok' => true,
            ]);
        } catch (\Exception $e) {
            abort($e->getCode());
        }
    }
}
