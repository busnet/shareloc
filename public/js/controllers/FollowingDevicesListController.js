app.controller("FollowingDevicesListController", function($scope, $route, PlaceService, followingDevices, places) {
    $scope.followingDevices = followingDevices;
    $scope.places = places;
    $scope.reload = $route.reload;

    //if no nickname use the phone_code for the nickname(Admin only)
    for (var i = 0; i < followingDevices.length; i++) {
        if (!followingDevices[i].nickname && followingDevices[i].phone_no) {
            followingDevices[i].nickname = followingDevices[i].phone_no;
        }
    }

    $scope.isApproved = function(followingDevice){
        return followingDevice.approved_at ? true : false;
    };

    $scope.getMapImg = function(followingDevice){
        var src = '';

        if (followingDevice.approved_at && !followingDevice.disabled_at && followingDevice.lat && followingDevice.long){
            var latLongStr = followingDevice.lat + ',' + followingDevice.long;
            var pinUrl = getPinIco(followingDevice.created_at);

            //Dev env
            pinUrl = pinUrl.replace('shareloc.local', 'shareloc.co/sloc/public/');

            //var icon = encodeURIComponent(pinUrl);
            //src = '//maps.googleapis.com/maps/api/staticmap?center=' + latLongStr  + '&zoom=13&size=50x50&maptype=roadmap&markers=icon:' + icon + '|' + latLongStr + '&sensor=false';
            src = pinUrl;
        } else if (!followingDevice.approved_at){
            src = 'img/pin/not-approved.png';
        } else {
            src = 'img/map-disabled-50x50.jpg';
        }

        return src;
    };

    $scope.removePlace = function(place){
        if (window.confirm('Are you sure you want to delete this place:' + place.name)){
            PlaceService.remove(place).then(function(){
                var places = PlaceService.getPlaces();
                places.then(function(places){
                    $scope.places = places;
                });
            });
        }
    };
});