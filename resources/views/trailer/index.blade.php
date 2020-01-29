@extends('layouts.app')
@section('content')
<script src="{{ url('public/js/controller/trailer.js') }}"></script>
<div class="container" ng-controller="Ctrl">
    <div class="row">
      <div class="col-lg-12">
        <div class="card panel-default">
          <div class="card-head style-default-light">
            <header>
              Trailers
          </header>
          </div>
          <div class="card-body">
            <div ng-bind-html="err_message"></div>
            <div class="col-md-12 form-group">
              <label>Select Movie</label>
              <select name="lst_movie" class="form-control">
                @foreach($movies as $val)
                <option value="{{ $val->id }}">{{ $val->name }}</option>
                @endforeach
              </select>
            </div>
            <br />
            <div class="col-md-12 form-group">
                <input type="text" name="txt_name" class="form-control" placeholder="Name" />
            </div>
            <div class="col-md-12 form-group">
              <label>Mp4 720</label>
              <input type="file" class="form-control" name="file_mp4_720_url" />
              <label>Mp4 360</label>
              <input type="file" class="form-control" name="file_mp4_360_url" />
            </div>
            <div class="col-lg-12 form-group">
              <button class="btn btn-success btn-block" ng-click="save()">Save</button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
</div>
@endsection
