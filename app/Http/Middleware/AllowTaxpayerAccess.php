<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Suport\Facades\Auth;

class AllowTaxpayerAccess
{
    // public function handle($request, Closure $next)
    public function handle($request, Closure $next)
    {
        $taxPayer = $request->route('taxPayer');
        $cycle = $request->route('cycle');

        if (isset($taxPayer))
        {
            $allowTaxpayer = $user->currentTeam->whereHas('taxPayerIntegration', function ($query) use ($taxPayer) {
                $query->where('taxpayer_id', $taxPayer->id)
                ->whereIn('staus', [1, 2]);
            })->any();

            if (isset($cycle))
            {
                $allowCycle = $taxPayer->id == $cycle->taxpayer_id ? true : false;
            }

            if ($allowTaxpayer && $allowCycle)
            {
                return $next($request);
            }
        }

        return reponse()->json(['error' => 'Resource not Found'], 404);
    }
}
