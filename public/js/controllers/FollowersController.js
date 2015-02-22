app.controller("FollowersController", function($scope, $route, DeviceService, followers) {
    $scope.followers = followers;
    $scope.reload = $route.reload;

    var getSelectedIds = function(){
        var ids = [];
        for (var i=0; i<$scope.followers.length; i++){
            if ($scope.followers[i].selected) ids.push($scope.followers[i].id);
        }

        return ids;
    };

    var selected = false;
    $scope.selectAll = function(){
        selected = selected ? false : true;
        for (var i=0; i<$scope.followers.length; i++){
            $scope.followers[i].selected = selected;
        }
    };

    $scope.hasSelectedDevice = function(){
        var ids = getSelectedIds();
        return ids.length ? true : false;
    };

    $scope.deleteSelected = function(){
        var ids = getSelectedIds();
        $promise = DeviceService.deleteFollowingDevice(ids);
        $promise.then(function(){
            $scope.reload();
        });
    };

    $scope.updateActive = function(device){
        DeviceService.activeFollowingDevice(device);
    };

    $scope.getApprovedClass = function(isActive){
        return isActive ? 'green' : 'red';
    };
});