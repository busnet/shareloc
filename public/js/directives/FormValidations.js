/*
app.directive('confirm', function() {
    return {
        restrict: 'A',
        require : 'ngModel',
        link : function(scope, elm, attrs, ctrl) {
            ctrl.$parsers.unshift(function(viewValue) {
                //TODO: remove hardcoded - use attr.confirm instead + check password.on('keyUp')
                if (scope.newUser.password){
                    if (scope.newUser.password == viewValue) {
                        // it is valid
                        ctrl.$setValidity('confirm', true);
                        return viewValue;
                    } else {
                        // it is invalid, return undefined (no model update)
                        ctrl.$setValidity('confirm', false);
                        return undefined;
                    }
                }
            });
        }
    };
});
*/
app.directive('confirm', [function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, elem, attrs, ctrl) {
            var confirmSel = '#' + attrs.confirm;
            elem.add(confirmSel).on('keyup', function () {
                scope.$apply(function () {
                    var v = elem.val() === $(confirmSel).val();
                    ctrl.$setValidity('confirm', v);
                });
            });
        }
    }
}]);

//app.directive('unique', ['$http', '$q', function ($http, $q) {
//    return {
//        restrict: 'A',
//        require: 'ngModel',
//        link : function(scope, elm, attrs, ctrl) {
//            ctrl.$parsers.unshift(function(value) {
//                var regex=/^[0-9a-zA-Z]+@[0-9a-zA-Z]+[\.]{1}[0-9a-zA-Z]+[\.]?[0-9a-zA-Z]+$/;
//                if (regex.test(value)) {
//                    $http.post('/auth/isUnique', {'email' : value})
//                        .success(function(){
//                            ctrl.$setValidity('unique', true);
//                            return value;
//                        }).error(function(){
//                            ctrl.$setValidity('unique', false);
//                            return undefined;
//                        });
//                }
//
//                return value;
//            });
//        }
//    }
//}]);

app.directive('unique', [function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link : function(scope, elm, attrs, ctrl) {
            elm.on('keydown', function () {
                ctrl.$setValidity('unique', true);
            });
        }
    }
}]);

app.directive('exists', [function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link : function(scope, elm, attrs, ctrl) {
            elm.on('keydown', function () {
                ctrl.$setValidity('exists', true);
            });
        }
    }
}]);