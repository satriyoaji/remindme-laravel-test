<?php

namespace App\Contracts;

use App\Models\Reminder;
use App\Models\User;

interface ReminderInterface
{
    public function list(User $user, int $limit = 10): array;

    public function create(array $data): Reminder;

    public function view(int $id): Reminder;

    public function edit(int $id, array $data): Reminder;

    public function delete(int $id): bool;
}
