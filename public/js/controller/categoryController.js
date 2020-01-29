app.controller("Ctrl",function($scope,$http){
  $scope.data = [];
    $scope.editItem = function(id,url){
      $(".alert").hide();
    $http({
      method:'get',
      url:baseUrl+"/category/get/" + id
    }).then(function(response){
      $scope.name = response.data.name;
      $("form").attr("action",url + id);
    }).then(function(error){
    });
  };

  $http({
    method:'get',
    url:baseUrl+"/category/get"
  }).then(function(response){
    $scope.data = response.data;
  }).then(function(error){
  });

});
