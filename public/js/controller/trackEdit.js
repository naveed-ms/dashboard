app.controller("Ctrl", function($scope, $http, $location, fileUpload, Upload, Notification) {
    var url = $location.absUrl().split("?");
    var id = $location.absUrl().split("?")[0].split("/");
    var track_id = 0;
    $scope.new = function() {
        var url = $(this).attr("href");
        window.location = url;
    }
    var getData = function(track_id = 0) {
        if (id[id.length - 2] == "addNew") {
            $scope.txt_title = "";
        } else {
            $http({
                method: 'GET',
                url: baseUrl + "/api/get/Tracks/" + (id[id.length - 2] == "addNew" ? track_id : id[id.length - 1])
            }).then(function(response) {

                console.log(response.data);
                $scope.txt_title = response.data.track.name;
                $scope.id = response.data.track.id;
                $scope.cover_url = response.data.track.cover_url;


                //ashok changes
                $scope.geo = response.data.track.geo;
                //ashok changes

                // ArtistId,Artist
                $("[name='artist'] option").remove();
                $("[name='singer'] option").remove();
                for (var i = 0; i < response.data.artists.length; i++) {
                    var val = response.data.artists[i].id;
                    var text = response.data.artists[i].name;
                    var option = "<option value='" + val + "'>" + text + "</option>";
                    $("[name='artist']").append(option);
                    $("[name='artist_list']").find("option[value=" + val + "]").remove();
                }
                for (var i = 0; i < response.data.singers.length; i++) {
                    var val = response.data.singers[i].id;
                    var text = response.data.singers[i].name;
                    var option = "<option value='" + val + "'>" + text + "</option>";
                    $("[name='singer']").append(option);
                    $("[name='singer_list']").find("option[value=" + val + "]").remove();
                }
                $("[name='music_director']").val(response.data.track.director_id);
                $("[name='genere']").val(response.data.track.genere);
                $("[name='lst_movie']").val(response.data.track.movie_id);
                $("form").attr("action", url + id);

                //ashok changes
                if($scope.geo == 1){
                    $("[name='video_geo']").prop("checked", "checked");
                }else{
                    $("[name='video_geo']").removeAttr("checked");
                }


            }).then(function(error) {});
        }

    };
    var urls = $location.absUrl().split("/");
    if (urls[urls.length - 1] !== "addNew") {
        getData();
    }
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

    $scope.addArtist = function() {
        $("[name='artist_list'] option:selected").each(function(index, ndx) {
            var val = ndx.value;
            var text = ndx.text;
            var option = "<option value='" + val + "'>" + text + "</option>";
            $("[name='artist']").append(option);
            $("[name='artist_list']").find("option[value=" + val + "]").remove();
        });
    };

    $scope.removeArtist = function() {
        $("[name='artist'] option:selected").each(function(index, ndx) {
            var val = ndx.value;
            var text = ndx.text;
            var option = "<option value='" + val + "'>" + text + "</option>";
            $("[name='artist_list']").append(option);
            $("[name='artist']").find("option[value=" + val + "]").remove();
        });
    };


    $scope.addSinger = function() {
        $("[name='singer_list'] option:selected").each(function(index, ndx) {
            var val = ndx.value;
            var text = ndx.text;
            var option = "<option value='" + val + "'>" + text + "</option>";
            $("[name='singer']").append(option);
            $("[name='singer_list']").find("option[value=" + val + "]").remove();
        });
    };

    $scope.removeSinger = function() {
        $("[name='singer'] option:selected").each(function(index, ndx) {
            var val = ndx.value;
            var text = ndx.text;
            var option = "<option value='" + val + "'>" + text + "</option>";
            $("[name='singer_list']").append(option);
            $("[name='singer']").find("option[value=" + val + "]").remove();
        });
    };


    $scope.save = function() {
        if (!$scope.txt_title) {
            printMsg({
                status: 422,
                data: [
                    { "Title": "Please enter title" }
                ]
            }, "danger");
            return false;
        }
        var artistLst = [];
        var singerLst = [];
        var cover_upUrl = baseUrl + "/tracks/uploader";

        $("[name='artist'] option").each(function(index, ndx) {
            artistLst.push(ndx.value);
        });

        $("[name='singer'] option").each(function(index, ndx) {
            singerLst.push(ndx.value);
        });

        var url = $location.absUrl().split("/");
        var mid = $("[name='lst_movie']").val();
        if (url[url.length - 2] == "addNew") {
            $http({
                method: "POST",
                url: baseUrl + "/tracks/addNew",
                data: {
                    name: $scope.txt_title,
                    artist_id: artistLst,
                    singer_id: singerLst,
                    movie: mid,
                    music_director: $("[name='music_director']").val(),
                    genere: $("[name='genere']").val(),
                    geo: $("[name='video_geo']").prop("checked") ? 1 : 0,  //ashok changes
                }
            }).then(function(resp) {
                track_id = resp.data.track_id
                if (resp.data.message == "save") {
                    $scope.txt_title = "";
                    if ($scope.file_cover_url) {
                        Upload.upload({
                            url: cover_upUrl,
                            data: {
                                "track_id": resp.data.track_id,
                                "movie_id": $("[name='lst_movie']").val(),
                                "cover_url": $scope.file_cover_url
                            }
                        }).then(function(data) { $scope.file_cover_url.result = data; }, function(err) { alert("error occured while uploading"); }, function(evt) { $scope.file_cover_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                    }
                    if ($scope.file_mp3_url) {

                        Upload.upload({
                            url: cover_upUrl,
                            data: {
                                "track_id": resp.data.track_id,
                                "movie_id": $("[name='lst_movie']").val(),
                                "audio_url": $scope.file_mp3_url
                            }
                        }).then(function(data) { $scope.file_mp3_url.result = data; }, function(err) { alert("error occured while uploading"); }, function(evt) { $scope.file_mp3_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                    }
                    if ($scope.file_mp4_url) {
                        Upload.upload({
                            url: cover_upUrl,
                            data: {
                                "track_id": resp.data.track_id,
                                "movie_id": $("[name='lst_movie']").val(),
                                "mp4_url": $scope.file_mp4_url
                            }
                        }).then(function(data) { $scope.file_mp4_url.result = data; }, function(err) { alert("error occured while uploading"); }, function(evt) { $scope.file_mp4_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                    }
                    if ($scope.file_mpd_url) {
                        Upload.upload({
                            url: cover_upUrl,
                            data: {
                                "track_id": resp.data.track_id,
                                "movie_id": $("[name='lst_movie']").val(),
                                "mpd_url": $scope.file_mpd_url
                            }
                        }).then(function(data) {
                                $scope.file_mpd_url.result = data;
                                var idData = data;
                                (function poll() {
                                    setTimeout(function() {
                                        console.log("polling start");
                                        $.ajax({
                                            url: "/api/endcoder/" + idData.data.track_id + "/" + idData.data.job_id,
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
                                            },
                                            error: function(err) {
                                                if (err.status == 500) {
                                                    printMsg({
                                                        status: 422,
                                                        data: [
                                                            { "Server Error": err.statusText }
                                                        ]
                                                    }, "danger");
                                                    poll();
                                                }
                                            }
                                        });
                                    }, 30000);
                                })();
                            },
                            function(err) { alert("error occured while uploading"); },
                            function(evt) { $scope.file_mpd_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                    }

                    movieid = $("[name='lst_movie']").val();
            
                    if(movieid > 0){
                        //trailer event
                        $http({
                            method: "post", 
                            url: baseUrl + "/event/create",
                            data: {
                                object_id: movieid,
                                object_type: "album",
                            }
                        }).then(function(){
                            Notification.info({ message: "Event generated and data has been posted" });
                        }).catch(function(err){
                            // error while generating event
                        })
                    }


                    // $scope.id = 0;
                    // $scope.title = "";
                    // $("[name='artist'] option").remove();
                    // $("[name='singer'] option").remove();
                    printMsg(resp, "info");
                    // getData();
                    $("[name='btn_save']").attr("disabled", true);
                }
            }, function(errResp) {
                $("[name='btn_save']").attr("disabled", false);
                printMsg(errResp, 'danger');
            });

        } else {

            $http({
                method: "POST",
                url: baseUrl + "/tracks/edit/" + id[id.length - 1],
                data: {
                    id: id[id.length - 1],
                    name: $scope.txt_title,
                    artist_id: artistLst,
                    singer_id: singerLst,
                    movie: mid,
                    music_director: $("[name='music_director']").val(),
                    genere: $("[name='genere']").val(),
                    geo: $("[name='video_geo']").prop("checked") ? 1 : 0,  //ashok changes
                }
            }).then(function(resp) {
                if (resp.data.message == "save") {
                    $scope.artist_title = "";
                    if ($scope.file_cover_url) {
                        Upload.upload({
                            url: cover_upUrl,
                            data: {
                                "track_id": resp.data.track_id,
                                "movie_id": $("[name='lst_movie']").val(),
                                "cover_url": $scope.file_cover_url
                            }
                        }).then(function(data) { $scope.file_cover_url.result = data; }, function(err) { alert("error occured while uploading"); }, function(evt) { $scope.file_cover_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                    }
                    if ($scope.file_mp3_url) {
                        Upload.upload({
                            url: cover_upUrl,
                            data: {
                                "track_id": resp.data.track_id,
                                "movie_id": $("[name='lst_movie']").val(),
                                "audio_url": $scope.file_mp3_url
                            }
                        }).then(function(data) { $scope.file_mp3_url.result = data; }, function(err) { alert("error occured while uploading"); }, function(evt) { $scope.file_mp3_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                    }
                    if ($scope.file_mp4_url) {
                        Upload.upload({
                            url: cover_upUrl,
                            data: {
                                "track_id": resp.data.track_id,
                                "movie_id": $("[name='lst_movie']").val(),
                                "mp4_url": $scope.file_mp4_url
                            }
                        }).then(function(data) { $scope.file_mp4_url.result = data; }, function(err) { alert("error occured while uploading"); }, function(evt) { $scope.file_mp4_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                    }
                    if ($scope.file_mpd_url) {
                        Upload.upload({
                            url: cover_upUrl,
                            data: {
                                "track_id": resp.data.track_id,
                                "movie_id": $("[name='lst_movie']").val(),
                                "mpd_url": $scope.file_mpd_url
                            }
                        }).then(function(data) {
                                $scope.file_mpd_url.result = data;
                                var idData = data;
                                (function poll() {
                                    setTimeout(function() {
                                        console.log("polling start");
                                        $.ajax({
                                            url: "/api/endcoder/" + idData.data.track_id + "/" + idData.data.job_id,
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


                    movieid = $("[name='lst_movie']").val();
            
                    if(movieid > 0){
                        //trailer event
                        $http({
                            method: "post", 
                            url: baseUrl + "/event/create",
                            data: {
                                object_id: movieid,
                                object_type: "album",
                            }
                        }).then(function(){
                            Notification.info({ message: "Event generated and data has been posted" });
                        }).catch(function(err){
                            // error while generating event
                        })
                    }



                    $scope.id = 0;
                    $scope.title = "";
                    $("[name='artist'] option").remove();
                    $("[name='singer'] option").remove();
                    printMsg(resp, "info");
                    getData((id[id.length - 2] == "addNew" ? track_id : 0));
                }
                $("[name='btn_save']").attr("disabled", true);
            }, function(errResp) {
                $("[name='btn_save']").attr("disabled", false);
                printMsg(errResp, 'danger');
            });
        }
        $("[name='btn_save']").attr("disabled", true);
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
                $scope.artist_title = "";
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
    $scope.addNewSinger = function() {
        $http({
            method: "POST",
            url: baseUrl + "/singer/save",
            data: {
                name: $scope.singer_title,
            }
        }).then(function(resp) {
            if (resp.data == "Saved") {
                $scope.singer_title = "";
                alert("Update");
                $http({
                    method: 'post',
                    url: baseUrl + "/api/get/Singer"
                }).then(function(response) {
                    var sing_lst = response.data;
                    $("[name='singer_list']").html("");
                    sing_lst.forEach(function(item) {
                        var opt = "<option value='" + item.id + "'>" + item.name + "</option>";
                        $("[name='singer_list']").append(opt);
                    });
                }).then(function(error) {});
            }
        }, function() {
            alert("An error while updating record");
        });
    };

});