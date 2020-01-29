app.controller("Ctrl", function($scope, $http, Upload, fileUpload) {
    $scope.banners = [];
    $scope.albumData = [];
    $scope.trackData = [];
    $scope.videoData = [];

    var getData = function() {
        $http({
            url: "/api/get/Banner",
            method: 'GET',
        }).then(function(response) {
            console.log(response);
            $scope.banners = response.data;
        }, function(errResponse) {
            console.log(errResponse);
        });
    };
    getData();
    var getAlbum = function() {
        if ($scope.albumData.length == 0) {
            $http({
                method: 'get',
                url: '/api/get/Movies'
            }).then(function(response) {
                $scope.albumData = response.data
            }, function(errResponse) {
                console.log(errResponse);
            });
        }
    };
    var getTrack = function(id) {

        $http({
            method: 'get',
            url: '/api/get/TrackByMovie/' + id
        }).then(function(response) {
            $scope.trackData = response.data
        }, function(errResponse) {
            console.log(errResponse);
        });

    };


    var getVideo = function(id) {
        var url = "";
        if (id == '1') {
            url = "/api/get/Trailler";
        } else if (id == '2') {
            url = "/api/get/video";
        }
        $http({
            method: 'get',
            url: url
        }).then(function(response) {
            $scope.videoData = response.data
        }, function(errResponse) {
            console.log(errResponse);
        });
    };


    $scope.changeGroup = function() {
        var id = $scope.section;
       /* switch (id) {
            case '1':
                getAlbum();
                break;
            case '2':
                getAlbum();
                break;
            default:
                break;
        }*/
    };

    $scope.changeAlbum = function() {
        var id = $scope.album;
        getTrack(id);
    };

    $scope.changeType = function() {
        var id = $scope.type;
        getVideo(id);
    };

    $scope.new = function() {
        $scope.txt_id = undefined;
        $scope.txt_name = "";
        $scope.file_image_url = "";
        $scope.section = "";
        $scope.txt_track = "";
        $scope.album = "";
        $scope.type = "";
        $scope.txt_video = "";
    };

    $scope.view = function(id) {
        /*
           1. album / Movies
           2. track
           3. video
           4. trailer
       */
        $http({
            url: "/api/get/Banner/" + id,
            method: 'GET',
        }).then(function(response) {
            var type = response.data.type;
            $scope.txt_id = id;
            $scope.section = (type == '4' ? "3" : type);
            switch (type) {
                case 1:
                    $scope.album = response.data.ref_id;
                    break;
                case 2:
                    $scope.txt_track = response.data.ref_id;
                    break;
                case 3:
                    $scope.type = 1;
                    $scope.txt_video = response.data.ref_id;
                    break;
                case 4:
                    $scope.type = 2;
                    $scope.txt_video = response.data.ref_id;
                    break;
                default:
                    break;
            }
            $scope.txt_name = response.data.name;

        }, function(errResponse) {

        });
    };

    $scope.save = function() {
        var section = $scope.section;
        var data = {};
        /*
            1. album / Movies
            2. track
            3. video
            4. trailer
        */
        /*if (section == '1') {
            data.type = 1
            data.ref_id = $scope.album
            if (!$scope.album) {
                alert("Please select album");
                return;
            }
        } else if (section == '2') {
            data.type = 2
            data.ref_id = $scope.txt_track
            if (!$scope.txt_track) {
                alert("Please select track");
                return;
            }
        } else if (section == '3') {
            var type_id = $scope.type;
            if (type_id == '1') {
                data.type = 4
            } else {
                data.type = 3
            }
            data.ref_id = $scope.txt_video
            if (!$scope.txt_video) {
                alert("Please select video");
                return;
            }
        }*/
        if (section) {
            data.id = ($scope.txt_id == undefined ? 0 : $scope.txt_id);
            data.name = $scope.txt_name;
           data.type=1;
	   data.ref_id = 999;
            if ($scope.file_image_url) {
                data.image_url = $scope.file_image_url;
            }
            Upload.upload({
                url: baseUrl + "/banner" + ($scope.txt_id == undefined ? "" : "/" + $scope.txt_id),
                data: data
            }).then(function(data) {
                alert(data.data);
                getData();
            }, function(err) {
                return err;
            }, function(evt) {
                $scope.file_image_url.progress = parseInt(100.0 * evt.loaded / evt.total);
            });
        } else {
            alert("please select any section !");
        }
    };
});
