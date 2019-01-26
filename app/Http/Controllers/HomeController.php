<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TaxpayerIntegration;
use Auth;

class HomeController extends Controller
{
  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct()
  {
    $this->middleware('auth');

    // $this->middleware('subscribed');

    // $this->middleware('verified');
  }

  /**
  * Show the application dashboard.
  *
  * @return Response
  */
  public function show()
  {
    return view('home');
  }
}
