<!doctype html>
<html>
  <head>
    @include('includes.head')
  </head>
  <body>
    @include('includes.header')
    
    <div class="container">
	    <div class="row">
		    <div class="col-sm-12">
				@yield('content')
		    </div>
	    </div>
    </div>
    
    @include('includes.footer')
    @include('includes.foot')
  </body>
</html>