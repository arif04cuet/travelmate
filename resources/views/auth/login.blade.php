@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <div class="btn-group">
                        
                        <a class='btn btn-primary' href="{{ route('social.login',['social'=>'facebook'])}}" style="width:13em"> <i class="fa fa-facebook" style="width:16px; height:20px"></i> Sign in with Facebook</a>
                    </div>	
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
