app.controller("Ctrl", function($scope, $http, fileUpload, Upload, Notification) {
    $http({
        method: 'post',
        url: baseUrl + "/api/get/Artist"
    }).then(function(response) {
        $scope.data = response.data;
    }).then(function(error) {});


    $scope.new = function() {
        $scope.title = "";
        $scope.id = 0;
        $scope.cover_url = "//placehold.it/200";
    };

    $scope.editItem = function(id, url) {
        $http({
            method: 'GET',
            url: baseUrl + "/api/get/Artist/" + id
        }).then(function(response) {
            $scope.title = response.data.name;
            $scope.id = response.data.id;
            $scope.cover_url = response.data.cover_url;
            $("[name='gender']").val(response.data.gender);
            $("form").attr("action", url + id);
        }).then(function(error) {});
    };

    $scope.save = function() {
        Upload.upload({
            url: baseUrl + "/artist/save",
            data: {
                id: $scope.id,
                name: $scope.title,
                gender: $("[name='gender']").val(),
                cover_url: $scope.file_cover_url
            }
        }).then(function(resp) {
            if (resp.data == "Saved") {

                if($scope.id > 0){
                    $http({
                        method: "post", 
                        url: baseUrl + "/event/create",
                        data: {
                            object_id: $scope.id,
                            object_type: "artists",
                        }
                    }).then(function(){
                        Notification.info({ message: "Event generated and data has been posted" });
                    }).catch(function(err){
                        // error while generating event
                    })
                }else{
        
                }
                
                $scope.title = "";
                alert("Update");
                $scope.id = 0;
                $http({
                    method: 'post',
                    url: baseUrl + "/api/get/Artist"
                }).then(function(response) {
                    $scope.data = response.data;
                }).then(function(error) {});
            }
        }, function(err) { alert("An Error occurd !"); }, function(evt) { $scope.file_cover_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
    };

});