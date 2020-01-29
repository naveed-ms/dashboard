@extends('layouts.app')
@section('content')
<script src="{{ url('public/js/controller/mediaController.js') }}"></script>
<div class="container" ng-controller="Ctrl">
  @if($errors->any())
  <div class="alert alert-danger"><ul>
  @foreach($errors->all() as $key => $val)
  <li>{{ $val }}</li>
  @endforeach
  </ul></div>
  @endif
  <form method="post" enctype="multipart/form-data" action="{{ url('/media/upload') }}">
    {!! csrf_field() !!}
  <div class="row">
    <div class="col-lg-6">
      <div class="panel panel-default">
        <div class="panel-heading"></div>
        <div class="panel-body">
          <div class="col-md-12 form-group">
            <label>Movie</label>
            <select class="form-control" name="movie">
              @foreach($movies as $album)
                <option value="{{ $album->id }}">{{ $album->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-12 form-group">
            <label>Music Director</label>
            <select class="form-control" name="music_director">
              @foreach($music_directors as $m_dir)
                <option value="{{ $m_dir->id }}">{{ $m_dir->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-12 form-group">
            <label>Genere</label>
            <select class="form-control" name="genere">
              @foreach($generes as $genere)
                <option value="{{ $genere->id }}">{{ $genere->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-12 form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" />
          </div>
          <div class="col-md-12 form-group">
            <label>MP3</label>
            <input type="file" name="mp3_file" class="form-control" />
          </br>
            <label>MP4 720</label>
            <input type="file" name="mp4_720_file" class="form-control" />
          </br>
            <label>MP4 360</label>
            <input type="file" name="mp4_360_file" class="form-control" />
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="panel panel-default">
        <div class="panel-heading"></div>
        <div class="panel-body">
          <div class="col-md-12">
            <fieldset>
              <legend>Singer</legend>
              <div class="col-lg-6">
                <select class="form-control" name="singer_list" multiple style="height:170px">
                  @foreach($artists as $artist)
                    <option value="{{ $artist->id }}">{{ $artist->name }}</option>
                  @endforeach
                </select>
                <br />
                <span class="btn btn-primary btn-block" ng-click="addSinger()"> >> </span>
              </div>
              <div class="col-lg-6">
                <select class="form-control" name="singer" multiple style="height:170px">

                </select>
                <br />
                <span class="btn btn-danger btn-block" ng-click="removeArtist()"> << </span>
              </div>
            </fieldset>
          </div>
          <div class="col-md-12">
            <fieldset>
              <legend>Artist</legend>
              <div class="col-lg-6">
                <select class="form-control" name="artist_list" multiple style="height:170px">
                  @foreach($artists as $artist)
                    <option value="{{ $artist->id }}">{{ $artist->name }}</option>
                  @endforeach
                </select>
                <br />
                <span class="btn btn-primary btn-block" ng-click="addArtist()"> >> </span>
              </div>
              <div class="col-lg-6">
                <select class="form-control" name="artist" multiple style="height:170px">

                </select>
                <br />
                <span class="btn btn-danger btn-block" ng-click="removeArtist()"> << </span>
              </div>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br />
  <div class="panel panel-footer">
    <input type="submit" class="btn btn-block btn-success" value="Upload" />
  </div>
  </form>
</div>
@endsection
