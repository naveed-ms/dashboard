@extends('layouts.app')
@section('content')
<script src="{{ url('public/js/controller/trackBulk.js') }}"></script>
<div class="container" ng-controller="Ctrl">
  <div class="row">
    <div class="panel panel-default">
      <div class="panel-heading"></div>
      <div class="panel-body">
        <div class="col-md-12 form-group">
          <label>Select Movie</label>
          <select name="lst_movie" class="form-control">
            @foreach($movies as $val)
            <option value="{{ $val->id }}" {{{ ($movie_id == $val->id ? "Selected" : "") }}}>{{ $val->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-12 form-group">
          <label>Title</label>
          <input type="text" class="form-control" ng-model="txt_title" name="txt_title" placeholder="Title" />
        </div>
        <div class="col-md-12 form-group">
        <label>MP3</label>
        <input type="file" class="form-control" name="file_mp3_url" txt-title="txt_title" file-model="file_mp3_url" />
        <span class="progress" ng-show="file_mp3_url.progress > 0 && file_mp3_url.result == undefined" style="width:100%">
          <img src="{{ url('/public/img/spinner-mini.gif') }}"/>
        </span>
        </div>
        <div class="col-md-3">
          <button class="btn-primary" ng-click="add()"> Add </button>
        </div>
      </div>
      <div class="panel-footer"></div>
    </div>
  </div>
   <div class="row">
    <div class="panel panel-default">
      <div class="panel-heading">Exists list</div>
      <div class="panel-body">
        <table role="grid" name="tbl_tracks_old" class="table">
          <thead>
            <tr>
              <th>Title</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
      <div class="panel-footer"></div>
    </div>
  </div>
  <div class="row">
    <div class="panel panel-default">
      <div class="panel-heading">Uploading list</div>
      <div class="panel-body">
        <table role="grid" name="tbl_tracks" class="table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Movie</th>
              <th>status</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
      <div class="panel-footer"></div>
    </div>
  </div>
</div>
@endsection
