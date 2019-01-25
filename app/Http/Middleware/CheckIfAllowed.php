<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Suport\Facades\Auth;

class CheckIfAllowed
{
  public function handle($request, Closure $next)
  {
    //user -> user_team :: role
    //team -> taxpayer access allowed or not.

    if(Auth::user()->role === 2)
    {
      return $next($request);
    } else {
      return reponse()->json(['error' => 'Unauthorized'], 403);
    }
  }
}
