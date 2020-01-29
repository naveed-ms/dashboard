@extends('layouts.app')
@section('content')
<script src="{{ url('public/js/controller/trackEdit.js') }}"></script>
<div class="container" ng-controller="Ctrl">
    <div class="row">
      <div class="col-lg-12">
        <div class="card panel-default">
          <div class="card-head style-default-light">
            <header>
               <div class="col-lg-12 col-lg-offset-10">
                <a href="{{ url('/tracks/addNew') }}{{{ ($movie_id > 0 ? '/' . $movie_id : "")  }}}" class="hidden btn btn-primary pull right" ng-click="new()">Add new</a>
                <a href="{{ url('/tracks') }}{{{ ($movie_id > 0 ? '/' . $movie_id : "")  }}}" class="btn btn-primary pull right">Back</a>
              </div> 
          </header>
          </div>
          <div class="card-body">
            <!-- <div ng-bind-html="err_message"></div> -->
            <div class="col-md-12 form-group">
              <label>Title</label>
              <input type="text" class="form-control" required ng-model="txt_title" name="txt_title" placeholder="Title" />
            </div>
            <div class="col-md-6 form-group">
              <label>Select Movie</label>
              <select name="lst_movie" class="form-control" disabled="true">
                <option value="" selected>Select Movie</option>
                @foreach($movies as $val)
                <option value="{{ $val->id }}" {{{ ($movie_id == $val->id ? "Selected" : "") }}}>{{ $val->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label>Select music director</label>
              <select name="music_director" class="form-control">
                @foreach($music_directors as $val)
                <option value="{{ $val->id }}">{{ $val->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="row">
                  <div class="col-lg-6">
                    <fieldset>
                      <legend>Singer</legend>
                      <div class="input-group">
                        <input type="text" class="form-control" required ng-model="singer_title" name="txt_title" placeholder="New Singer" />
                        <span class="input-group-btn">
                          <button class="btn btn-secondary btn-success" type="button" ng-click="addNewSinger()">Add New</button>
                          <!-- <button class="btn btn-success btn-block" ng-click="addcArtist()">Add New</button> -->
                        </span>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>
            <br>
                  <div class="col-lg-6">
                    <select class="form-control" name="singer_list" multiple style="height:100px">
                      @foreach($singers as $val)
                      <option value="{{ $val->id }}">{{ $val->name }}</option>
                      @endforeach
                    </select>
                    <br />
                    <button class="btn btn-primary btn-block" ng-click="addSinger()"> Add </button>
                  </div>
                  <div class="col-lg-6">
                    <select class="form-control" name="singer" multiple style="height:100px">
                    </select>
                    <br />
                    <button class="btn btn-danger btn-block" ng-click="removeSinger()"> Remove </button>
                  </div>
                </fieldset>
              </div>
              <div class="col-lg-12">
                  <div class="row">
                <fieldset>
                  <legend>Artist</legend>
                  <div class="col-md-6">
                    <div class="input-group">
                        <input type="hidden" ng-model="id" value="">
                      <input type="text" class="form-control" ng-model="artist_title" name="title" placeholder="New Artist" />
                      <span class="input-group-btn">
                        <button class="btn btn-secondary btn-success"  ng-click="addNewArtist()">Add New</button>
                      </span>
                    </div>
                  </div>
                  </fieldset>
                  </div>
                  <br>
                  <!-- Popup Boxes -->
                  <div class="col-lg-6">
                    <select class="form-control" name="artist_list" multiple style="height:100px">
                      @foreach($artists as $val)
                      <option value="{{ $val->id }}">{{ $val->name }}</option>
                      @endforeach
                    </select>
                    <br />
                    <button class="btn btn-primary btn-block" ng-click="addArtist()"> Add </button>
                  </div>

                  <div class="col-lg-6">
                    <select class="form-control" name="artist" multiple style="height:100px">

                    </select>
                    <br />
                    <button class="btn btn-danger btn-block" ng-click="removeArtist()"> Remove </button>
                  </div>
                  <!-- / End popup Boxes -->

              </div>
            </div>
            <br />
            <div class="col-md-12 form-group">
              <label>Select genere</label>
              <select name="genere" class="form-control">
                @foreach($generes as $val)
                  <option value="{{ $val->id }}">{{ $val->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 form-group">
                <img style="width:200px" ngf-src="file_cover_url" src="<% cover_url %>" />
                <br>
              <label>Cover Image</label>
              <input type="file" name="file_cover_url" file-model="file_cover_url" class="form-control" />
              <!--<img ngf-src="file_cover_url" class="img-rounded img-responsive" style="width:100%;text-align:center">-->
              <span class="progress" ng-show="file_cover_url.progress > 0 && file_cover_url.result == undefined" style="width:100%">
                <img src="{{ url('/public/img/spinner-mini.gif') }}"/>
              </span>
            </div>
            <div class="col-md-9">
              <label>MP3</label>
              <input type="file" class="form-control" name="file_mp3_url" txt-title="txt_title" file-model="file_mp3_url" />
              <span class="progress" ng-show="file_mp3_url.progress > 0 && file_mp3_url.result == undefined" style="width:100%">
                <img src="{{ url('/public/img/spinner-mini.gif') }}"/> | <% file_mp3_url.progress %>
              </span>

              <!--ashok changes-->
              <div class="col-lg-12 form-group" style="padding:20px 0px 10px 0px;">
                <input type="checkbox" name="video_geo" value="1" > Show Worldwide
              </div>
              <!--ashok changes-->

              <label>Mp4 720</label>
              <input type="file" class="form-control" name="file_mpd_url" file-model="file_mpd_url"/>
              <span class="progress" name="mpd-prog" ng-show="file_mpd_url.progress > 0">
                <img src="{{ url('/public/img/spinner-mini.gif') }}"/> | <% file_mpd_url.progress %> % | 
              </span>
              <span name="mpd-status"></span>
              <br>
              <label>Mp4 360</label>
              <input type="file" class="form-control" name="file_mp4_url" file-model="file_mp4_url"/>
              <span class="progress" ng-show="file_mp4_url.progress > 0 && file_mp4_url.result == undefined" style="width:100%">
                <img src="{{ url('/public/img/spinner-mini.gif') }}"/> <% file_mp4_url.progress %> %
              </span>
            </div>
            <div class="col-lg-12">
              <button class="btn btn-success btn-block" name="btn_save" id="btn_save" ng-click="save()">Save</button>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
