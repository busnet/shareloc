app.factory("PlaceService", function($http, $q, FlashService) {
    var onSuccess = function(){
        FlashService.clear();
    };

    var onError = function(response) {
        FlashService.show(response.flash);
    };

    return {
        add : function(place){
            var deferred = $q.defer();

            $http.post('user/place', place)
                .success(function(place){
                    onSuccess();
                    deferred.resolve(place);
                }).error(function(response){
                    onError(response);
                    deferred.reject();
                });

            return deferred.promise;
        },
        remove : function(place){
            var deferred = $q.defer();

            $http.delete('user/place/' + place.id)
                .success(function(place){
                    onSuccess();
                    deferred.resolve(place);
                }).error(function(response){
                    onError(response);
                    deferred.reject();
                });

            return deferred.promise;
        },
        getPlaces : function(){
            var deferred = $q.defer();

            $http.get('user/places')
                .success(function(devices){
                    onSuccess();
                    deferred.resolve(devices);
                });

            return deferred.promise;
        }
    }
});