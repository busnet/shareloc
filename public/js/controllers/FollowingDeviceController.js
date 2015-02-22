app.controller("FollowingDeviceController", function($scope, $location, DeviceService, NearbyAlertService,  device, places) {
    $scope.device = device;
    $scope.places = places;

    var getNearbyAlerts = function(){
        var nearbyAlers = NearbyAlertService.getNearByAlerts(device.id);
        nearbyAlers.then(function(nearbyAlerts){
            $scope.nearbyAlerts = nearbyAlerts;
        });
    };
    getNearbyAlerts();



    $scope.delete = function(id){
        DeviceService.deleteDevice(id).then(function(){
            $location.path('/following-devices-list');
        });
    };

    $scope.nearbyAlert = {device_id : device.id, distance : 100};
    if (places.length) $scope.nearbyAlert.place_id = places[0].id;

    $scope.addNearbyAlert = function(){
        var nearbyAlert = NearbyAlertService.add($scope.nearbyAlert);
        nearbyAlert.then(getNearbyAlerts);
    };

    $scope.removeNearbyAlert = function(nearbyAlert){
        if (window.confirm('Are you sure you want to delete this alert')){
            var nearbyAlers = NearbyAlertService.remove(nearbyAlert);
            nearbyAlers.then(getNearbyAlerts);
        }
    };
});