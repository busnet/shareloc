app.factory("NearbyAlertService", function($http, $q, FlashService) {
    var onSuccess = function(){
        FlashService.clear();
    };

    var onError = function(response) {
        FlashService.show(response.flash);
    };

    return {
        add : function(nearbyAlert){
            var deferred = $q.defer();

            $http.post('user/nearbyAlert', nearbyAlert)
                .success(function(nearbyAlert){
                    onSuccess();
                    deferred.resolve(nearbyAlert);
                }).error(function(response){
                    onError(response);
                    deferred.reject();
                });

            return deferred.promise;
        },
        remove : function(nearbyAlert){
            var deferred = $q.defer();

            $http.delete('user/nearbyAlert/' + nearbyAlert.id)
                .success(function(nearbyAlert){
                    onSuccess();
                    deferred.resolve(nearbyAlert);
                }).error(function(response){
                    onError(response);
                    deferred.reject();
                });

            return deferred.promise;
        },
        getNearByAlerts : function(id){
            var deferred = $q.defer();

            $http.get('user/nearbyAlerts?device_id=' + id)
                .success(function(nearbyAlerts){
                    onSuccess();
                    deferred.resolve(nearbyAlerts);
                });

            return deferred.promise;
        }
    }
});