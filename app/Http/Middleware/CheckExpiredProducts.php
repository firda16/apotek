<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Events\ProductExpired;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckExpiredProducts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            $expiredProducts = PurchaseItem::whereDate('expiry_date', '<=', now())
                ->whereNull('expired_notified_at')
                ->get();

            foreach ($expiredProducts as $expired) {
                event(new ProductExpired($expired));
                $expired->update(['expired_notified_at' => now()]);
            }
        }
        return $next($request);
    }
}
