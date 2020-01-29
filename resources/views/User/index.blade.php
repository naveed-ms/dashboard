@extends('layouts.app')
@section('content')
<script>
app.controller('Ctrl',function($scope){

});
</script>
<div class="container" ng-controller="Ctrl">
    <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">List </div>
          <div class="panel-body">
            <table role="grid" class="table table-responsive">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
                <tr>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td>{{ $user->role_name }}</td>
                  <td><a class="btn btn-info" href="{{ url('/users') }}/{{ $user->id }}">Edit</a> | <a class="btn btn-danger" href="{{ url('/users/remove') }}/{{ $user->id }}">Remove</a></td>
                </tr>
              @endforeach
              </tbody>
              <tfoot>
                <tr><td colspan="5">{!! $users->render() !!}</td></tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      </div>
    </div>
@endsection
