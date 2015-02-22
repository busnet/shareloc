app.controller("RemindController", function($scope, PasswordService) {
    $scope.successMsg = '';
    $scope.data = { email: ""};

    $scope.reminder = function() {
        PasswordService.reminder($scope.data).success(function(res) {
            $scope.successMsg = res.flash;
        });
    };
});