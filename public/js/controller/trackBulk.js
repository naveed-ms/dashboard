app.controller("Ctrl", function($scope, $http, $location, fileUpload, Upload, Notification) {
    var titleArray = [];

    $("[name='lst_movie']").on('change', function() {
        $.ajax({
            url: '/api/get/TrackByMovie/' + $("[name='lst_movie']").val(),
            success: function(data) {
                data.forEach(function(item) {
                    var row = "<tr>";
                    row += "<td>" + item.name + "</td>";
                    row += "</tr>";
                    $("[name='tbl_tracks_old'] tbody").append(row);
                });
            },
            error: function(err) {
                consoe.log(err);
            }
        });
    });

    $scope.add = function() {
        var table = $("[name='tbl_tracks']");
        var movie_id = $("[name='lst_movie']").val();
        var movie_name = $("[name='lst_movie'] [value='" + movie_id + "']").text();
        var title = $scope.txt_title;
        var title_col = "<td>" + title + "</td>";
        var movie_col = "<td>" + movie_name + "</td>";
        var status_col = "<td name='status'></td>";
        if ($scope.file_mp3_url && $scope.txt_title && $.inArray($scope.txt_title, titleArray) < 0) {
            titleArray.push(title);
            $http({
                method: "POST",
                url: baseUrl + "/tracks/bulkSave",
                data: {
                    name: $scope.txt_title,
                    movie: $("[name='lst_movie']").val(),
                }
            }).then(function(data) {
                var track_id = 0;
                if (parseInt(data) != NaN || parseInt(data) != undefined || parseInt(data) != 0) {
                    track_id = data.data.track_id;
                    var row = "<tr id='" + track_id + "'>" + title_col + movie_col + "<td> <img src='/public/img/spinner-mini.gif' style='width:1vw'/> <span name='status'></span> </td>" + "<tr>";
                    table.prepend(row);
                    Upload.upload({
                        url: "/tracks/uploader",
                        data: {
                            "track_id": track_id,
                            "movie_id": $("[name='lst_movie']").val(),
                            "audio_url": $scope.file_mp3_url
                        }
                    }).then(function(data) {
                        $("#" + track_id + " td [name='status']").each(function(item) { $(this).text(data.data) });
                        if (data = "Uploaded") {
                            $("#" + track_id + " td img").hide();
                        }
                    }, function(err) {
                        $("#" + track_id + " td img").hide();
                        $("#" + track_id + " td [name='status']").each(function(item) { $(this).text("error occured while uploading....network failure") });
                        alert("error occured while uploading");
                    }, function(evt) {
                        var progress = parseInt(100.0 * evt.loaded / evt.total);
                        $("#" + track_id + " td [name='status']").each(function(item) { $(this).text(progress + "%") });
                    });
                }
            });

        } else {
            if ($.inArray($scope.txt_title, titleArray) >= 0) {
                Notification.info({ message: 'Title is already in uploading queue' });
            } else {
                Notification.info({ message: 'Please select mp3 or enter valid title' });
            }

        }
    }
});