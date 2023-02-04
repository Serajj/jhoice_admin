 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Jhoice</title>
	<meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{asset('vendor/fontawesome-free/css/all.min.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/styles.min.css')}}">
    {{--<link rel="stylesheet" href="{{asset('css/'.setting("theme_color","primary").'.min.css')}}">--}}
	
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-201315059-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-201315059-1');
</script>

</head>

	<style>
    .btn-primary {
        color: #fff;
        background-color: #08143A !important;
        border-color: #08143A !important;
        box-shadow: none;
    }
    .btn-outline-primary{
        /* color: #fff; */
        color: #08143A !important;
        border-color: #08143A !important;
    }
    .btn-outline-primary:hover{
        /* color: #fff; */
        color: #fff !important;
        background-color: #08143A !important;
        border-color: #08143A !important;
    }
    .btn-link{
      color : #08143A !important;
    }
    .btn-link:hover {
        color: #0056b3 !important;
        text-decoration: none;
    }

		
</style>
	
<body style="background-color: black;">

    <div class="position-absolute d-none d-lg-block" style="left:0; bottom:0;">
       <!--  <img style="height: 120vh; width: auto;" src="{{asset('images/jhoice_welcome_blob_2.png')}}" alt=""> -->
    </div>

    <div class="position-relative" style="height: 100vh; width: 100vw; z-index: 2; overflow-y: scroll;">

        <div class="container my-12" >
            <div class="row">

                <div class="col-md-9 mx-auto">
                    <div class="card shadow rounded" style="background-color: black;">
                        <div class="card-header">
						</div>
						<div class="col-md-9">
							<nav class="navbar navbar-expand-lg" >
  
    <ul class="navbar-nav mr-auto">
    <li class="nav-item active">
    <a class="nav-link" href="https://jhoice.com">
          <img src="https://jhoice.com/storage/app/public/460/Customer.png" alt="Jhoice" width="25%;" height="25%;">
      </a>
      </li>
      <li class="nav-item">
      <a href="{{route('default.terms')}}" class="nav-link" style="font-size:20px;">Terms & Conditions</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('default.privacy')}}" style="font-size:20px;">Privacy Policy</a>
      </li>
      <!-- Default disabled -->
{{--<div class="custom-control custom-switch">
  <input type="checkbox" class="custom-control-input" id="customSwitch2" disabled>
  <label class="custom-control-label" for="customSwitch2"></label>
</div>--}}
    </ul>
</nav>
                        </div>
						<div class="card-body">
                        
                  
					
              
							<div id="email-message" class="text-white"></div>
							<div id="sms-message" class="text-white"></div>
              
                        @if (Session::has('success'))
                        <div class="alert alert-success">
                        <ul>
                          <li>{{ Session::get('success') }}</li>
                        </ul>
                        </div>
                  @endif
                <br>  <br>
							
						
							
							<div class="row">
							<div class="col-8">
							
								
								<br>
								<br>
								
								
							<h1 class="h1 mb-0 text-muted">Welcome to Jhoice</h1>
    <br>
                        	
                        
							
                            <p class="text-muted" style="font-size:20px;">
                                we provide a platform where both local customers and service <br>providers can communicate with each other for daily needs.
                            </p><br>
						
                                @auth
                                @else
                                <form class="form-inline"  method="POST" action="{{ route('default.installapp')}}">
                                  @csrf
                                  <div class="col-sm-10 mb-2">
                                  <input checked="checked" name="email_phone" type="radio" value="Email"><span class="text-white"> Email</span>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                
                                <input name="email_phone" type="radio" value="Phone" class="text-white"><span class="text-white">Phone</span>
                                </div>
                                    <br><br>
                                      <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}" 
                                      placeholder="Please enter email">
                                    @error('email')
                                      <span class="invalid-feedback d-block" role="alert">
                                      <strong>{{ $message }}</strong>
                                      </span>
                                      @enderror
                                  
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" 
                                      placeholder="Phone" style="display:none">
                                    @error('phone')
                                      <span class="invalid-feedback d-block" role="alert">
                                      <strong>{{ $message }}</strong>
                                      </span>
                                      @enderror&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
								 <div class="dropdown" >
								  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" name="jhoice_app_link">Send Link
								  <span class="caret"></span></button>
								  <ul class="dropdown-menu">
									<li id="service_link"><a href="#" name="service_link">Customer App</a></li>
									<li id="customer_link"><a href="#" name="customer_link">Provider App</a></li>
								  </ul>
								</div>
										
                                {{--<button type="submit" class="btn btn-primary" >Send Link</button>--}}
                                </div>
                                </form>
                                @endauth
								
                            <div class="col-4">
							<img src="{{asset('images\Screenshot_20221018-142557.jpg')}}" alt="Jhoice" hieght="50%" width="80%">
						</div>
        					</div>
							
							</div>
                            <h5></h5>
                            <h5></h5>
                            <h5></h5>

                            <br><br><br>
        <div class="card-deck">
        <div class="card" style="background-color:gray;height:120px;width:30%;">
    
    <div class="card-body">
      <h5 class="text-white">280+</h5>
      
      <p class="card-text"><small class="text-white">registerd with us</small></p>
    </div>
  </div>
  <div class="card" style="background-color:gray;height:120px;width:30%;">
  
    <div class="card-body">
      <h5 class="text-white">5000+</h5>
      <p class="card-text"><small class="text-white">Order till now</small></p>
    </div>
  </div>
  
  <div class="card" style="background-color:gray;height:120px;width:30%;">
    
    <div class="card-body">
      <h5 class="text-white">10000+</h5>
      
      <p class="card-text"><small class="text-white">downloads</small></p>
    </div>
  </div>
  <div class="card" style="background-color:gray;height:120px;width:30%;">
    
    <div class="card-body">
      <h5 class="text-white">4.5</h5>
      
      <p class="card-text"><small class="text-white">Rating</small></p>
    </div>
  </div>
						</div>
  @if(count($faqs) > 0)
                            <hr>

                            <h4 class="h4 mb-4">FAQs</h4>


                            <div class="accordion" id="faqSection">

                              @foreach($faqs as $faq)
                                <div class="card">
                                  <div class="card-header" id="heading-{{$faq->id}}">
                                    <h2 class="mb-0">
                                      <button class="btn d-flex font-bold text-left justify-content-between align-items-center w-100" type="button" data-toggle="collapse" data-target="#collapse-{{$faq->id}}" aria-expanded="true" aria-controls="collapse-{{$faq->id}}">
                                        <div >
                                          {!! $faq->question !!}
                                        </div>

                                        <div class="ml-5">
                                          <svg version="1.1" style="height: auto; width: 1rem; opacity: 0.6;" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 122.88 66.91" xml:space="preserve"><g><path d="M11.68,1.95C8.95-0.7,4.6-0.64,1.95,2.08c-2.65,2.72-2.59,7.08,0.13,9.73l54.79,53.13l4.8-4.93l-4.8,4.95 c2.74,2.65,7.1,2.58,9.75-0.15c0.08-0.08,0.15-0.16,0.22-0.24l53.95-52.76c2.73-2.65,2.79-7.01,0.14-9.73 c-2.65-2.72-7.01-2.79-9.73-0.13L61.65,50.41L11.68,1.95L11.68,1.95z"/></g></svg>
                                        </div>
                                      </button>
                                    </h2>
                                  </div>

                                  <div id="collapse-{{$faq->id}}" class="collapse" aria-labelledby="heading-{{$faq->id}}" data-parent="#faqSection">
                                    <div class="card-body ">
                                      <p class="text-muted font-weight-light">
                                        {!! $faq->answer !!}
                                      </p>
                                    </div>
                                  </div>
                                </div>
                              @endforeach
                            </div>

                          @endif
</div>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
  $(document).ready(function(){
    $("input[name$='email_phone']").click(function() {
      if($(this).val() == "Phone"){
      $('#phone').show();
      $('#email').hide();
      }
      else{
        $('#phone').hide();
      $('#email').show();
      }
    });
	$('#service_link').click(function(e){
    e.preventDefault();
		if($("input[name$='email_phone']").val()=='Email'){
			var service_link = $('#service_link').text()
			
        $.ajax({
            type: "POST",
            url: '{{ url("/installapp") }}',
            data:{ email : $('#email').val(),
              phone : $('#phone').val(),
			 service_link: service_link,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
		
            success: function(response) {
                if(response.status == 200){
					
					//if($("input[name$='email_phone']").val()=='Email'){
                   // console.log(response);
                   	 $('#email-message').html('<span>'+response.msg+'</span>');
					//}
					//else{
					//	 $('#sms-message').html('<span>'+response.msg+'</span>');
					//}
                }
            }
          });
		}
		else{
		var service_link = $('#service_link').text()
		$.ajax({
            type: "POST",
            url: '{{ url("/installapp") }}',
            data:{ email: $('#email').val(),
              phone : $('#phone').val(),
			  service_link: service_link,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
		
            success: function(response) {
                if(response.status == 200){
					
					//if($("input[name$='email_phone']").val()=='Email'){
                   // console.log(response);
                  // 	 $('#email-message').html('<span>'+response.msg+'</span>');
					//}
					//else{
						 $('#sms-message').html('<span>'+response.msg+'</span>');
					//}
                }
            }
          });
		}
		
        });
		
    $('#customer_link').click(function(e){
    e.preventDefault();
		if($("input[name$='email_phone']").val()=='Email'){
			var service_link = $('#customer_link').text()
			
        $.ajax({
            type: "POST",
            url: '{{ url("/providerapp") }}',
            data:{ email : $('#email').val(),
              phone : $('#phone').val(),
			 service_link: service_link,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
		
            success: function(response) {
                if(response.status == 200){
					
					//if($("input[name$='email_phone']").val()=='Email'){
                   // console.log(response);
                   	 $('#email-message').html('<span>'+response.msg+'</span>');
					//}
					//else{
					//	 $('#sms-message').html('<span>'+response.msg+'</span>');
					//}
                }
            }
          });
		}
		else{
		var service_link = $('#service_link').text()
		$.ajax({
            type: "POST",
            url: '{{ url("/providerapp") }}',
            data:{ email: $('#email').val(),
              phone : $('#phone').val(),
			  service_link: service_link,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
		
            success: function(response) {
                if(response.status == 200){
					
					//if($("input[name$='email_phone']").val()=='Email'){
                   // console.log(response);
                  // 	 $('#email-message').html('<span>'+response.msg+'</span>');
					//}
					//else{
						 $('#sms-message').html('<span>'+response.msg+'</span>');
					//}
                }
            }
          });
		}
		
        });
	  
	  
  });

</script>
<script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>

<script src="{{asset('vendor/bootstrap-v4-rtl/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>

<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
<script src="{{asset('js/scripts.min.js')}}"></script>
</body>
</html>
