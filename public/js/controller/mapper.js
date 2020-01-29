app.controller("Ctrl", function($scope, $http, $location, fileUpload, Upload, Notification) {
    var printMsg = function(errResp, alertType) {
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
        Notification.info({ message: $scope.err_message });
        // setTimeout(function(){
        //   $("[ng-bind-html='err_message']").html("");
        // }, 3000);
    };

    var getData = function() {
        $http({
            url: baseUrl + "/api/get/Playlist",
            method: "get"
        }).then(function(resp) {
            $scope.data = resp.data;
        }, function(error) {
            printMsg(error, "danger")
        });
    };
    getData();

    $scope.getTracks = function() {
        var id = $("txt_album").val();
        $http({
            url: baseUrl + "/api/get/TrackByMovie/" + $scope.txt_album,
            method: "get"
        }).then(function(resp) {
            $("[name='txt_tracks']").html("");
            resp.data.forEach(function(item) {
                $("[name='txt_tracks']").append("<option value='" + item.id + "'>" + item.name + "</option>");
            });
        }, function(error) { printMsg(error, "danger") });
    };
    $scope.view = function(id) {
        $http({
            url: "/api/get/Playlist/" + id,
            method: "get"
        }).then(function(resp) {
            var data = resp.data;
            $scope.id = data.id;

             //ashok changes
            $scope.geo = data.geo;
            if($scope.geo == 1){
                $("[name='topvideos_geo']").prop("checked", "checked");
            }else{
                $("[name='topvideos_geo']").removeAttr("checked");
            }

            $("[name='txt_title']").val(data.name);
            $("[name='txt_sel_tracks']").html("");
            if (data.tracks.length > 0) {
                data.tracks.forEach(function(item) {
                    $("[name='txt_sel_tracks']").append("<option value='" + item.id + "'>" + item.name + "</option>");
                });
            }
            $("[name='txt_sel_artists']").html("");
            if (data.arists.length > 0) {
                data.arists.forEach(function(item) {
                    $("[name='txt_sel_artists']").append("<option value='" + item.id + "'>" + item.name + "</option>");
                });
            }
            if (data.cat){
                id = 0
                if (data.cat.id == 57){
                    id = 1;
                }else if(data.cat.id == 62 || data.cat.id == 32){
                    id = 2;
                }else if(data.cat.id == 62){
                    id = 3;
                }
                $("[name='txt_carousel']").val(id);
            }
        }, function(errResp) {
            printMsg(errResp, "danger")
        });
    };

    $scope.save = function() {
        var tracks = [];
        var artists = []
        if ($("[name='txt_title']").val() == ""){
            Notification.info({ message: "Please enter name" });
            return true;
        }
        $("[name='txt_sel_tracks'] option").each(function(id, item) {
            tracks.push(item.value);
        });
        $("[name='txt_sel_artists'] option").each(function(id, item) {
            artists.push(item.value);
        });

        var homeChk = $("[name='txt_carousel']").val();

        $http({
            url: baseUrl + "/mapping/save" + ($scope.id ? "/" + $scope.id : ""),
            method: "post",
            data: {
                id: ($scope.id ? $scope.id : null),
                name: $("[name='txt_title']").val(),
                geo: $("[name='topvideos_geo']").prop("checked") ? 1 : 0, //ashok changes
                ref: tracks,
                home: (homeChk != undefined ? homeChk : 0),
                artists: artists
            }
        }).then(function(resp) {
            if (resp.data.message == "save") {

                var id = resp.data.id;
                if ($scope.file_cover_url) {
                    Upload.upload({
                        url: baseUrl + "/mapping/uploader",
                        data: {
                            id: id,
                            cover_url: $scope.file_cover_url
                        }
                    }).then(function(data) {
                        Notification.info({ message: "Uploaded" });
                    }, function(err) {
                        Notification.info({ message: "An error occurd while uploading image" });
                    }, function(evt) {
                        $scope.file_cover_url.progress = parseInt(100.0 * evt.loaded / evt.total);
                    });
                }
               
                Notification.info({ message: "Saved" });

                if(id > 0)
                {
                    $http({
                        method: "post", 
                        url: baseUrl + "/event/create",
                        data: {
                            object_id: id,
                            object_type: "album",
                        }
                    }).then(function(){
                        Notification.info({ message: "Event generated and data has been posted" });
                    }).catch(function(err){
                        // error while generating event
                    })
                }

           

            }
        }, function(error) {
            printMsg(error, "danger")
        });

    };

    $scope.add = function() {
        $("[name='txt_tracks'] option:selected").each(function(id, row) {
            $("[name='txt_sel_tracks']").append("<option value='" + row.value + "'>" + row.text + "</option>");
        });
    };
    $scope.remove = function() {
        $("[name='txt_sel_tracks'] option:selected").each(function(index, ndx) {
            var val = ndx.value;
            var text = ndx.text;
            $("[name='txt_sel_tracks']").find("option[value=" + val + "]").remove();
        });
    };

    $scope.artistAdd = function() {
        $("[name='txt_artists'] option:selected").each(function(id, row) {
            $("[name='txt_sel_artists']").append("<option value='" + row.value + "'>" + row.text + "</option>");
        });
    };
    $scope.artistRemove = function() {
        $("[name='txt_sel_artists'] option:selected").each(function(index, ndx) {
            var val = ndx.value;
            var text = ndx.text;
            $("[name='txt_sel_artists']").find("option[value=" + val + "]").remove();
        });
    };

    $scope.new = function() {
        $scope.id = null;
        $("[name='img_cover_url']").removeProp("src");
        $("[name='txt_carousel']").val("0");
        $("[name='txt_title']").val("");
        $("[name='txt_sel_tracks']").html("");
        $("[name='txt_sel_artists']").html("");
        getData();
    }


});