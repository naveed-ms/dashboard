@extends('layouts.app')
@section('content')
<script src="{{ url('public/js/controller/trackmapController.js') }}"></script>
<div class="container" ng-controller="Ctrl">
    <div class="row">
      <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">Mapped</div>
            <div class="panel-body">
              <table role="grid" datatable="ng" class="table table-responsive">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Movie</th>
                    <th>Artist</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr ng-repeat="row in data" ng-if="data[$index].artist > 0">
                    <td><% row.track %></td>
                    <td><% row.album %></td>
                    <td><% row.artist %></td>
                    <td>
                      <a class="btn btn-link" ng-click="editItem(row.id,'{{ url('/trailler/save') }}')">View</a>
                    <!-- <a class="btn btn-danger" ng-href="{{ url('/trailler/delete') }}/<% row.id %>">Delete</a> -->
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="panel-footer"></div>
          </div>
      </div>




        <div class="col-md-6">
          <div class="row">
            <div class="col-lg-12">
              <div class="panel panel-default">
                  <div class="panel-heading">Track information </div>
                  <div class="panel-body">
                    @if (isset($message))
                    <div class="alert alert-info">{{$message}}</div>
                    @endif
                    <div class="row">
                      <div class="col-lg-12">
                        <input type="hidden" ng-model="id" value="">
                        <input type="text" class="form-control disabled" ng-model="title" name="title" placeholder="Track" />
                      </div>
                    </div>
                    <br />
                    <div class="row">
                      <div class="col-lg-12">
                        <select class="form-control" name="music_director">
                          @foreach ($music_directors as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->name }} </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <br />
                    <div class="row">
                      <div class="col-lg-12">
                        <select class="form-control" name="genere">
                          @foreach ($generes as $key => $value)
                            <option value="{{ $value->name }}">{{ $value->name }} </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <br />
                    <div class="row">
                      <div class="col-lg-12">
                        <fieldset>
                          <legend>Singer</legend>
                          <div class="col-lg-6">
                            <select class="form-control" name="singer_list" multiple style="height:100px">
                              @foreach ($singers as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }} </option>
                              @endforeach
                            </select>
                            <br />
                            <button class="btn btn-primary btn-block" ng-click="addSinger()"> >> </button>
                          </div>
                          <div class="col-lg-6">
                            <select class="form-control" name="singer" multiple style="height:100px">
                            </select>
                            <br />
                            <button class="btn btn-danger btn-block" ng-click="removeSinger()"> << </button>
                          </div>
                        </fieldset>
                      </div>
                      <div class="col-lg-12">
                        <fieldset>
                          <legend>Artist</legend>
                          <div class="col-lg-6">
                            <select class="form-control" name="artist_list" multiple style="height:100px">
                              @foreach ($artists as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }} </option>
                              @endforeach
                            </select>
                            <br />
                            <button class="btn btn-primary btn-block" ng-click="addArtist()"> >> </button>
                          </div>
                          <div class="col-lg-6">
                            <select class="form-control" name="artist" multiple style="height:100px">

                            </select>
                            <br />
                            <button class="btn btn-danger btn-block" ng-click="removeArtist()"> << </button>
                          </div>
                        </fieldset>
                      </div>
                    </div>
                    <br />
                    <div class="row">
                      <div class="col-lg-12">
                        <button class="btn btn-success btn-block" ng-click="save()">Save</button>
                      </div>
                    </div>
                    <br />
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="alert alert-info err hidden"></div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-lg-12">
              <div class="panel panel-default">
                  <div class="panel-heading">Un Mapped</div>
                  <div class="panel-body">
    								<table role="grid" datatable="ng" class="table table-responsive">
    									<thead>
    										<tr>
                          <th>Title</th>
                          <th>Movie</th>
    											<th>Artist</th>
                          <th>Action</th>
    										</tr>
    									</thead>
    									<tbody>
    										<tr ng-repeat="row in data" ng-if="data[$index].artist == 0">
                          <td><% row.track %></td>
                          <td><% row.album %></td>
                          <td><% row.artist %></td>
                          <td>
                            <a class="btn btn-link" ng-click="editItem(row.id,'{{ url('/trailler/save') }}')">View</a>
                          <!-- <a class="btn btn-danger" ng-href="{{ url('/trailler/delete') }}/<% row.id %>">Delete</a> -->
                          </td>
    										</tr>
    									</tbody>
    								</table>
                  </div>
                  <div class="panel-footer"></div>
                </div>
            </div>
          </div>
        </div>
      </div>
@endsection
