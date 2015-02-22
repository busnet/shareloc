app.controller("RootController", function($scope, $location, $window, AuthenticationService, FlashService) {
    //$rootScope.loggedInUser = AuthenticationService.getLoggedInUser();

    $scope.addActive = function(page){
        var currentRoute = $location.path().substring(1) || 'home';
        return page === currentRoute ? 'active' : '';
    };

    $scope.backPage = function(){
        $window.history.back();
    };

    $scope.isLoggedIn = AuthenticationService.isLoggedIn;
    $scope.clearFlash = FlashService.clear;

//    $scope.homePage = function(){
//        $location.url('/');
//    };
//
//    $scope.termsPage = function(){
//        $location.url('/terms');
//    };
//
//    $scope.loginPage = function(){
//        $location.url('/login');
//    };
//
//    $scope.registerPage = function(){
//        $location.url('/register');
//    };
//
//    $scope.dashboardPage = function(){
//        $location.url('/dashboard');
//    };
//
//    $scope.devicesIFollowPage = function(){
//        $location.url('/devicesIFollow');
//    };
//
//    $scope.usersFollowMePage = function(){
//        $location.url('/usersFollowMe');
//    };

});