@extends('layouts.app')
@section('content')
<script src="{{ url('public/js/controller/singerController.js') }}"></script>
<div class="container" ng-controller="Ctrl">
    <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">List </div>
          <div class="panel-body">
            <table role="grid" datatable="ng" class="table table-responsive">
              <thead>
                <tr>
                  <th>Singer</th>
                  <th>Action 123</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="row in data">
                  <td><% row.name %></td>
                  <td>
                    <a class="btn btn-link" ng-click="editItem(row.id,'{{ url('/singer/save') }}')">View</a>
                  <!-- <a class="btn btn-danger" ng-href="{{ url('/trailler/delete') }}/<% row.id %>">Delete</a> -->
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-lg-12">
              <div class="panel panel-default">
                  <div class="panel-heading">Singer <button class="btn btn-danger" ng-click="new()">Reset</button> </div>
                  <div class="panel-body">
                    @if (isset($message))
                    <div class="alert alert-info">{{$message}}</div>
                    @endif
                      <div class="col-lg-12 form-group">
                        <input type="hidden" ng-model="id" value="">
                        <input type="text" class="form-control disabled" ng-model="title" name="title" placeholder="Name" />
                      </div>
                      <div class="col-lg-12 form-group">
                        <input type="hidden" ng-model="id" value="">
                        <select name="gender" class="form-control">
                          <option value="male">Male</option>
                          <option value="female">Female</option>
                        </select>
                      </div>
                      <div class="col-lg-12 form-group">
                        <button class="btn btn-success btn-block" ng-click="save()">Save</button>
                      </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
