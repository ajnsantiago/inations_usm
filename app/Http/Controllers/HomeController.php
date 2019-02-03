<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inationsuser;
use App\Group;

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
   }

   /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $data['usercount'] = Inationsuser::count();
      $data['groupcount'] = Group::count();

      return view('home', $data);
   }
}
