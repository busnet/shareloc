var app = angular.module("ShareLoc", ['ngSanitize', 'ngCookies', 'ngRoute']);

app.config(function($httpProvider) {

  var logsOutUserOn401 = function($rootScope, $location, $q, SessionService, FlashService) {
      var counter = 0;

      var success = function(response) {
          if ((--counter)===0) $rootScope.isLoading = false;

          return response;
      };

      var error = function(response) {
          if ((--counter)===0) $rootScope.isLoading = false;

          if(response.status === 401) {
              SessionService.unset('authenticated');
              $location.path('/login');
              FlashService.show(response.data.flash);
          } else if (response.status === 403){
              window.location.href = 'register';
          }
          return $q.reject(response);
      };

    return function(promise) {
      counter++;
      $rootScope.isLoading = true;

      return promise.then(success, error);
    };
  };

  $httpProvider.responseInterceptors.push(logsOutUserOn401);
});

app.config(function($routeProvider, $locationProvider) {

    $routeProvider.when('/', {
        //templateUrl: 'templates/home.html'
        redirectTo: '/register',
        resolve: {
            ifUserLoggedIn : function($location, AuthenticationService){
                if (AuthenticationService.isLoggedIn()) $location.path('/following-devices-map');
            }
        }
    });

    $routeProvider.when('/login', {
        templateUrl: 'templates/login.html',
        controller: 'LoginController'
    });

    $routeProvider.when('/logout', {
        redirectTo: '/login',
        resolve: {
            logout : function($location, AuthenticationService){
                return AuthenticationService.logout();
            }
        }
    });

    $routeProvider.when('/remind', {
        templateUrl: 'templates/remind.html',
        controller: 'RemindController'
    });


    $routeProvider.when('/reset/:token', {
        templateUrl: 'templates/reset.html',
        controller: 'ResetController'
    });


    $routeProvider.when('/terms', {
        templateUrl: 'templates/terms.html'
    });

    $routeProvider.when('/register', {
        templateUrl: 'templates/register.html',
        controller: 'RegisterController'
    });

//    $routeProvider.when('/dashboard', {
//        templateUrl: 'templates/user/dashboard.html',
//        controller: 'DashboardController',
//        resolve: {
//            followers : function(DeviceService){
//                return DeviceService.getFollowers();
//            },
//            followingDevices : function(DeviceService){
//                return DeviceService.getFollowingDevices();
//            }
//        }
//    });

    $routeProvider.when('/add-following-device', {
        templateUrl: 'templates/user/addFollowingDevice.html',
        controller: 'AddFollowingDeviceController'
    });

    $routeProvider.when('/followers', {
        templateUrl: 'templates/user/followers.html',
        controller: 'FollowersController',
        resolve: {
            followers : function(DeviceService){
                return DeviceService.getFollowers();
            }
        }
    });



    $routeProvider.when('/following-devices-map', {
        templateUrl: 'templates/user/followingDevicesMap.html',
        controller: 'FollowingDevicesMapController',
        resolve: {
            followingDevices : function(DeviceService){
                return DeviceService.getFollowingDevices();
            },
            places : function(PlaceService){
                return PlaceService.getPlaces();
            }
        }
    });

    $routeProvider.when('/following-devices-list', {
        templateUrl: 'templates/user/followingDevicesList.html',
        controller: 'FollowingDevicesListController',
        resolve: {
            followingDevices : function(DeviceService){
                return DeviceService.getFollowingDevices();
            },
            places : function(PlaceService){
                return PlaceService.getPlaces();
            }
        }
    });

    $routeProvider.when('/following-device/:id', {
        templateUrl: 'templates/user/followingDevice.html',
        controller: 'FollowingDeviceController',
        resolve : {
            device : function($route, $location, DeviceService){
                var device = DeviceService.getFollowingDevice($route.current.pathParams.id);
                device.then(function(){},function(res){
                    $location.path('/home');
                });

                return device;
            },
            places : function(PlaceService){
                return PlaceService.getPlaces();
            }
        }
    });

    $routeProvider.otherwise({ redirectTo: '/' });

    $locationProvider.html5Mode(true).hashPrefix('!');
});

app.run(function($rootScope, $location, AuthenticationService, FlashService) {
  var routesThatRequireAuth = ['/dashboard', '/add-device', '/following-devices', '/add-following-device', '/followers', '/following-device/:id'];

    $rootScope.$on('$routeChangeStart', function(event, next, current) {
        $rootScope.isLoading = true;
        if(_(routesThatRequireAuth).contains($location.path())) {
            if (!AuthenticationService.isLoggedIn()){
                $location.path('/login');
                FlashService.show("Please log in to continue.");
            }
        }

        if ($location.path().indexOf('/reset/') >= 0) $rootScope.hideMenu = true;
        else $rootScope.hideMenu = false;

        //Close mobile top nav
        if ($('.nav-collapse.in').length) $('.btn-navbar').click();
    });

    $rootScope.$on('$routeChangeSuccess', function(event, next, current) {
        $rootScope.isLoading = false;
    });

    if (AuthenticationService.isLoggedIn()) $rootScope.loggedInUser = AuthenticationService.getLoggedInUser();



    try {
        Device.startReportService();
    } catch (e){

    }
});
