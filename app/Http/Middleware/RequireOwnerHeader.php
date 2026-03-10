<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequireOwnerHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $owner = $request->header('X-Owner');

        if (! is_string($owner) || ! Str::isUuid($owner)) {
            return new JsonResponse([
                'message' => 'The X-Owner header must contain a valid UUID.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $next($request);
    }
}