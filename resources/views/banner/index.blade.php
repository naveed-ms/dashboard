@extends('layouts.app') @section('content')
<script src="{{ url('public/js/controller/bannerController.js') }}"></script>
<div class="container" ng-controller="Ctrl">
    <div class="row">

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Banners List</div>
                <div class="panel-body">
                    <table role="grid" datatable="ng" class="table table-responsive">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>name</th>
				<th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="val in banners">
                                <td><% val.id %></td>
                                <td><% val.name %></td>
                               	<td><img src="<% val.image_url %>" style="width: 100px; height: 50px" /></td>
                                <td><a href="#" ng-click="view(val.id)">Edit</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Banners</div>
                <div class="panel-body">
                    <div class="col-md-12 form-group">
                        <input type="hidden" class="form-control" name="id" value="" ng-model="txt_id" placeholder="Required Field" required/>
                        <label class="control-label"><b>Name:</b></label>
                        <input type="text" class="form-control" name="name" value="" ng-model="txt_name" placeholder="John Cena" required/>
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="control-label"><b>Upload:</b></label>
                        <input type="file" class="form-control" name="file_image_url" file-model="file_image_url" placeholder="upload the file" required/>
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="control-label"></b>Select Section:</b></label>
                        <select id="1" name="group" class="selectboxit form-control" required ng-model="section" ng-change="changeGroup()">      
                            <option value="1">Album</option>
                            <option value="2">Single Track</option>
                            <option value="3">Video</option>
                        </select>
                    </div>

                    <div class="col-md-12" ng-show="section == 1">
                        <label>Album : </label>
                        <select name="txt_album" class="form-control" ng-model="album" > 
                            <option ng-repeat="al in albumData" value="<% al.id %>"><% al.name %></option>
                        </select>
                        <br />
                    </div>
                    <div class="col-md-12" ng-show="section == 2">
                        <label>Album : </label><br />
                        <select name="txt_album" class="form-control" ng-model="album" ng-change="changeAlbum()">  
                            <option ng-repeat="al in albumData" value="<% al.id %>"><% al.name %></option>
                        </select>
                        <br />
                        <label>Track : </label><br />
                        <select name="txt_track" class="form-control" ng-model="txt_track"> 
                            <option ng-repeat="tr in trackData" value="<% tr.id %>"><% tr.name %></option>
                        </select>
                        <br />
                    </div>
                    <div class="col-md-12" ng-show="section == 3">
                        <label>Type : </label><br />
                        <select name="txt_type" class="form-control" ng-model="type" ng-change="changeType()"> 
                            <option></option>
                            <option value="1">Trailer</option>
                            <option value="2">Bollywood Gupshup</option>
                        </select>
                        <br />
                        <label>Video : </label><br />
                        <select name="txt_video" class="form-control" ng-model="txt_video"> 
                            <option ng-repeat="vid in videoData" value="<% vid.id %>"><% (vid.title ? vid.title : vid.name) %></option>
                        </select>
                        <br />
                    </div>
                    <div class="col-md-12 form-group">
                        <button type="submit" class="btn" name="button" value="Submit" ng-click="save()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="albums sub">
                <hr>
                <h3>Album Form</h3>
                <hr>
                <div class="form-group">
                    <label class="control-label"><b>Album Id:</b></label>

                    <input type="text" class="form-control" name="albumid" value="" required/>
                </div>
                <div class="form-group">
                    <label class="control-label"><b>Year:</b></label>

                    <input type="number" class="form-control" name="Year" value="" placeholder="Type year Only e.g 1999" required/>
                </div>
                <div class="form-group">
                    <label class="control-label"><b>Total Track:</b></label>

                    <input type="text" class="form-control" name="totaltrack" value="" required/>
                </div>
            </div> -->

    <!-- SINGLE TRACK FORMS-->


    <!-- <div class="singletrack sub">
                <hr>
                <h3>Track Form</h3>
                <hr>
                <div class="form-group">
                    <label class="control-label"><b>Song Id:</b></label>

                    <input type="text" class="form-control" name="songid" value="" required/>
                </div>
                <div class="form-group">
                    <label class="control-label"><b>Album Id:</b></label>

                    <input type="text" class="form-control" name="albumid2" value="" required/>
                </div>
                <div class="form-group">
                    <label class="control-label"><b>Year:</b></label>

                    <input type="number" class="form-control" name="Year" value="" placeholder="Type year Only e.g 1999" required/>
                </div>
                <div class="form-group">
                    <label class="control-label"><b>Total Track:</b></label>

                    <input type="text" class="form-control" name="totaltrack" value="" required/>
                </div>

            </div> -->

    <!-- VIDEO FORMS-->


    <!-- <div class="video sub">
                <hr>
                <h3>Video Form</h3>
                <hr>
                <div class="form-group">
                    <label class="control-label"><b>Trailer Id:</b></label>

                    <input type="text" class="form-control" name="trailerid" value="" required/>
                </div>
                <div class="form-group">
                    <label class="control-label"><b>Video Url:</b></label>

                    <input type="number" class="form-control" name="video" value="" required/>
                </div>
                <div class="form-group">
                    <label class="control-label"><b>Share Url:</b></label>

                    <input type="text" class="form-control" name="share" value="" required/>
                </div>
            </div> -->



    @endsection
