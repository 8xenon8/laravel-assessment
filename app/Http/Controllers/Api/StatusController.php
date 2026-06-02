<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatusService;

class StatusController extends Controller
{
    public function __construct(private StatusService $statusService) {}

    public function index()
    {
        return $this->statusService->getStatuses();
    }
}
