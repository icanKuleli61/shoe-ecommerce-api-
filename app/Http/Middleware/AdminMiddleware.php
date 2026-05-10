<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->role !== 'admin') {
            throw new BaseException(ErrorCode::FORBIDDEN);
        }
        return $next($request);
    }
}
