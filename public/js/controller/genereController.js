app.controller("Ctrl",function($scope,$http){
  $http({
    method:'post',
    url:baseUrl+"/api/get/Genere"
    }).then(function(response){
      $scope.data = response.data;
    }).then(function(error){
  });

  $scope.title = "";
  $scope.type = "";
  $scope.id = 0;

  $scope.new = function (){
    $scope.title = "";
    $scope.type = "";
    $scope.id = 0;
  };

  $scope.editItem = function(id,url){
    $http({
      method:'GET',
      url:baseUrl+"/api/get/Genere/" + id
      }).then(function(response){
        console.log(response.data);
        $scope.title = response.data.name;
        $scope.type = response.data.type;
        $scope.id = response.data.id;
        $("[name='type']").val(response.data.type);
        $("form").attr("action",url + id);
      }).then(function(error){
    });
  };

  $scope.save = function (){
    $http({
      method:"POST",
      url:baseUrl+"/genere/save",
      data:{
        id:$scope.id,
        name:$scope.title,
        type:$("[name='type']").val()
      }
    }).then(function(resp){
      if (resp.data == "Saved"){
        alert("Update");
        $scope.id = 0;
        $http({
          method:'post',
          url:baseUrl+"/api/get/Genere"
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
