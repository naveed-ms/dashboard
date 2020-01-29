@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">User Editor </div>
          <div class="panel-body">
            <form method="post" action="{{ url('/users/edit') }}/{{ $user->id }}">
              {!! csrf_field() !!}
                <div class="row">
                  <div class="col-md-12 form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="txt_name" disabled="disabled" placeholder="Name" value="{{ $user->name }}"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <input type="email" class="form-control" name="txt_email" disabled="disabled" placeholder="Email Address" value="{{ $user->email }}"/>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <input type="password" class="form-control" name="txt_password" placeholder="Password" />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <select name="txt_role" class="form-control">
                      @foreach($roles as $role)
                      <option value="{{ $role->id }}" {{{ ($user->role == $role->id) ? "selected" : "" }}}>{{ $role->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 form-group">
                    <input type="submit" class="btn btn-success" value="Save" />
                  </div>
                </div>
            </form>
          </div>
        </div>
      </div>
      </div>
    </div>
@endsection
