app.controller("FollowingDevicesMapController",function ($scope, $route, $location, $timeout, DeviceService, PlaceService, followingDevices, places, notify) {
    $scope.followingDevices = followingDevices;
    $scope.places = places;
    $scope.tmpObj = null;

    var updateMarker = function () {
        $timeout(function () {
            var p = DeviceService.getFollowingDevices();
            p.then(function (res) {
                //if no nickname use the phone_code for the nickname(Admin only)
                for (var i = 0; i < res.length; i++) {
                    if (!res[i].nickname && res[i].phone_no) {
                        res[i].nickname = res[i].phone_no;
                        res[i].isVisible = true;
                    } else if(res[i].nickname){
                        res[i].isVisible = true;
                    }

                    if($scope.tmpObj != null && $scope.tmpObj.length){
                        for(var j = 0; j < $scope.tmpObj.length; j++){
                            if($scope.tmpObj[j].id == res[i].id){
                                if($scope.tmpObj[j].isVisible === 'yes'){
                                    res[i].isVisible = true;
                                } else if($scope.tmpObj[j].isVisible === 'no'){
                                    res[i].isVisible = false;
                                }
                            }
                        }
                    }
                }
                $scope.followingDevices = res;
                updateMarker();
            });

        }, 2000);
    };

    updateMarker();

    $scope.hasMarker = function () {
        var res = false;

        for (var i = 0; i < $scope.followingDevices.length; i++) {
            if ($scope.followingDevices[i].approved_at && !$scope.followingDevices[i].disabled_at) {
                res = true;
                break;
            }
        }

        for(var i = 0; i < $scope.followingDevices.length; i++){
            if(!$scope.followingDevices[i].approved_at && !$scope.followingDevices[i].disabled_at && $scope.followingDevices[i].created_at){
                res = true;
                break;
            }
        }

        return res;
    };
    $scope.addPlace = function () {
        if ($scope.newPlace.name) {
            var place = PlaceService.add($scope.newPlace);
            place.then(function (place) {
                //TODO: check $$v promise issue
                var places = PlaceService.getPlaces();
                places.then(function (places) {
                    $scope.places = places;
                });
            });
        }
    };

    $scope.reload = $route.reload;

    $scope.showList = function () {
        $location.path('/following-devices-list');
    };
    $scope.displayDevice = function (e, device) {
        var isVisible = '';
        if ($('#'+device.id).is(':checked')) isVisible = 'yes';
        else isVisible = 'no';

        notify($scope, device, isVisible);
    };
}).factory('notify', ['$window', function ($scope, device,isVisible) {
        return function ($scope, device, isVisible) {

            var tmp = {id: device.id, isVisible: isVisible};

            if($scope.tmpObj == null){
                $scope.tmpObj = [];
                $scope.tmpObj.push(tmp);
            } else {
                if($.inArray($scope.tmpObj, tmp) === -1){
                    $scope.tmpObj.push(tmp);
                } else {
                    for(var m = 0; m < $scope.tmpObj.length; m++){
                        if($scope.tmpObj[m].id == device.id){
                            if($scope.tmpObj[m].isVisible != isVisible){
                                $scope.tmpObj[m].isVisible == 'yes'
                            }
                        }
                    }
                }
            }
        };
}]);






