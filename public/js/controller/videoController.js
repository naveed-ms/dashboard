app.controller("Ctrl", function($scope, $http, Upload, fileUpload, Notification) {

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
    $("[name='movies_loader']").show();
    $http({
        method: 'post',
        url: baseUrl + "/api/get/video"
    }).then(function(response) {
        $scope.data = response.data;
        $("[name='movies_loader']").hide();
    }).then(function(error) {});

    $scope.title = "";
    $scope.type = "";
    $scope.id = 0;

    $scope.new = function() {
        $scope.title = "";
        $scope.post_date = "";
        // $scope.cover_url = "//placehold.it/200";
        $("[name='cover_url']").removeAttr("src");
        $("[name='file_cover_url']").val("");
        $scope.video_cover_url = "";
        $scope.file_mpd_url = "";
        $scope.file_mp4_url = "";
        $scope.video_id = 0;
        $scope.id = 0;
        $("[name='artist']").html("");
        $("[name='btn_save']").removeAttr("disabled");
    };
    $scope.addArtist = function() {
        $("[name='artist_list'] option:selected").each(function(index, ndx) {
            var val = ndx.value;
            var text = ndx.text;
            var option = "<option value='" + val + "'>" + text + "</option>";
            $("[name='artist']").append(option);
            $("[name='artist_list']").find("option[value=" + val + "]").remove();
        });
    };
    //Edit Item
    $scope.editItem = function(id, url) {
        $http({
            method: 'GET',
            url: baseUrl + "/api/get/video/" + id
        }).then(function(response) {
            $scope.title = response.data.name;
            $scope.post_date = new Date(response.data.post_date);
            $scope.id = response.data.id;

            // ashok changes
            $scope.geo = response.data.geo; 

            if($scope.geo == 1){
                $("[name='featured_geo']").prop("checked", "checked");
            }else{
                $("[name='featured_geo']").removeAttr("checked");
            }

            $("[name='txt_feature']").prop("checked", response.data.feature);
            $("[name='artist'] option").remove();
            $scope.cover_url = response.data.cover_url;
            //$("[name='category']").val(response.data.cat[0].id);
            //$("[name='sub_category']").val(response.data.subcat[0].id);
            $http({
                method: 'post',
                url: baseUrl + "/api/get/Artist"
            }).then(function(response) {
                var art_lst = response.data;
                $("[name='artist_list']").html("");
                art_lst.forEach(function(item) {
                    var opt = "<option value='" + item.id + "'>" + item.name + "</option>";
                    $("[name='artist_list']").append(opt);
                    $("[name='artist_list']").find("option[value=" + val + "]").remove();
                });
            }).then(function(error) {});

            for (var i = 0; i < response.data.artists.length; i++) {
                var val = response.data.artists[i].art_id;
                var selOpt = $("[name='artist_list']").find("option[value=" + val + "]")[0];
                if (selOpt != undefined) {
                    var option = "<option value='" + selOpt.value + "'>" + selOpt.text + "</option>";
                    $("[name='artist']").append(option);
                    $("[name='artist_list']").find("option[value=" + val + "]").remove();
                }
            }
            $("form").attr("action", url + "/" + id);
            $http({
                method: 'GET',
                url: baseUrl + "/api/get/video/" + response.data.id
            }).then(function(response) {
                $scope.video_data = response.data;
            }).then(function(error) {

            });
        }).then(function(error) {});
    };
    $scope.addNewArtist = function() {
        $http({
            method: "POST",
            url: baseUrl + "/artist/save",
            data: {
                name: $scope.artist_title
            }
        }).then(function(resp) {
            if (resp.data == "Saved") {
                // $scope.new();
                $scope.artist_title = '';
                alert("Update");
                $http({
                    method: 'post',
                    url: baseUrl + "/api/get/Artist"
                }).then(function(response) {
                    var art_lst = response.data;
                    $("[name='artist_list']").html("");
                    art_lst.forEach(function(item) {
                        var opt = "<option value='" + item.id + "'>" + item.name + "</option>";
                        $("[name='artist_list']").append(opt);
                    });
                }).then(function(error) {});
            }
        }, function() {
            alert("An error while updating artist record");
        });
    };
    //Remove Artist
    $scope.removeArtist = function() {
        $("[name='artist'] option:selected").each(function(index, ndx) {
            var val = ndx.value;
            var text = ndx.text;
            var option = "<option value='" + val + "'>" + text + "</option>";
            $("[name='artist_list']").append(option);
            $("[name='artist']").find("option[value=" + val + "]").remove();
        });
        //$("[name='artist'] option:selected").remove();
    };
    $scope.save = function() {
        data = [];
        if (!$scope.title) {
            data.push({
                "Title": "Please enter title"
            });
        }
        if (!$scope.post_date) {
            data.push({
                "Post_Date": "Please enter publish date"
            });
        }

        if (data.length > 0) {
            printMsg({
                status: 422,
                data: data
            }, "danger");
            return false;
        }

        var artistLst = [];
        $("[name='artist'] option").each(function(index, ndx) {
            artistLst.push(ndx.value);
        });
        $http({
            method: "POST",
            url: baseUrl + "/video/save",
            data: {
                id: $scope.id,
                name: $scope.title,
                post_date: $scope.post_date,
                type: $scope.txt_type,
                video_title: $scope.video_title,
                video_id: $scope.video_id,
                featured: ($("[name='txt_feature']").prop("checked") ? 1 : 0),
                featured_geo: $("[name='featured_geo']").prop("checked") ? 1 : 0, // ashok changes
                artists: artistLst
            }
        }).then(function(resp) {
            if (resp.data.message == "save") {
                // $scope.new();
                if ($scope.file_cover_url) {
                    Upload.upload({
                        url: baseUrl + "/video/uploader",
                        data: {
                            "movie_id": resp.data.video_id,
                            "type": $scope.txt_type,
                            "cover_url": $scope.file_cover_url
                        }
                    }).then(function(data) { return data; }, function(err) { return err; }, function(evt) { $scope.file_cover_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                }


                if ($scope.file_mp4_url) {
                    Upload.upload({
                        url: baseUrl + "/video/uploader",
                        data: {
                            "movie_id": resp.data.video_id,
                            "type": $scope.txt_type,
                            "mp4_url": $scope.file_mp4_url
                        }
                    }).then(function(data) { $scope.file_mp4_url.result = data; }, function(err) { alert("error occured while uploading"); }, function(evt) { $scope.file_mp4_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                }

                if ($scope.file_mpd_url) {
                    Upload.upload({
                        url: "/video/uploader",
                        data: {
                            "video_id": resp.data.video_id,
                            "type": $scope.txt_type,
                            "mpd_url": $scope.file_mpd_url
                        }
                    }).then(function(data) {
                            $scope.file_mpd_url.result = data.message;
                            var idData = data;
                            (function poll() {
                                setTimeout(function() {
                                    console.log("polling start");
                                    $.ajax({
                                        url: "/api/endcoder/video/" + idData.data.movie_id + "/" + idData.data.job_id,
                                        success: function(data) {
                                            $scope.file_mpd_url.result = data;
                                            $("[name='mpd-status']").text(data);
                                            console.log(data);
                                            if (data == "In Process ....") {
                                                console.log("polling end");
                                                poll();
                                            } else {
                                                $("[name='mpd-prog']").hide();
                                            }
                                        }
                                    });
                                }, 30000);
                            })();
                        },
                        function(err) { alert("error occured while uploading"); },
                        function(evt) { $scope.file_mpd_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                }
                printMsg(resp, "info");

                $http({
                    method: 'post',
                    url: baseUrl + "/api/get/video"
                }).then(function(response) {
                    $scope.data = response.data;
                }).then(function(error) {});

                if($scope.id > 0){
                    $http({
                        method: "post", 
                        url: baseUrl + "/event/create",
                        data: {
                            object_id: $scope.id,
                            object_type: "video",
                        }
                    }).then(function(){
                        Notification.info({ message: "Event generated and data has been posted" });
                    }).catch(function(err){
                        // error while generating event
                    })
                }else{
        
                }



            }
        }, function(resp) {
            printMsg(resp, "danger");
        });
        $("[name='btn_save']").attr("disabled", true);
    };
    $(document).ready(function() {
        console.log(new Date());
        document.getElementsByName('post_date')[0].valueAsDate = new Date();
    });
});