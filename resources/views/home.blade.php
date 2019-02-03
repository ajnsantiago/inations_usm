@extends('layouts.app')

@section('content')
   <div class="container">
      <div class="row">
         <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
               <div class="panel-heading">Dashboard | Welcome {{ Auth::user()->name }}</div>
               <div class="panel-body">
                  @if (session('status'))
                     <div class="alert alert-success">
                        {{ session('status') }}
                     </div>
                  @endif

                  <!-- count of registered users -->
                  <li>
                     There are {{ $usercount }} registered user(s) -
                     <a href="{{ route('inationuser.list') }}">
                        @if (Auth::user()->isadmin == 1)
                           Manage
                        @else
                           View
                        @endif
                     </a>
                  </li>

                  <!-- count of registered groups -->
                  <li>
                     There are {{ $groupcount }} registered group(s) -
                     <a href="{{ route('ination.listgroup') }}">
                        @if (Auth::user()->isadmin == 1)
                           Manage
                        @else
                           View
                        @endif
                     </a>
                  </li>
               </div>
            </div>
         </div>
      </div>
   </div>
@endsection
