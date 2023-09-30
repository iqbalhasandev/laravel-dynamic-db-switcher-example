<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DatabaseSwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) : Response
    {
        // auth check
        if (auth()->check()) {
            // get user
            $user = auth()->user();
            // check user db_name
            if ($user->db_name) {
                // switch database
                $useDbQuery = "USE " . $user->db_name;
                DB::statement($useDbQuery);
            }
        }
        return $next($request);
    }
}