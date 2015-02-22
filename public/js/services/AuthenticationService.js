app.factory("AuthenticationService", function($rootScope, $http, $sanitize, $q, SessionService, FlashService, CSRF_TOKEN) {
    var getLoggedInUser = function(){
        return $rootScope.loggedInUser ? $rootScope.loggedInUser : null;
    };
    var setLoggedInUser = function(user){
        $rootScope.loggedInUser = user;
    };
    var removeLoggedInUser = function(){
        $rootScope.loggedInUser = null;
    };

    var cacheSession   = function() {
        SessionService.set('authenticated', true);
    };

    var uncacheSession = function() {
        SessionService.unset('authenticated');
    };

    var onError = function(response) {
        FlashService.show(response.flash);
    };

    var onSuccess = function(user) {
        setLoggedInUser(user);
        cacheSession();
        FlashService.clear();
    };

    var sanitizeCredentials = function(credentials) {
        return {
            email: $sanitize(credentials.email),
            pass: $sanitize(credentials.password),
            remember: credentials.remember,
            csrf_token: CSRF_TOKEN
        };
    };

    var sanitizeRegister = function(newUser) {
        return {
            phone: $sanitize(newUser.phone),
            full_name: $sanitize(newUser.full_name),
            email: $sanitize(newUser.email),
            pass: $sanitize(newUser.password),
            agree: newUser.agree,
            remember: newUser.remember,
            csrf_token: CSRF_TOKEN,
            device: newUser.device
        };
    };

    return {
        register: function(newUser) {
            var register = $http.post("auth/register", sanitizeRegister(newUser));
            register.success(onSuccess);
            register.error(onError);
            return register;
        },
        login: function(credentials) {
            var login = $http.post("auth/login", sanitizeCredentials(credentials));
            login.success(onSuccess);
            login.error(onError);
            return login;
        },
        logout: function() {
            var logout = $http.get("auth/logout");
            logout.success(function(){
                removeLoggedInUser();
                uncacheSession();
            });
            return logout;
        },
        isLoggedIn: function() {
            return SessionService.get('authenticated');
        },
        getLoggedInUser: function(){
            if (!getLoggedInUser()) {
                var deferred = $q.defer();

                var loggedInUser = $http.get("auth/loggedInUser");
                loggedInUser.success(function(user){
                    onSuccess(user);
                    deferred.resolve(user);
                });

                setLoggedInUser(deferred.promise);
            }
            return getLoggedInUser();
        }
    };
});