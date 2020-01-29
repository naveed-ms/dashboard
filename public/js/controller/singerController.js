app.controller("Ctrl",function($scope,$http){
  $http({
    method:'post',
    url:baseUrl+"/api/get/Singer"
    }).then(function(response){
      $scope.data = response.data;
    }).then(function(error){
  });


  $scope.new = function (){
    $scope.title = "";
    $scope.id = 0;
  };

  $scope.editItem = function(id,url){
    $http({
      method:'GET',
      url:baseUrl+"/api/get/Singer/" + id
      }).then(function(response){
        $scope.title = response.data.name;
        $scope.id = response.data.id;
        $("[name='gender']").val(response.data.gender);
        $("form").attr("action",url + id);
      }).then(function(error){
    });
  };

  $scope.save = function (){
    $http({
      method:"POST",
      url:baseUrl+"/singer/save",
      data:{
        id:$scope.id,
        name:$scope.title,
        gender:$("[name='gender']").val()
      }
    }).then(function(resp){
      if (resp.data == "Saved"){
        alert("Update");
        $scope.id = 0;
        $http({
          method:'post',
          url:baseUrl+"/api/get/Singer"
          }).then(function(response){
            $scope.data = response.data;
          }).then(function(error){
        });
      }
    },function (){
      alert("An error while updating record");
    });
  };

});
