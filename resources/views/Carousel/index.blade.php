@extends('layouts.app')
@section('content')
<script src="{{ url('/public/js/controller/carouselController.js') }}"></script>
  <div class="container" ng-controller="Ctrl">
    <div class="panel panel-default">
      <div class="panel-heading">

      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <select class="form-control" name="album">
              @foreach($album as $key => $val)
              <option value="{{$val->id}}">{{$val->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <select class="form-control" name="category">
              @foreach($categories as $key => $val)
              <option value="{{$val->id}}">{{$val->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <button class="btn btn-block btn-primary" ng-click="add()">Add</button>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <table role="grid" name="carousel_table" datatable="ng" class="table table-responsive">
            <thead>
              <tr>
                <th>Name</th>
                <th>Carousel</th>
                <th>Order</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="carousel in carousels | orderBy: 'carousel'">
                <td><% carousel.title %></td>
                <td><% carousel.carousel %></td>
                <td><input type="number" value="<% carousel.m_order %>"/></td>
                <td><a href="#" ng-click="delete(carousel.a_id,carousel.c_id)">Delete</a></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="panel-footer">
        <button class="btn btn-success btn-block" ng-click="update()">Update !</button>
      </div>
    </div>
  </div>
</div>
@endsection
