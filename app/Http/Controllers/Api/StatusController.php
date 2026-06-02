<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;

class StatusController extends Controller
{
    // @TODO: Refactor not to use tasks for status list receiving
    public function index()
    {
        return Task::select('status')
            ->distinct()
            ->pluck('status');
    }
}
