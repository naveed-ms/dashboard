@extends('layouts.app')
@section('content')
<script src="{{ url('public/js/controller/videoMap.js') }}"></script>
<div class="container" ng-controller="Ctrl">
    <div class="row">
        <div class="col-xs-12">
            <button ng-click="new()" class="btn btn-primary pull-right">Refresh</button>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 form-group">
            <input type="text" name="txt_title" class="form-control" placeholder="Title" />
        </div>
        <div class="col-xs-12 form-group">
            <div class="col-md-6 form-group">
            <input type="checkbox" name="txt_featured" value="1" /> Featured
            <input type="checkbox" name="txt_featured" value="2" /> Ever Green

             <!--ashok changes-->
            <input type="checkbox" name="featured_geo" value="1" > Show Worldwide
            <!--ashok changes-->
            
            <br /><br />
              <label>Cover Image</label>
              <input type="file" name="file_cover_url" file-model="file_cover_url" class="form-control" />
              <!--<img ngf-src="file_cover_url" class="img-rounded img-responsive" style="width:100%;text-align:center">-->
              <span class="progress" ng-show="file_cover_url.progress > 0 && file_cover_url.result == undefined" style="width:100%">
                <img src="{{ url('/public/img/spinner-mini.gif') }}"/>
              </span>
            </div>
        </div>
        <div class="col-xs-12 form-group">
            <select name="txt_album" ng-model="txt_album" class="form-control" ng-change="getTracks()">
                @foreach($movies as $key => $val)
                <option value="{{$val->id}}">{{$val->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-6 form-group">
                <select name="txt_tracks" class="form-control" multiple style="height: 200px">
                    
                </select>
                <button class="btn btn-block btn-primary" name="btn_add" ng-click="add()">Add</button>
            </div>
            
            <div class="col-xs-6 form-group">
                <select name="txt_sel_tracks" class="form-control" multiple style="height: 200px">
                    
                </select>
                <button class="btn btn-block btn-danger" name="btn_remove" ng-click="remove()">Remove</button>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-xs-12">
        <div class="col-xs-6 form-group">
            <select name="txt_artists" class="form-control" multiple style="height: 200px">
                    @foreach($artists as $val)
                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                    @endforeach
            </select>
            <button class="btn btn-block btn-primary" name="btn_add_artist" ng-click="artistAdd()">Add</button>
        </div>
            
        <div class="col-xs-6 form-group">
            <select name="txt_sel_artists" class="form-control" multiple style="height: 200px">
                
            </select>
            <button class="btn btn-block btn-danger" name="btn_remove_artist" ng-click="artistRemove()">Remove</button>
        </div>
    </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button class="btn btn-block btn-success" ng-click="save()">Publish</button>
        </div>
    </div>

    <br/><br/>
    <div class="row">
        <div class="col-xs-12">
            <table role="grid" datatable="ng" class="table table-responsive">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="item in data">
                        <td><% item.name %></td>
                        <td><a href="#" ng-click="view(item.id)">Edit</a></td>
                    </tr>
                <tbody>
            </table>            
        </div>
    </div>
</div>
@endsection
