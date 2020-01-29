@extends('layouts.app')
@section('content')
<script src="{{ url('public/js/controller/videoController.js') }}"></script>
<div class="container" ng-controller="Ctrl">
<div class="col-md-6">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
           <div class="panel-heading"> <button class="btn btn-primary col-lg-offset-10" ng-click="new()">Refresh</button> </div> 
          <div class="panel-body">
            <!-- <div ng-bind-html="err_message"></div> -->
              <div class="col-lg-12 form-group">
                <input type="hidden" ng-model="id" value="">
                <input type="text" class="form-control disabled" ng-model="title" name="title" placeholder="Video Name" />
              </div>
              <div class="col-lg-12 form-group">
                <input type="date" name="post_date" ng-model="post_date" onLoad="getdate()" class="form-control" />
              </div>

               <!--Ashok Changes-->
              <div class="col-lg-6 form-group">
                <select name="label" class="form-control" ng-model="label" >
                    <option value="" selected>Select Label</option>
                  @foreach($label as $val)
                    <option value="{{ $val->id }}">{{ $val->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-lg-6 form-group" style="padding-bottom:20px;">
                  <input type="checkbox" name="featured_geo" value="1" > Show Worldwide
              </div>
              <!--ashok changes-->
              
              <div class="col-lg-12 form-group">
                <select name="txt_type" ng-model="txt_type" class="form-control">  
                  <option value="bollywood-gupshup">Gupshup</option>
                  <option value="kids">Kids</option>
                </select>
                <br />
                <input type="checkbox" name="txt_feature" value="1" /> Feature
              </div>
              <div class="col-md-12 form-group">
                  <img style="width:200px" name="cover_url" ng-src="<% (cover_url != null ? cover_url : '//placehold.it/200') %>" />
                  <br>
                <label>Cover Image for Movie</label>
                <input type="file" name="file_cover_url" file-model="file_cover_url" class="form-control" />
                <span class="progress" style="border:solid 2px #ccc;width:320px" ng-show="file_cover_url.progress >= 0">
                  <div style="width:<% file_cover_url.progress %>%" ng-bind="file_cover_url.progress + '%'" class="ng-binding"></div>
                </span>
                <span ng-show="file_cover_url.result">Upload Successful</span>
              </div>
              <div class="col-lg-12 col-md-12">
                <fieldset>
                  <legend>Artist</legend>
                  <div class="col-md-12">
                    <div class="input-group">
                        <input type="hidden" ng-model="id" value="">
                      <input type="text" class="form-control" ng-model="artist_title" name="title" placeholder="New Artist" />
                      <span class="input-group-btn">
                        <button class="btn btn-secondary btn-success"  ng-click="addNewArtist()">Add New</button>
                      </span>
                    </div>
                  <div class="col-lg-6 col-md-6">
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
                </fieldset>
              </div>
              <div class="col-md-12 form-group">
              <fieldset>
                <div class="col-md-12 form-group">
                  <label>Mp4 720</label>
                  <input type="file" class="form-control" name="file_mpd_url" file-model="file_mpd_url"/>
                  <span class="progress" name="mpd-prog" ng-show="file_mpd_url.progress > 0">
                    <img src="{{ url('/public/img/spinner-mini.gif') }}"/> | <% file_mpd_url.progress %> % | 
                  </span>
                  <span name="mpd-status"></span>
                </div>
                <div class="col-md-12 form-group">
                  <label>Mp4 360</label>
                  <input type="file" class="form-control" name="file_mp4_url" file-model="file_mp4_url"/>
                  <span class="progress" ng-show="file_mp4_url.progress > 0 && file_mp4_url.result == undefined" style="width:100%">
                    <img src="{{ url('/public/img/spinner-mini.gif') }}"/>| <% file_mp4_url.progress %> %
                  </span>
                </div>
              </fieldset>
            </div>
              <div class="col-lg-12 form-group">
                <button class="btn btn-success btn-block" name="btn_save" ng-click="save()">Save</button>
              </div>
          </div>
        </div>
    </div>
  </div>
</div>
<div class="row">
<div class="col-md-6">
  <div class="panel panel-default">
    <div class="panel-heading">List <img name="movies_loader" style="display: none" src="{{ url('/public/img/spinner-mini.gif') }}"/></div>
      <div class="panel-body">
        <table role="grid" datatable="ng" class="table table-responsive">
          <thead>
            <tr>
              <th>Title</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="row in data">
              <td><% row.name %></td>
              <td>
                <a class="btn btn-link" ng-click="editItem(row.id,'{{ url('/video/save') }}')">View</a>
                @role("Admin")
                <a href="#" class="btn btn-danger">Delete</a>
                @endrole
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
@endsection
