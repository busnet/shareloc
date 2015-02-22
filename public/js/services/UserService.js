app.factory("UserService", function($http, $sanitize, AuthenticationService, FlashService, CSRF_TOKEN) {

    var cacheSession   = function() {
        SessionService.set('authenticated', true);
    };

    var uncacheSession = function() {
        SessionService.unset('authenticated');
    };

    var loginError = function(response) {
        FlashService.show(response.flash);
    };
});