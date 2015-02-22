app.factory("PasswordService", function($http, $q, FlashService) {
    return {
        reminder : function(data){
            FlashService.clear();

            var register = $http.post("password/remind", data);
            register.error(function(res){
                FlashService.show(res.flash);
            });
            return register;
        },

        reset : function(data){
            FlashService.clear();

            var register = $http({
                url : 'password/reset',
                method : 'POST',
                data : $.param(data),
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                }
                //"password/reset", $.param(data)
            });
            register.error(function(res){
                FlashService.show(res.flash);
            });
            return register;
        }
    };
});