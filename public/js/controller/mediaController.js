app.controller("Ctrl",function($scope,$http){
 // Artist Part Start
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
// Artist Part End


// Singer Part Start
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
  // Singer Part End

});
