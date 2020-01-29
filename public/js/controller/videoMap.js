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
            url: baseUrl + "/api/get/virtualmap",
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

        $("[name='txt_sel_tracks']").html("");
        $("[name='txt_sel_artists']").html("");
        $http({
            url: "/api/get/virtualmap/" + id,
            method: "get"

        }).then(function(resp) {

            var data = resp.data;
            $scope.id = data.id;

             //ashok changes 
            $scope.geo =  data.geo;
            if($scope.geo == 1){
                $("[name='featured_geo']").prop("checked", "checked");
            }else{
                $("[name='featured_geo']").removeAttr("checked");
            }


            $("[name='txt_featured']").each(function(index, item) {
                var val = $(item).val();
                if (val == data.featured) {
                    $(item).prop("checked", 1);
                } else {
                    $(item).prop("checked", 0);
                }
            });
            $("[name='txt_title']").val(data.name);
            var tracks = [];
            data.tracks.forEach(function(id) {
                tracks.push(id.track_id);
            });

            var url = "/api/get/virtualmap/" + id + "?tracks=" + tracks.concat();
            $http({
                url: url,
                method: "get"
            }).then(function(respRef) {
                var d = respRef.data;
                //added by ashok kumar
                $("[name='txt_sel_tracks']").html("");
                d.forEach(function(item) {
                    $("[name='txt_sel_tracks']").append("<option value='" + item.id + "'>" + item.name + "</option>");
                });
            }, function(err) {
                printMsg(errResp, "danger")
            });
            url = "/api/get/virtualmap?artists=" + data.artists;
            $http({
                url: url,
                method: "get"
            }).then(function(respRef) {
                var d = respRef.data;
                d.forEach(function(item) {
                    $("[name='txt_sel_artists']").append("<option value='" + item.id + "'>" + item.name + "</option>");
                });
            }, function(err) {
                printMsg(errResp, "danger")
            });




        }, function(errResp) {
            printMsg(errResp, "danger")
        });
    };

    $scope.save = function() {
        var tracks = [];
        var artists = []
        $("[name='txt_sel_tracks'] option").each(function(id, item) {
            tracks.push(item.value);
        });
        $("[name='txt_sel_artists'] option").each(function(id, item) {
            artists.push(item.value);
        });

        var homeChk = $("[name='txt_carousel']:checked").val();

        $http({
            url: baseUrl + "/mapping/video/save" + ($scope.id ? "/" + $scope.id : ""),
            method: "post",
            data: {
                id: ($scope.id ? $scope.id : null),
                name: $("[name='txt_title']").val(),
                ref: tracks,
                artists: artists,
                featured: ($("[name='txt_featured']:checked").val() ? $("[name='txt_featured']:checked").val() : 0),
                geo: $("[name='featured_geo']").prop("checked") ? 1 : 0,  // ashok changes
            }
        }).then(function(resp) {

            if (resp.data.message == "save") {
                var id = resp.data.id;

                if ($scope.file_cover_url) {
                    Upload.upload({
                        url: baseUrl + "/mapping/video/uploader",
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

                Notification.info({ message: "Save" });

                
                if(resp.data.id > 0){
                    $http({
                        method: "post", 
                        url: baseUrl + "/event/create",
                        data: {
                            object_id: resp.data.id,
                            object_type: "featured_videos",
                        }
                    }).then(function(){
                        Notification.info({ message: "Event generated and data has been posted" });
                    }).catch(function(err){
                        // error while generating event
                    })
                }else{
        
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
        $("[name='txt_featured']:checked").removeAttr("checked");
        $("[name='txt_title']").val("");
        $("[name='txt_sel_tracks']").html("");
        $("[name='txt_sel_artists']").html("");
        getData();
    }


});