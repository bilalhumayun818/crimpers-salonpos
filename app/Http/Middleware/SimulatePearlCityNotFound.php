<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SimulatePearlCityNotFound
{
    public function handle(Request $request, Closure $next): Response
    {
        // Always allow branch changes through, so switching away from Pearl City cannot be blocked.
        $isBranchSwitchRequest = $request->routeIs('branch.switch', 'admin.branch.switch_from_select')
            || ($request->isMethod('POST') && $request->is('branch/switch', 'admin/branch-switch-from-select'));

        if ($isBranchSwitchRequest) {
            return $next($request);
        }

        // Let the redirect immediately after a branch change load without a simulated error.
        if (session()->pull('skip_pearl_city_404_once', false)) {
            return $next($request);
        }

        if (app()->environment('local')) {
            $branchId = session('current_branch_id', auth()->user()?->branch_id ?? 1);
            $branch = Branch::find($branchId);
            $isPearlCity = $branch && str_starts_with(strtolower(trim((string) $branch->name)), 'pearl city');

            if ($isPearlCity && random_int(1, 100) < 15) {
                abort(404);
            }
        }

        return $next($request);
    }
}
