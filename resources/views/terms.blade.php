<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{asset('vendor/fontawesome-free/css/all.min.css')}}">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/styles.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/'.setting("theme_color","primary").'.min.css')}}">
	<link rel="icon" type="image/png" href="{{$app_logo ?? ''}}"/>
</head>
<body class="position-relative">

    <div class="position-absolute" style="left:0; bottom:0rem;">
       <!--  <img style="height: 120vh; width: auto;" src="{{asset('images/jhoice_welcome_blob_2.png')}}" alt=""> -->
    </div>

    <div class="position-relative" style="height: 100vh; width: 100vw; z-index: 2; overflow-y: scroll;">
        <div class="container my-5">
            <div class="card rounded shadow">
                <div class="card-body">
                    {!!$data->content!!}
                </div>
            </div>
        </div>
    </div>

<!-- jQuery -->
<script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>

<script src="{{asset('vendor/bootstrap-v4-rtl/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>

<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
<script src="{{asset('js/scripts.min.js')}}"></script>
</body>
</html>
