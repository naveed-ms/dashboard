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
//    $("[name='movies_loader']").show();
$scope.data = [];
//var getData = function($http, url){
$("[name='movies_loader']").show();
    $http({
        method: 'post',
        url: baseUrl + "/api/get/Movies"
    }).then(function(response) {
response.data.forEach(function(item){ 
$scope.data.push(item); 
});
       
//if (response.data.next_page_url != null){
//getData($http, response.data.next_page_url);
//}
//        $("[name='movies_loader']").hide();
    }).then(function(error) {});
//$("[name='movies_loader']").hide();
//};
//getData($http, baseUrl + "/api/get/Movies");
$("[name='movies_loader']").hide();
//    $http({
  //      method: 'post',
    //    url: baseUrl + "/api/get/Movies"
    //}).then(function(response) {
//console.log(response.data);
      //  $scope.data.push(response.data.data);

       // $("[name='movies_loader']").hide();
    //}).then(function(error) {});

    $scope.title = "";
    $scope.type = "";
    $scope.id = 0;

    $scope.new = function() {
        $scope.title = "";
        $scope.post_date = "";
        $("[name='category'] option[value='']").prop('selected', true);
        $("[name='sub_category'] option[value='']").prop('selected', true);
        $scope.subcat = "";
        $("[name='cover_url']").removeAttr("src");
        $("[name='file_cover_url']").val("");
        $scope.trailer_title = "";
        $scope.trailer_cover_url = "";
        $scope.file_mpd_url = "";
        $scope.file_mp4_url = "";
        $scope.trailer_id = 0;
        $scope.id = 0;
        $("[name='artist']").html("");
        $("[name='txt_carousel']").removeAttr("checked");
        $("[name='btn_save']").removeAttr("disabled");
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
    };


    $scope.newTrailer = function() {
        $scope.trailer_title = "";
        $scope.trailer_cover_url = "";
        $scope.file_mpd_url = "";
        $scope.file_mp4_url = "";
        $scope.trailer_id = 0;
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


    $scope.musicdirector_changed = function() {
        window.location = baseUrl + "/tracks/" + $scope.musicdirector_id;
    };
    $scope.editTrailer = function(id) {
        $http({
            method: 'GET',
            url: baseUrl + "/api/get/Trailler/" + $scope.id + "/" + id
        }).then(function(response) {
            $scope.trailer_id = response.data.id;
            $scope.trailer_title = response.data.name;
            $scope.trailer_geo = response.data.trailer_geo;
            if($scope.trailer_geo == 1){
                $("[name='trailer_txt_geo']").prop("checked", "checked");
            }else{
                $("[name='trailer_txt_geo']").removeAttr("checked");
            }
            $("[name='txt_carousel']").each(function(index, item) { if (item.value == 15) { item.checked = (response.data.feature == 1 ? true : false); } });
        }).then(function(error) {

        });
    };
    $scope.editItem = function(id, url) {
        $http({
            method: 'GET',
            url: baseUrl + "/api/get/Movies/" + id
        }).then(function(response) {

            $scope.title = response.data.name;
            $scope.post_date = new Date(response.data.post_date);
            $scope.id = response.data.id;
            $scope.cover_url = response.data.cover_url;
            $("[name='txt_carousel']").removeAttr("checked");
            response.data.cat.forEach(function(i, item) {
                $("[name='txt_carousel']").each(function(i, carousel) {
                    if (carousel.value == response.data.cat[item].id && carousel.value != 15) {
                        $(carousel).prop("checked", "checked");
                    }
                });
            });
            $scope.cat = response.data.cat[0].id;
            $("[name='category']").val(response.data.cat[0].id);
            if (response.data.subcat.length > 0) {
                $scope.subcat = response.data.subcat[0].id;
                $("[name='sub_category']").val(response.data.subcat[0].id);
            }
            $("[name='artist'] option").remove();
            for (var i = 0; i < response.data.artists.length; i++) {
                var val = response.data.artists[i].id;
                var text = response.data.artists[i].name;
                var option = "<option value='" + val + "'>" + text + "</option>";
                $("[name='artist']").append(option);
                $("[name='artist_list']").find("option[value=" + val + "]").remove();
            }

            //my changed code 
            $scope.label = response.data.label;
            if(response.data.geo == 1){
                $("[name='txt_geo']").prop("checked", "checked");
            }else{
                $("[name='txt_geo']").removeAttr("checked");
            }


            $("form").attr("action", url + "/" + id);
            $http({
                method: 'GET',
                url: baseUrl + "/api/get/Trailler/" + response.data.id
            }).then(function(response) {
                $scope.trailer_data = response.data;
            }).then(function(error) {

            });
        }).then(function(error) {});
    };

    //ashok kumar changes
     $scope.delete = function(id){

        
        if(confirm('Are You Sure to Delete'))
        {
            $http({
                method: "get", 
                url: baseUrl + "/delMovie/"+id,
                data: {
                    id: id
                }
            }).then(function(resp){

                if(resp.data.message == 'deleted'){
                
                    Notification.info({ message: "Deleted Successfully" });
                    $('.row'+id).fadeOut(1000);
                    
                }
                else{
                    Notification.info({ message: "Error In deleteion" });
                }

        
            }).catch(function(err){
                // error while generating event
            })
            

        }
       
    };

    $scope.save = function() {
        errArr = [];
        if (!$scope.title) {
            Notification.info({ message: "Please enter title" });
        }
        if (!$scope.post_date) {
            Notification.info({ message: "Please enter publish date" });
        }
        if (!$("[name='category']").val()) {
            Notification.info({ message: "Please Select Movie Category" });
        }
        var artistLst = [];
        $("[name='artist'] option").each(function(index, ndx) {
            artistLst.push(ndx.value);
        });
        var carouselLst = [];
        $("[name='txt_carousel']:checked").each(function(i, item) {
            var str = $(item).val();
            carouselLst.push(str);
        });
        $http({
            method: "POST",
            url: baseUrl + "/movie/save",
            data: {
                id: $scope.id,
                name: $scope.title,
                post_date: $scope.post_date,
                cat: $scope.cat,
                subcat: $scope.subcat,
                carousel: carouselLst,
                trailer_title: $scope.trailer_title,
                trailer_id: $scope.trailer_id,
                artists: artistLst,
                label: $scope.label,
                geo: $("[name='txt_geo']").prop("checked") ? 1 : 0,
                trailer_geo: $("[name='trailer_txt_geo']").prop("checked") ? 1 : 0
            }
        }).then(function(resp) {
            if (resp.data.message == "save") {
                // $scope.new();
                if ($scope.file_cover_url) {
                    Upload.upload({
                        url: baseUrl + "/movie/uploader",
                        data: {
                            "movie_id": resp.data.movie_id,
                            "cover_url": $scope.file_cover_url
                        }
                    }).then(function(data) { return data; }, function(err) { return err; }, function(evt) { $scope.file_cover_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                }

                // if ($scope.trailer_cover_url) {
                //   Upload.upload({
                //     url: baseUrl + "/movie/uploader",
                //     data: {
                //       "movie_id": resp.data.movie_id,
                //       "trailer_id": resp.data.trailer_id,
                //       "trailer_cover_url": $scope.trailer_cover_url
                //     }
                //   }).then(function (data) { return data; }, function (err) { return err; }, function (evt) { $scope.file_cover_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                // }

                if ($scope.file_mp4_url) {
                    $scope.file_mp4_url.progress = 1;
                    Upload.upload({
                        url: baseUrl + "/movie/uploader",
                        data: {
                            "movie_id": resp.data.movie_id,
                            "trailer_id": resp.data.trailer_id,
                            "mp4_url": $scope.file_mp4_url
                        }
                    }).then(function(data) { $scope.file_mp4_url.result = data; }, function(err) { alert("error occured while uploading"); }, function(evt) { $scope.file_mp4_url.progress = parseInt(100.0 * evt.loaded / evt.total); });
                }

                if ($scope.file_mpd_url) {
                    $scope.file_mpd_url.progress = 1;
                    Upload.upload({
                        url: "/movie/uploader",
                        data: {
                            "movie_id": resp.data.movie_id,
                            "trailer_id": resp.data.trailer_id,
                            "mpd_url": $scope.file_mpd_url
                        }
                    }).then(function(data) {
                            $scope.file_mpd_url.result = data.message;
                            var idData = data;
                            (function poll() {
                                setTimeout(function() {
                                    console.log("polling start");
                                    $.ajax({
                                        url: "/api/endcoder/trailer/" + idData.data.trailer_id + "/" + idData.data.job_id,
                                        success: function(data) {
                                            $scope.file_mpd_url.result = data;
                                            $("[name='mpd-status']").text(" | " + data);
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

                 if(resp.data.movie_id > 0){
                    $http({
                        method: "post", 
                        url: baseUrl + "/event/create",
                        data: {
                            object_id: resp.data.movie_id,
                            object_type: "album",
                        }
                    }).then(function(){
        
                        if(resp.data.trailer_id > 0){
                        //trailer event
                        $http({
                            method: "post", 
                            url: baseUrl + "/event/create",
                            data: {
                                object_id: resp.data.trailer_id,
                                object_type: "trailer",
                            }
                        }).then(function(){
                            Notification.info({ message: "Event generated and data has been posted" });
                        }).catch(function(err){
                            // error while generating event
                        })
                        }
                        else{
        
                            Notification.info({ message: "Event generated and data has been posted" });
                        }
        
                
                    }).catch(function(err){
                        // error while generating event
                    })
                }

                printMsg(resp, "info");
                // $http({
                //     method: 'post',
                //     url: baseUrl + "/api/get/Movies"
                // }).then(function(response) {
                //     $scope.data = response.data;
                // }).then(function(error) {});
                // $scope.id = 0;
                // $scope.title = "";
                // $scope.post_date = "";
                // $scope.cat = "";
                // $scope.subcat = "";
                // $scope.cover_url = "//placehold.it/200";
                // $scope.trailer_title = "";
                // $scope.trailer_cover_url = "";
                // $scope.file_mpd_url = "";
                // $scope.file_mp4_url = "";
                // $scope.trailer_id = 0;
                // $scope.artist_title = "";
                // $scope.trailer_data = [];
            }
        }, function(resp) {
            Notification.info({ message: resp.data });
            $("[name='btn_save']").attr("disabled", false);
        });
        $("[name='btn_save']").attr("disabled", true);
    };

    $(document).ready(function() {
        document.getElementsByName('post_date')[0].valueAsDate = new Date();
    });
});
