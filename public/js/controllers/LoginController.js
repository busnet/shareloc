app.controller("LoginController", function($scope, $location, AuthenticationService) {
    $scope.credentials = { email: "", password: "", remember: false};

    $scope.login = function() {
        AuthenticationService.login($scope.credentials).success(function(user) {
            if (user.has_perm_to_approved) $location.path('/followers');
            else $location.path('/following-devices-map');
        });
    };
});