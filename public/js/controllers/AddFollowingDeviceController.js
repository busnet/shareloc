app.controller("AddFollowingDeviceController", function($scope, $location, DeviceService) {
    $scope.device = {};
    $scope.add = function(){
        $pDevice = DeviceService.addFollowingDevice($scope.device);
        $pDevice.then(function(){
            $location.path('/following-devices-list');
        }, function(res){
            setFormValidation($scope, 'addFollowingDeviceForm', res);
        });
    };
});