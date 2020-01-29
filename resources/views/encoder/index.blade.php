@extends('layouts.app')
@section('content')
<script type="text/javascript">
app.controller("myCtrl",function($scope,$http){
  $http({
    method:'post',
    url:baseUrl+"/api/get/Encode"
  }).then(function(response){
    $scope.data = response.data;
  }).then(function(error){
  });
});
</script>
<div class="container">
    <div class="row">
        <div class="col-md-6">
          <div class="panel panel-default">
              <div class="panel-heading"></div>
              <div class="panel-body" ng-controller="myCtrl">
								<table role="grid" datatable="ng" class="table table-responsive">
									<thead>
										<tr>
											<th>Job</th>
											<th>Movie</th>
											<th>Url</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="row in data">
											<td><% row.JID %></td>
											<td><% row.Movie %></td>
											<td><% row.Url %></td>
										</tr>
									</tbody>
								</table>
              </div>
              <div class="panel-footer"></div>
            </div>
          </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Encoder <span id="jobId"></span><img src="<?php echo config("app.url"); ?>/images/loader.gif" alt="" name="loader" style="height:50px;width:120px;display:none"/> <a href="{{ url('/encoder/movie') }}" class="btn btn-primary pull-right">Add Movie</a></div>
                <div class="panel-body">
                  <div class="row">
                    <div class="col-lg-12">
                      <input type="text" class="form-control" name="url" placeholder="Input Url" required />
                    </div>
                  </div>
                  <br />
                  <div class="row">
                    <div class="col-lg-12">
                      <select class="form-control" name="cat">
                        <option value="bollywood">Bollywood</option>
                        <option value="hollywood">Hollywood</option>
			<option value="pakistani">Pakistani</option>
                      </select>
                    </div>
                  </div>
                  <br />
                  <div class="row">
                    <div class="col-lg-12">
                      <select class="form-control" name="subcat">
                        <option value="0-9">0-9</option>
			<option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="G">G</option>
                        <option value="H">H</option>
                        <option value="I">I</option>
                        <option value="J">J</option>
                        <option value="K">K</option>
                        <option value="L">L</option>
                        <option value="M">M</option>
                        <option value="N">N</option>
                        <option value="O">O</option>
                        <option value="P">P</option>
                        <option value="Q">Q</option>
                        <option value="R">R</option>
                        <option value="S">S</option>
                        <option value="T">T</option>
                        <option value="U">U</option>
                        <option value="V">V</option>
                        <option value="W">W</option>
                        <option value="X">X</option>
                        <option value="Y">Y</option>
                        <option value="Z">Z</option>
                      </select>
                    </div>
                  </div>
                  <br />
                  <div class="row">
                    <div class="col-lg-12">
                      <select class="form-control" name="movie">
                        @foreach ($movies as $key => $value)
                          <option value="<?php echo $value->name; ?>"><?php echo $value->name; ?></option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <br />
                  <div class="row">
                    <div class="col-lg-12">
                      <input type="text" class="form-control" name="file" placeholder="File Name" />
                    </div>
                  </div>
                  <br />
                  <div class="row">
                    <div class="col-lg-12">
                      <button class="btn btn-danger btn-block">Encode</button>
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
          </div>
          <hr />
<div class="err"></div>
        <script src="<?php echo config("app.url"); ?>/js/encode.js"></script>
@endsection
