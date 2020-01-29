var app = angular.module('bsongs', ["datatables", "ngSanitize", "ngFileUpload", 'ui-notification'], function($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});

app.config(function(NotificationProvider) {
    NotificationProvider.setOptions({
        delay: 10000,
        startTop: 20,
        startRight: 10,
        verticalSpacing: 20,
        horizontalSpacing: 20,
        positionX: 'right',
        positionY: 'top'
    });
});

app.directive('fileModel', ['$parse', function($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            if (attrs.txtTitle) {
                var txt = $parse(attrs.txtTitle);
                var txtSetter = txt.assign;
            }

            element.bind('change', function() {
                scope.$apply(function() {
                    // var contains = element[0].name;
                    // if (contains.search("cover") > -1) {
                    //     if (element[0].files[0].type) {

                    //     }
                    // } else if (contains.search("mp3") > -1) {

                    // } else if (contains.search("mpd") > -1) {

                    // } else if (contains.search("mp4") > -1) {

                    // }
                    modelSetter(scope, element[0].files[0]);
                    if (txt != undefined) {
                        txtSetter(scope, element[0].files[0].name);
                    }
                });
            });
        }
    };
}]);

app.service('fileUpload', ['$http', function($http) {
    this.uploadFileToUrl = function(file, uploadUrl, attr) {
        var fd = new FormData();
        fd.append(attr, file);
        $http.post(uploadUrl, fd, {
                transformRequest: angular.identity,
                headers: { 'Content-Type': undefined }
            })
            .success(function(data) {
                return data;
            })
            .error(function(err) {
                return err;
            });
    }
}]);