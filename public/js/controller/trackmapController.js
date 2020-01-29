app.controller("Ctrl",function($scope,$http){
  $http({
    method:'post',
    url:baseUrl+"/api/get/Tracks"
    }).then(function(response){
      $scope.data = response.data;
    }).then(function(error){
  });

  $scope.editItem = function(id,url){
    $http({
      method:'GET',
      url:baseUrl+"/api/get/Tracks/" + id
      }).then(function(response){
        console.log(response.data);
        $scope.title = response.data.track.name;
        $scope.id = response.data.track.id;
        // ArtistId,Artist
        $("[name='artist'] option").remove();
        $("[name='singer'] option").remove();
          for (var i = 0; i < response.data.artists.length;i++){
            var val = response.data.artists[i].id;
            var text = response.data.artists[i].name;
            var option = "<option value='" + val + "'>" + text + "</option>";
            $("[name='artist']").append(option);
          }
          for (var i = 0; i < response.data.singers.length;i++){
            var val = response.data.singers[i].id;
            var text = response.data.singers[i].name;
            var option = "<option value='" + val + "'>" + text + "</option>";
            $("[name='singer']").append(option);
          }



        $("[name='music_director']").val(response.data.track.director_id);
        $("[name='genere']").val(response.data.track.genere);
        $("form").attr("action",url + id);
      }).then(function(error){
    });
  };

  $scope.addArtist = function (){
    $("[name='artist_list'] option:selected").each(function (index,ndx){
      var val = ndx.value;
      var text = ndx.text;
      var option = "<option value='" + val + "'>" + text + "</option>";
      $("[name='artist']").append(option);
    });
  };

  $scope.removeArtist = function (){
    $("[name='artist'] option:selected").remove();
  };


  $scope.addSinger = function (){
    $("[name='singer_list'] option:selected").each(function (index,ndx){
      var val = ndx.value;
      var text = ndx.text;
      var option = "<option value='" + val + "'>" + text + "</option>";
      $("[name='singer']").append(option);
    
    });
  };

  $scope.removeSinger = function (){
    $("[name='singer'] option:selected").remove();
  };

  $scope.save = function (){
    var artistLst = [];
    var singerLst =[];
    $("[name='artist'] option").each(function (index,ndx){
      artistLst.push(ndx.value);
    });

    $("[name='singer'] option").each(function (index,ndx){
      singerLst.push(ndx.value);
    });



    $http({
      method:"POST",
      url:baseUrl+"/tracks",
      data:{
        id:$scope.id,
        name:$scope.title,
        artist_id:artistLst,
        singer_id:singerLst,
        music_director:$("[name='music_director']").val(),
        genere:$("[name='genere']").val()
      }
    }).then(function(resp){
      if (resp.data == "Saved"){
        alert("Update");
        $scope.id = 0;
        $scope.title = "";
        $("[name='artist'] option").remove();
        $http({
          method:'post',
          url:baseUrl+"/api/get/Tracks"
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
