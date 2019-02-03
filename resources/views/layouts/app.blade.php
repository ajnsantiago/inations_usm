<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- CSRF Token -->
   <meta name="csrf-token" content="{{ csrf_token() }}">

   <title>{{ config('app.name', 'iNations') }}</title>

   <!-- Styles -->
   <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
   <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
         <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
               <span class="sr-only">Toggle Navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/home') }}">
               {{ config('app.name', 'Laravel') }}
            </a>
         </div>

         <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
               &nbsp;
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
               <!-- Authentication Links -->
               @guest
                  <li><a href="{{ route('login') }}">Login</a></li>
                  <li><a href="{{ route('register') }}">Register</a></li>
               @else
                  <li class="dropdown">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                     </a>

                     <ul class="dropdown-menu">
                        <!-- home -->
                        <li>
                           <a href="{{ route('home') }}">
                              Home
                           </a>
                        </li>

                        <!-- inationsusers -->
                        <li>
                           <a href="{{ route('inationuser.list') }}">
                              @if (Auth::user()->isadmin == 1)
                                 iNations users management
                              @else
                                 iNations users
                              @endif
                           </a>
                        </li>

                        <!-- groups -->
                        <li>
                           <a href="{{ route('ination.listgroup') }}">
                              @if (Auth::user()->isadmin == 1)
                                 Groups management
                              @else
                                 Groups
                              @endif
                           </a>
                        </li>

                        <li>
                           <hr>
                        </li>

                        <li>
                           <a href="{{ route('logout') }}"
                              onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();">
                              Logout
                           </a>

                           <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                              {{ csrf_field() }}
                           </form>
                        </li>
                     </ul>
                  </li>
               @endguest
            </ul>
         </div>
      </div>
   </nav>

</div>

<div class="container">
   @yield('content')
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

@yield('post-script')

</body>
</html>
