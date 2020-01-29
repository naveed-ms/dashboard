@extends('layouts.app')
@section('content')
<script src="{{ url('public/js/controller/trackIndex.js') }}"></script>
<div class="container" ng-controller="Ctrl">
    <div class="row">
      <div class="col-lg-12">
        <div class="card card-default">
          <div class="card-head style-default-light">
            <header>
              <div class="col-lg-6 form-group">
                <select class="form-control" ng-model="movie_id" ng-change="movie_changed()" style="margin-top:15px" required="required">
                  @foreach($movies as $val)
                   <option value="{{ $val->id }}" {{{ ($val->id == $movie_id) ? "selected" : "" }}}>{{ $val->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-lg-6 form-group">
                <button class="btn btn-primary btn-block" style="margin-top:15px" ng-click="addNew()">Add New</button>
              </div>
          </header>
          </div>

          <div class="card-body">

            <table role="grid" class="table table-responsive">
              <thead>
                <tr>
                  <th>Cover</th>
                  <th>Title</th>
                  <th>Links</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($data as $val)
                <tr>
                  <td><img style="width:90px; height:100px" src="{{{ isset($val->cover_url) ? $val->cover_url  : 'http://placehold.it/90' }}}" /></td>
                  <td>{{ $val->name }}</td>
                  <td>
@if ($val->video_share_url)
 <a href="{{ $val->video_share_url }}">Share Url</a> 
@endif
</td>
                  <td>
                    <a href="{{ url('/tracks/edit/') }}/{{ $val->id }}">View</a>
                    @role("Admin")
                    <a href="{{ url('/tracks/delete/') }}/{{ $val->id }}" class="btn btn-danger">Delete</a>
                    @endrole
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="card-footer">
            {!! $data->render() !!}
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
