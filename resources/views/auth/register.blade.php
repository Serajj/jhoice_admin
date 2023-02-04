@extends('layouts.auth.default')
@section('content')

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

    .icheck-primary>input:first-child:checked+label::before {
        background-color: #08143A !important;
        border-color: #08143A !important;
    }

    [class*=icheck-]>input:first-child+label::before {
       
        border: 1px solid #08143A !important;
    }
</style>
    <div class="card-body login-card-body">
        <p class="login-box-msg">{{__('auth.register_new_member')}}</p>

        <form action="{{ url('/register') }}" method="post">
            {!! csrf_field() !!}

            <div class="input-group mb-3">
                <input value="{{ old('name') }}" type="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" placeholder="{{__('auth.name')}}" aria-label="{{__('auth.name')}}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                @if ($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
            </div>

            <div class="input-group mb-3">
                <input value="{{ old('email') }}" type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" placeholder="{{__('auth.email')}}" aria-label="{{__('auth.email')}}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                </div>
                @if ($errors->has('email'))
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                @endif
            </div>

            <div class="input-group mb-3">
                <input value="{{ old('password') }}" type="password" class="form-control  {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{__('auth.password')}}" aria-label="{{__('auth.password')}}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                @if ($errors->has('password'))
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                @endif
            </div>

            <div class="input-group mb-3">
                <input value="{{ old('password_confirmation') }}" type="password" class="form-control  {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" placeholder="{{__('auth.password_confirmation')}}" aria-label="{{__('auth.password_confirmation')}}">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                @if ($errors->has('password_confirmation'))
                    <div class="invalid-feedback">
                        {{ $errors->first('password_confirmation') }}
                    </div>
                @endif
            </div>

            <div class="row mb-2">
                <div class="col-8">
                    <div class="icheck-primary">
                        <input type="checkbox" id="remember" name="remember"> <label for="remember">
                            {{__('auth.agree')}}
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">{{__('auth.register')}}</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        @if(setting('enable_facebook',false) || setting('enable_google',false) || setting('enable_twitter',false))
            <div class="social-auth-links text-center mb-3">
                <p style="text-transform: uppercase">- {{__('lang.or')}} -</p>
                @if(setting('enable_facebook',false))
                    <a href="{{url('login/facebook')}}" class="btn btn-block btn-facebook"> <i class="fab fa-facebook mr-2"></i> {{__('auth.login_facebook')}}
                    </a>
                @endif
                @if(setting('enable_google',false))
                    <a href="{{url('login/google')}}" class="btn btn-block btn-google btn-primary"> <i class="fab fa-google mr-2"></i> {{__('auth.login_google')}}
                    </a>
                @endif
                @if(setting('enable_twitter',false))
                    <a href="{{url('login/twitter')}}" class="btn btn-block btn-twitter"> <i class="fab fa-twitter mr-2"></i> {{__('auth.login_twitter')}}
                    </a>
                @endif
            </div>
            <!-- /.social-auth-links -->
        @endif

        <p class="mb-1 text-center">
            <a href="{{ url('/login') }}" class="btn-link">{{__('auth.already_member')}}</a>
        </p>
    </div>
    <!-- /.login-card-body -->
@endsection
