<?php

namespace App\Services;

use App\Contracts\ReminderInterface;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ReminderService implements ReminderInterface
{
    public function list(User $user, $limit = 10): array
    {
        $reminder = Reminder::where('user_id', $user->id)
            ->take($limit)
            ->orderBy('event_at', 'desc')
            ->get();

        return $reminder->toArray();
    }

    public function create($data): Reminder
    {
        $data['user_id'] = Auth()->user()->id;
        $reminder = Reminder::create($data);

        return $reminder;
    }

    public function view($id): Reminder
    {
        $reminder = Reminder::find($id);

        return $reminder;
    }

    public function edit($id, $data): Reminder
    {
        $reminder = Reminder::find($id)
            ->update($data);

        return $reminder;
    }

    public function delete($id): bool
    {
        try {
            Reminder::find($id)->delete();

            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}
