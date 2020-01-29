
@extends('layouts.app')
@section('content')
<script src="{{ url('/js/controller/categoryController.js') }}"></script>
<div class="container" ng-controller="Ctrl">
    <div class="row">
        <div class="col-md-6">
          <div class="panel panel-default">
              <div class="panel-heading"></div>
              <div class="panel-body">
								<table role="grid" datatable="ng" class="table table-responsive">
									<thead>
										<tr>
											<th>Name</th>
											<th>Slug</th>
                      <th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="row in data">
											<td><% row.name %></td>
											<td><% row.slug %></td>
                      <td class="btn-group"><a class="btn btn-warning" ng-click="editItem(row.id,'{{ url('/category/') }}/{{ 'update' }}/')">Edit</a><a class="btn btn-danger" ng-href="{{ url('/category/delete') }}/<% row.id %>">Delete</a></td>
										</tr>
									</tbody>
								</table>
              </div>
              <div class="panel-footer"></div>
            </div>
          </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Category <a class="btn btn-primary pull-right" href="{{ url('/category/') }}">Add New</a></div>
                <div class="panel-body">
                  @if (isset($message))
                  <div class="alert alert-info">{{$message}}</div>
                  @endif
                  <form method="post" role="form" action="{{ url('/category/create') }}">
                    {!! csrf_field() !!}
                    <div class="row">
                      <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                          <label class="col-md-4 control-label">Name</label>

                          <div class="col-md-6">
                              <input type="text" ng-model="name" class="form-control" name="name" ng-init="name='<?php echo (null !== old("name") ? old("name") : (null !== $data["name"] ? $data["name"] : "")); ?>'">
                              @if ($errors->has('name'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('name') }}</strong>
                                  </span>
                              @endif
                          </div>
                      </div>
                   </div>
                   <br>
                   <div class="row">
                        <div class="col-md-6 col-md-offset-4">
                            <input type="submit" class="btn btn-success btn-block" value="Save" />
                        </div>
                      </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
@endsection
