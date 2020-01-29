app.controller("Ctrl", function ($scope, $http, $location, fileUpload) {
    var getData = function () {
        // $http({
        //     method: 'GET',
        //     url: baseUrl + "/api/get/trailer/" + id[id.length - 1]
        // }).then(function (response) {
        //     console.log(response.data);
        //     $scope.id = response.data.trailer.id;
        //     $scope.cover_url = response.data.trailer.cover_url;
        //     $("[name='lst_movie']").val(response.data.trailer.movie_id);
        //     $("form").attr("action", url + id);
        // }).then(function (error) {
        // });
    };

    var printMsg = function (errResp, alertType) {
        $scope.err_message = '<div class="row"><div class="col-lg-12"><div class="alert alert-' + alertType + '">';
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
                $scope.err_message += errResp.data;
                break;
            default:
                $scope.err_message += errResp.data;
                break;
        }
        $scope.err_message += '</div></div></div>';
        // setTimeout(function(){
        //   $("[ng-bind-html='err_message']").html("");
        // }, 3000);
    };


    $scope.save = function () {
      var cover_upUrl = baseUrl + "/trailer/uploader";
      if ($scope.file_cover_url){
          fileUpload.uploadFileToUrl($scope.file_cover_url, cover_upUrl, 'cover_url');
      }
      $http({
        method: 'post',
        url : baseUrl + '/trailer/save',
        data : {
          movie_id: $("[name='lst_movie']").val()
        }
      }).then(function(response){

        printMsg(response);
      },function(){

      });
    };

});
