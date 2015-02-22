app.directive('googleMap', function($window, $anchorScroll, $location) {
    return {
        restrict : 'A',
        scope : {
          markers : '=',
          places : '=',
          newPlace : '=',
          onClickMarker: '&'
        },
        link : function(scope, element, attrs, controller) {
            var mapElement = document.createElement('div');
            mapElement.id = 'map-element-' + Date.now();
            mapElement.style.width = '100%';
            mapElement.style.height = '100%';

            if(!scope.markers.length) mapElement.style.backgroundImage = "url('img/layout/bg_no_device.png')";
            element.append(mapElement);

            var displayZoomControl = false;
            if(screen.width > 991) displayZoomControl = true;

            var map = null;
            var createMap = function(){
                google.maps.visualRefresh = true;

                var mapOptions = {
                    maxZoom: 19,
                    streetViewControl : false,
                    center: new google.maps.LatLng(32.06615829467773,34.77782058715820),
                    noClear: false,
                    overviewMapControl: false,
                    scaleControl : false,
                    rotateControl : false,
                    panControl : false,
                    zoomControl: displayZoomControl,
                    zoomControlOptions: {style: google.maps.ZoomControlStyle.LARGE,position: google.maps.ControlPosition.RIGHT_CENTER},
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                map = new google.maps.Map(mapElement, mapOptions);
            };

            $window.mapInitialize = function(){
                if (!map) createMap();

                map.overlayMapTypes.setAt( 0, null);
                bounds = new google.maps.LatLngBounds ();

                var markers = [];
                var oneTime = true;


                var infowindow = new google.maps.InfoWindow({
                    //content: "<div>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea explicabo ipsum laboriosam! Dolore, eligendi, et. Alias dolor eaque eveniet iure, nihil non quos repellat ut vel voluptatem voluptatibus voluptatum. Velit.</div>"
                    content : angular.element('#place-form')[0]
                });


                angular.element('#place-form .btn-cancel').click(function(){
                    infowindow.close();
                    newPlace.setMap(null);
                });

                angular.element('#place-form .btn-save').click(function(){
                    var name = angular.element('#place_name').val();
                    if (name){
                        infowindow.close();
                        newPlace.setMap(null);
                    }
                });


                var newPlace;
                google.maps.event.addListener(map, 'click', function(e) {
                    scope.$apply(function(scope){
                        //TODO: auto focus
                        //angular.element('#place_name').focus();

                        scope.newPlace = {};
                        scope.newPlace.lat = e.latLng.lat();
                        scope.newPlace.long = e.latLng.lng();
                        //scope.newPlace.name = '';
                    });

                    if (newPlace) newPlace.setMap(null);
                    newPlace = new MarkerWithLabel({
                        position: e.latLng,
                        map: map,
                        icon: getPinIco('P'),
                        title: 'Place',
                        labelContent: 'Place',
                        labelAnchor: new google.maps.Point(50, -1),
                        labelClass: "marker-label", // the CSS class for the label
                        labelStyle: {}
                    });

                    infowindow.open(map, newPlace);
                });

                scope.$watch('places', function(newVal, oldVal){
                    angular.forEach(newVal, function (place, i) {

                        if (!place.lat || !place.long) return;

                        var latlong = new google.maps.LatLng(place.lat, place.long);

                        var newMarker = new MarkerWithLabel({
                            position: latlong,
                            map: map,
                            icon: getPinIco('P'),
                            title: place.name,
                            labelContent: place.name,
                            labelAnchor: new google.maps.Point(50, -1),
                            labelClass: "marker-label", // the CSS class for the label
                            labelStyle: {}
                        });

                        //if (oneTime) bounds.extend(latlong);
                    });
                }, true);
                scope.$watch('markers', function(){
                    var hasMarker = false;

                    if (scope.markers.length){
                        for (var i=0; i < markers.length; i++){
                            markers[i].setMap(null);
                        }
                        markers = [];
                    }

                    angular.forEach(scope.markers, function (marker, i) {
                        if (!marker.lat || !marker.long) return;

                        hasMarker = true;
                        var latlong = new google.maps.LatLng(marker.lat, marker.long);

                        var newMarker = new MarkerWithLabel({
                            position: latlong,
                            map: map,
                            title: marker.nickname,
                            visible : marker.isVisible,
                            labelContent: marker.nickname ? marker.nickname : marker.phone_no,
                            labelAnchor: new google.maps.Point(50, -1),
                            labelClass: "marker-label marker_" + marker.id, // the CSS class for the label
                            icon: getPinIcon(marker.created_at, marker.device_type),
                            labelStyle: {}
                        });

                        var deviceUrl = 'following-device/' + marker.id;
                        var deviceInfo = marker.nickname ? marker.nickname : marker.phone_no;
                        var boxText = document.createElement("div");
                        boxText.style.cssText = "border: 1px solid black; margin-top: 8px; background: white; padding: 5px;";
                        boxText.innerHTML = "Device : " + deviceInfo + " <br>Last Report: " + convertTimeStamp(marker.created_at) + "<br><a style='color:black;' href=" + deviceUrl +">more details >></a>";

                        var ibOptions = {
                            content: boxText,
                            disableAutoPan: false,
                            maxWidth: 0,
                            pixelOffset: new google.maps.Size(-100, -150),
                            zIndex: null,
                            boxStyle: {opacity: 1,width: "245px"},
                            closeBoxMargin: "10px 2px 2px 2px",
                            closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
                            infoBoxClearance: new google.maps.Size(1, 1),
                            isHidden: false,
                            pane: "floatPane",
                            enableEventPropagation: false
                        };

                       google.maps.event.addListener(newMarker, 'click', function(e) {
                            //window.location.href = '/following-device/' + marker.id;
                            e.stop();
                           ib.open(map, this);
                            //$location.url('/following-device/' + marker.id);
                            //$location.path('following-device/' + marker.id);
//                            $window.location.href = 'following-device/' + marker.id;
                            //return false;
                            //scope.onClickMarker(marker);
                       });

                        var ib = new InfoBox(ibOptions);

                        markers.push(newMarker);

                        if (oneTime) bounds.extend(latlong);
                    });

                    if (oneTime && hasMarker) map.fitBounds(bounds);
                    oneTime = false;
                });
            };

            //Inject Google maps script if needed
            var googleMapsScriptLoaded = (window.google && window.google.maps) ? true : false;
            if (!googleMapsScriptLoaded){
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=mapInitialize';
                document.body.appendChild(script);
            } else {
                window.mapInitialize();
            }
        }
    };
});
