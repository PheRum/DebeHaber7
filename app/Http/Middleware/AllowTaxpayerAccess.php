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

    if (isset($taxPayer))
    {
      $allow = $user->currentTeam->whereHas('taxPayerIntegration', function ($query) use ($taxPayer) {
        $query->where('taxpayer_id', $taxPayer->id)
        ->whereIn('staus', [1,2]);
      })->any();

      if ($allow)
      {
        return $next($request);
      }
    }

    return reponse()->json(['error' => 'Resource not Found'], 404);
  }
}
