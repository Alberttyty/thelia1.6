var img_url = 'http://www.mondialrelay.com/img/';
var bounds;
var map;
var overlays = [];
var InfoWindow;

var GM3 = GM3 || function () {

    var private = {
        parameters: null,

        // Supprime les markers de la carte
        ClearOverlays: function () {
            for (var n = 0, overlay; overlay = overlays[n]; n++) {
                overlay.setMap(null);
            }
            overlays = [];
            bounds = new google.maps.LatLngBounds();
        },

        AddSimpleMarker: function (LatLng, title) {

            // Calcul de la lettre associé au PCL
            var letter =
                overlays.length < 26
                ? String.fromCharCode("A".charCodeAt(0) + (overlays.length))
                : "";

            // Création du Marker
            var marker = new google.maps.Marker({
                position: LatLng,
                map: map,
                //icon: new google.maps.MarkerImage(img_url + "mr_pointrelais/gmaps3_pr" + letter + ".png")
                icon: new google.maps.MarkerImage(img_url + "gmaps_pr02.png"),
                title: title
            });

            // Ajoute le Marker à la collection
            overlays.push(marker);

            // Prépare le redimentionnement de la carte
            bounds.extend(LatLng);

            return marker;
        },

        AddPopupMarker: function (LatLng, url, popup_spec, title) {

            // Création du Marker
            var marker = private.AddSimpleMarker(LatLng, title);

            // Ajoute une action à l'évennement "click"
            google.maps.event.addListener(marker, 'click', function () {
                var popup = window.open(url, 'G3_popup', popup_spec);
                popup.focus();
            });

            return marker;
        },

        AddInfoMarker: function (LatLng, text) {

            // Création du Marker
            var marker = private.AddSimpleMarker(LatLng);

            // Ajoute une action à l'évennement "click"
            google.maps.event.addListener(marker, 'click', function () {
                var Marker_WindowOptions = {
                    content: text
                };
                var Marker_InfoWindow = new google.maps.InfoWindow(Marker_WindowOptions);
                Marker_InfoWindow.open(map, marker);
            });

            return marker;
        }
    };

    var public = {

        // Initialisation
        Init: function (prms) {
            private.params = prms;
            var map_options = {
                zoom: private.params.MapZoom ? private.params.MapZoom : 12, // Zoom initial
                center: new google.maps.LatLng(private.params.MapPositionLatitude, private.params.MapPositionLongitude), // Position initiale
                mapTypeId: google.maps.MapTypeId.ROADMAP, // Type initial
                panControl: false, // Flèches de direction
                rotateControl: true,
                scaleControl: true, // Mesure de distance
                scrollwheel: private.params.MapScrollWheel ? private.params.MapScrollWheel : false, // Zoom avec la molette de la souris
                streetViewControl: private.params.MapStreetView ? private.params.MapStreetView : false, // Autorisation de StreetView
                zoomControl: true // Contrôle de zoom
            };
            map = new google.maps.Map(document.getElementById(private.params.MapDiv), map_options);
            overlays = [];
            bounds = new google.maps.LatLngBounds();
        },

        AddSimpleMarker: function (Lat, Long, title) {

            // Transformation de la position
            var LatLng = new google.maps.LatLng(Lat, Long)

            // Création du Marker
            var marker = private.AddSimpleMarker(LatLng, title);

            return marker;
        },

        AddPopupMarker: function (Lat, Long, url, popup_spec, title) {

            // Transformation de la position
            var LatLng = new google.maps.LatLng(Lat, Long)

            // Création du Marker
            var marker = private.AddPopupMarker(LatLng, url, popup_spec, title); // popup_spec = 'scrollbars=no,width=790,height=530,top=10,left=10'

            return marker;
        },

        AddInfoMarker: function (Lat, Long, text) {

            // Transformation de la position
            var LatLng = new google.maps.LatLng(Lat, Long)

            // Création du Marker
            var marker = private.AddInfoMarker(LatLng, text);

            return marker;
        },

        SetGoodZoom: function () {
            // Redimentionne la carte
            map.fitBounds(bounds);
        }

    }

    return public;
} ();