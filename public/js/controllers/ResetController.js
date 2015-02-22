app.controller("ResetController", function($scope, $route, PasswordService) {
    $scope.successMsg = '';
    $scope.data = {
        token : $route.current.pathParams.token,
        password : '',
        password_confirmation : ''
    };

    $scope.reset = function() {
        PasswordService.reset($scope.data).success(function(res) {
            $scope.successMsg = res.flash;
        });
    };
});