<!doctype html>
<html lang="en" ng-app="ShareLoc">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <base href="<?php echo Config::get('app.url');?>/"/>
    <meta charset="UTF-8">

    <link href="img/layout/logo.png" rel="apple-touch-icon">
    <link href="img/layout/logo-76x76.png" rel="apple-touch-icon" sizes="76x76">
    <link href="img/layout/logo-120x120.png" rel="apple-touch-icon" sizes="120x120">
    <link href="img/layout/logo-152x152.png" rel="apple-touch-icon" sizes="152x152">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <title>ShareLoc</title>
    <link rel="stylesheet" href="packages/bootstrap/css/bootstrap.min.css">
<!--    <link rel="stylesheet" href="packages/bootstrap/darkstrap/darkstrap.css">-->
    <style type="text/css">
        .sidebar-nav {
            padding: 9px 0;
        }

        @media (max-width: 980px) {
            /* Enable use of floated navbar text */
            .navbar-text.pull-right {
                float: none;
                padding-left: 5px;
                padding-right: 5px;
            }
        }
    </style>
    <link rel="stylesheet" href="packages/bootstrap/css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="css/app.css">

</head>

<body class="ng-cloak">

<div ng-if="isLoading" id="loading">
    <div id="loading-box">Loading</div>
</div>

<div id="container" ng-controller="RootController">
    <div id="top-nav-placeholder" class="visible-desktop"></div>
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse" ng-hide="hideMenu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="brand"><div id="logo">&nbsp;</div></div>
                <div class="nav-collapse collapse" ng-hide="hideMenu">
                    <p ng-show="isLoggedIn()" class="navbar-text pull-right">
                        <small>{{loggedInUser.full_name}}</small>&nbsp;|&nbsp;
                        <a class="navbar-link" href="logout" title="Log out">Log Out</a>
                    </p>
                    <ul class="nav">
                        <li ng-hide="isLoggedIn()" ng-class="addActive('login')"><a href="login" title="Open login page">Login</a></li>
                        <li ng-hide="isLoggedIn()" ng-class="addActive('register')"><a href="register" title="Open register page">Register</a></li>
                        <li ng-show="isLoggedIn()" ng-class="addActive('devices')"><a href="following-devices-map" title="Open Devices I follow page">Devices I follow</a></li>
                        <li ng-show="isLoggedIn()" ng-class="addActive('permissions')"><a href="followers" title="Open Permissions to follow me page">Permissions to follow me</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
    </div>

    <div class="container">
        <div class="alert alert-error" ng-show="flash">
            <button ng-click="clearFlash()" class="close" type="button">×</button>
            {{ flash }}
        </div>
    </div>

    <div ng-view ng-animate="{enter: 'animate-enter', leave: 'animate-leave'}"></div>

    <div id="footer-placeholder"></div>
    <div id="footer">

        <!--<p><small>Created by <a href="http://misterbit.co.il/?en" target="_blank">misterBIT</a></small></p>-->

    </div>
</div>



<script src="js/basic.js"></script>
<script src="js/utils/strtotime.js"></script>


<script src="packages/jquery/js/jquery-1.10.2.min.js"></script>
<script src="packages/underscore/underscore-min.js"></script>
<script src="packages/bootstrap/js/bootstrap.min.js"></script>

<!--<script src="packages/angular-1.1.5/angular.min.js"></script>-->
<!--<script src="packages/angular-1.1.5/angular-sanitize.min.js"></script>-->
<!--<script src="packages/angular-1.1.5/angular-cookies.min.js"></script>-->

<script src="packages/angular-1.2.6/angular.min.js"></script>
<script src="packages/angular-1.2.6/angular-route.min.js"></script>
<script src="packages/angular-1.2.6/angular-sanitize.min.js"></script>
<script src="packages/angular-1.2.6/angular-cookies.min.js"></script>

<script src="js/app.js"></script>

<script src="js/controllers/RootController.js"></script>
<script src="js/controllers/LoginController.js"></script>
<script src="js/controllers/RegisterController.js"></script>
<script src="js/controllers/RemindController.js"></script>
<script src="js/controllers/ResetController.js"></script>
<script src="js/controllers/DashboardController.js"></script>
<script src="js/controllers/AddFollowingDeviceController.js"></script>

<script src="js/controllers/FollowingDevicesMapController.js"></script>
<script src="js/controllers/FollowingDevicesListController.js"></script>
<script src="js/controllers/FollowersController.js"></script>
<script src="js/controllers/FollowingDeviceController.js"></script>

<script src="js/services/SessionService.js"></script>
<script src="js/services/FlashService.js"></script>
<script src="js/services/UserService.js"></script>
<script src="js/services/AuthenticationService.js"></script>
<script src="js/services/PasswordService.js"></script>
<script src="js/services/DeviceService.js"></script>
<script src="js/services/PlaceService.js"></script>
<script src="js/services/NearbyAlertService.js"></script>

<script src="js/directives/FormValidations.js"></script>
<script src="js/directives/googleMap.js"></script>

<script>
    if (typeof Device == 'undefined'){
        var Device = {
            getPhoneNo : function(){
                return null;
            },
            getCode : function(){
                return "RegFromDesktop_"+ new Date().getTime();
            },
            startReportService : function(){}
        };
    }

    angular.module("ShareLoc").constant("CSRF_TOKEN", '<?php echo csrf_token(); ?>');

    //Device Data
    angular.module("ShareLoc").constant("DEVICE", {phone_no : Device.getPhoneNo(), code : Device.getCode()});
</script>



<script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3&sensor=false"></script>
<script src="packages/googleMap/markerwithlabel.js"></script>
<script src="packages/googleMap/infobox.js"></script>

</body>
</html>
