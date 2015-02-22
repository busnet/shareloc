app.controller("RegisterController", function($scope, $location, AuthenticationService, DEVICE) {
    $scope.newUser = {device : DEVICE};

    $scope.register = function() {
        AuthenticationService.register($scope.newUser).success(function() {
            $location.path('/following-devices-map');
        }).error(function (res, status) {
            if (500 === status) setFormValidation($scope, 'registerForm', res);
        });

    };
});