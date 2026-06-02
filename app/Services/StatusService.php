<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TaskStatus;
use Illuminate\Support\Collection;

class StatusService
{
    public function getStatuses(): Collection
    {
        return collect(TaskStatus::cases())->map(fn(TaskStatus $s) => $s->value);
    }
}
