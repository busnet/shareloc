app.factory("DeviceService", function($http, $q, FlashService) {
    var onSuccess = function(){
        FlashService.clear();
    };

    var onError = function(response) {
        FlashService.show(response.flash);
    };

    return {
        add : function(device){
            var deferred = $q.defer();

            $http.post('user/device', device)
                .success(function(device){
                    onSuccess();
                    deferred.resolve(device);
                }).error(function(response){
                    onError(response);
                    deferred.reject();
                });

            return deferred.promise;
        },
        getDevices: function() {
            var deferred = $q.defer();

            $http.get('user/devices')
                .success(function(devices){
                    onSuccess();
                    deferred.resolve(devices);
                }).error(function(response){
                    onError(response);
                    deferred.reject(response);
                });

            return deferred.promise;
        },
        getFollowers: function() {
            var deferred = $q.defer();

            $http.get('perm/followers')
                .success(function(devices){
                    onSuccess();
                    deferred.resolve(devices);
                }).error(function(response){
                    onError(response);
                    deferred.reject(response);
                });

            return deferred.promise;
        },
        getFollowingDevices : function(){
            var deferred = $q.defer();

            $http.get('perm/followingDevices')
                .success(function(devices){
                    onSuccess();
                    deferred.resolve(devices);
                }).error(function(response){
                    onError(response);
                    deferred.reject(response);
                });

            return deferred.promise;
        },
        getFollowingDevice : function(id){
            var deferred = $q.defer();
            $http.get('perm/followingDevice/' + id)
                .success(function(devices){
                    onSuccess();
                    deferred.resolve(devices);
                }).error(function(response){
                    onError(response);
                    deferred.reject(response);
                });

            return deferred.promise;
        },
        addFollowingDevice : function(device){
            var deferred = $q.defer();

            $http.post('perm/followingDevice', device)
                .success(function(device){
                    onSuccess();
                    deferred.resolve(device);
                }).error(function(response){
                    onError(response);
                    deferred.reject(response);
                });

            return deferred.promise;
        },
        activeFollowingDevice : function(device){
            var deferred = $q.defer();

            $http.put('perm/activeFollowingDevice', device)
                .success(function(device){
                    onSuccess();
                    deferred.resolve(device);
                }).error(function(response){
                    onError(response);
                    deferred.reject(response);
                });

            return deferred.promise;
        },
        updateFollowingDevice : function(device){
            var deferred = $q.defer();

            $http.post('perm/updateFollowingDevice', device)
                .success(function(device){
                    onSuccess();
                    deferred.resolve(device);
                }).error(function(response){
                    onError(response);
                    deferred.reject(response);
                });

            return deferred.promise;
        },
        deleteFollowingDevice : function(ids){
            if (! ids.length) return;

            var deferred = $q.defer();

            $http({method: 'DELETE', url: 'perm/followingDevice', data: {ids : ids}})
                .success(function(res){
                    onSuccess();
                    deferred.resolve(res);
                }).error(function(res){
                    onError(res);
                    deferred.reject(res);
                });

            return deferred.promise;
        },
        deleteDevice : function(id){
            var deferred = $q.defer();
            $http({method: 'DELETE', url: 'perm/device/' + id})
                .success(function(res){
                    onSuccess();
                    deferred.resolve(res);
                }).error(function(res){
                    onError(res);
                    deferred.reject(res);
                });

            return deferred.promise;
        }
    };
});