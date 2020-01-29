app.controller("Ctrl",function($scope,$http,$location){
      var url = $location.absUrl().split("?");
      var id  = $location.absUrl().split("?")[0].split("/");
      if (!isNaN(id[id.length -1])){
        $scope.movie_id = id[id.length -1];
      }


  $scope.movie_changed = function (){
    window.location = baseUrl + "/tracks/" + $scope.movie_id;
   };


   $scope.addNew = function(){
     if ($scope.movie_id != undefined){
      window.location = baseUrl + "/tracks/addNew/" + $scope.movie_id;
     }else{
      alert("Please select movie or album");
      //  window.location = baseUrl + "/tracks/addNew";
     }

   }

});
