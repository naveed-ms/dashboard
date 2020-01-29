app.controller("Ctrl",function($scope,$http){
  $http({
    method:'post',
    url:baseUrl+"/api/get/Trailler"
    }).then(function(response){
    $scope.data = response.data;
    }).then(function(error){
      console.log(error);
  });

  $scope.editItem = function(id,url){
    $http({
      method:'GET',
      url:baseUrl+"/api/get/Trailler/" + id
      }).then(function(response){
        $scope.title = response.data.name;
        $scope.id = response.data.id;
        $("[name='movie']").val(response.data.movie_id);
        $("form").attr("action",url + id);
      }).then(function(error){
    });
  };

  $scope.save = function (){
    $http({
      method:"POST",
      url:baseUrl+"/traillers",
      data:{
        id:$scope.id,
        name:$scope.title,
        movie_id:$("[name='movie']").val()
      }
    }).then(function(resp){
      if (resp.data == "Saved"){
        alert("Update");
        $http({
          method:'post',
          url:baseUrl+"/api/get/Trailler"
          }).then(function(response){
          $scope.data = response.data;
        },function(error){
        });
      }
    },function (){
      alert("An error while updating record");
    });
  };

});
