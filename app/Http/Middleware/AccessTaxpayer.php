<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Cycle;

class AccessTaxpayer
{
    // public function handle($request, Closure $next)
    public function handle($request, Closure $next)
    {
        $taxPayerId = $request->route('taxPayer');
        $cycleId = $request->route('cycle');

        if (isset($taxPayerId)) {

            $allowTaxpayer = Auth::user()->currentTeam
            ->whereHas('taxPayerIntegration', function ($query) use ($taxPayerId) {
                $query->where('taxpayer_id', $taxPayerId)
                ->whereIn('status', [1, 2]);
            })->count() > 0 ? true : false;

            if ($allowTaxpayer) {
                if ($cycleId > 0) {

                    $allowCycle = Cycle::where('id', $cycleId)->where('taxpayer_id', $taxPayerId)->count() > 0 ? true : false;
                    if ($allowCycle) {
                        return $next($request);
                    }
                }
            }
        }

        return redirect('404');
    }
}
