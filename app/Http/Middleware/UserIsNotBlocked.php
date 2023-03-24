<?php

namespace App\Http\Middleware;

use App\Models\BlockedUser;
use Closure;
use Illuminate\Http\Request;

class UserIsNotBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $blockedUser = BlockedUser::query()->where('user_id', auth()->id())->first();
        if ($blockedUser) {
            return response()->json([
                'status' => 'blocked',
                'message' => 'User blocked',
                'reason' => $blockedUser->reason
            ]);
        }
        return $next($request);
    }
}
