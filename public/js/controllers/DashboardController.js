app.controller("DashboardController", function($scope, $route, followers, followingDevices) {
    $scope.followers = followers;
    $scope.followingDevices = followingDevices;
    $scope.reload = $route.reload;
});