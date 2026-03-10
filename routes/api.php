<?php

use App\Http\Controllers\Api\V1\HealthCheckController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health-check', HealthCheckController::class)
        ->middleware(['require.owner.header', 'throttle:health-check']);
});