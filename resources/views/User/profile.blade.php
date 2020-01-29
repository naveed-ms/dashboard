@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">User Editor </div>
          <div class="panel-body">
            <form method="post" action="{{ url('/profile') }}">
              {!! csrf_field() !!}
                <div class="row">
                  <div class="col-md-12 form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="txt_name" placeholder="Name" value="{{ $user->name }}"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <input type="email" class="form-control" disabled="disabled" name="txt_email" placeholder="Email Address" value="{{ $user->email }}"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <input type="password" class="form-control" name="txt_password" placeholder="Password" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <button type="submit" class="btn btn-success">Save</button>
                  </div>
                </div>
            </form>
          </div>
        </div>
      </div>
      </div>
    </div>
@endsection
