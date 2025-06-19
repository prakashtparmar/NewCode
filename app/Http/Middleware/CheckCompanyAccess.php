<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckCompanyAccess
{
    /**
     * Handle an incoming request.
     *
     * This middleware ensures:
     * - Master admin can access all data.
     * - Regular users can only access resources linked to their own company.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is master admin, allow access to all
        if ($user && $user->user_level === 'master_admin') {
            return $next($request);
        }

        // Detect company ID from route (e.g., /companies/{company}/users)
        $companyFromRoute = $request->route('company');

        // If it's a model (route model binding), get the ID
        if (is_object($companyFromRoute)) {
            $companyId = $companyFromRoute->id;
        } else {
            $companyId = $companyFromRoute; // assume it's an ID
        }

        // If user's company doesn't match the route's company, deny access
        if ($user && $companyId && $user->company_id !== (int) $companyId) {
            abort(403, 'Unauthorized: You do not have access to this company\'s data.');
        }

        return $next($request);
    }
}
