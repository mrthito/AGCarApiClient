<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ApplicationLicense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $status = Cache::remember('status', 60 * 60, function () {
            $status = \App\Models\Status::first();
            $url = Http::get('http://license.ucaryeman.com/index.php?page=verify&lisence=$status->token')->json();
            if ($url['status'] != 'error') {
                $status->status = true;
                $status->save();
            } else {
                $status->status = false;
                $status->save();
            }
            return $status;
        });

        if (!$status || !$status->status) {
            return response()->json(['status' => 'error', 'message' => 'Application is not licensed.'], 403);
        }
        return $next($request);
    }
}
