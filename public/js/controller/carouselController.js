app.controller("Ctrl",function($scope,$http){
  $scope.carousels = [];
  var printMsg = function (errResp, alertType) {
      $scope.err_message = '<div class="row"><div class="col-lg-12"><div>';
      switch (errResp.status) {
          case 422:
              for (msg in errResp.data) {
                  $scope.err_message += '<ul>';
                  for (err in errResp.data[msg]) {
                      $scope.err_message += '<li>' + errResp.data[msg][err] + '</li>';
                  }
                  $scope.err_message += '</ul>';
              }
              break;
          case 200:
              $scope.err_message += errResp.data.message;
              break;
          default:
              $scope.err_message += errResp.data;
              break;
      }
      $scope.err_message += '</div></div></div>';
      var bar = new $.peekABar();
      bar.show({
        html:$scope.err_message,
        position: 'bottom',
        autohide: true
      });
      // setTimeout(function(){
      //   $("[ng-bind-html='err_message']").html("");
      // }, 3000);
  };
  var getData = function(){
    $http({
      url: "/api/get/Carousel",
      method: "get",
    }).then(function(response){
      $scope.carousels = response.data;
    }, function(responseErr){

    });
  };
  getData();
  $scope.add = function(){
    // $("carousel_table")
    var album = $("[name='album'] option:selected");
    var category = $("[name='category'] option:selected");
    $scope.carousels.push({
      title: album.text(),
      carousel: category.text(),
      m_order: 0,
      a_id: album.val(),
      c_id: category.val()
    });
  };

  $scope.delete = function(a_id,c_id){
    $scope.carousels.forEach(function(item,index){
        if (a_id == item.a_id && c_id == item.c_id){
          $scope.carousels.splice(index,1);
        }
    });
  };

  $scope.update = function(){
    ids = [];
    $scope.carousels.forEach(function(item,index){
      ids.push({
        a_id: item.a_id,
        c_id: item.c_id,
        m_order: item.m_order
      });
    });
    $http({
      url: '/carousel/update',
      method: 'post',
      data: {
        data: ids
      }
    }).then(function(response){
      printMsg(response,"info");
    },function(response){
      printMsg(response,"danger");
    });
  };
});
