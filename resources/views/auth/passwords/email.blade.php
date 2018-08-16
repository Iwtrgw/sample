@extends('layouts.default')
@section('title','重置密码')

@section('content')
<div class="col-md-offset-2 col-md-8">
    <div class="panel panel-default">
        <div class="panel-heading">重置密码</div>
        <div class="panel-body">
            @if(session('status'))
                <div class="alert alert-success">
                   {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="form-horizontal">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('email') ? 'has-error' :'' }}">
                    <label for="email" class="col-md-4 control-label">邮箱地址：</label>

                    <div class="col-md-6">
                        <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}">

                        @if($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button class="btn btn-primary" type="submit">
                            发送密码重置邮件
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop