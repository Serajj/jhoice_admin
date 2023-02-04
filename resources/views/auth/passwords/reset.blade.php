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
</style>
    <div class="card-body login-card-body">
        <p class="login-box-msg">{{__('auth.reset_password_title')}}</p>

        <form method="POST" action="{{ route('password.request') }}">
            {!! csrf_field() !!}

            <input type="hidden" name="token" value="{{ $token }}">

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
                <div class="col-4 ml-auto">
                    <button type="submit" class="btn btn-primary btn-block">{{__('auth.reset_password')}}</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        <p class="mb-1 text-center">
            <a href="{{ url('/login') }}" class="text-center">{{__('auth.remember_password')}}</a>
        </p>
    </div>
    <!-- /.login-card-body -->
@endsection
