
<link rel="stylesheet" href="<?php echo UNL_OpenMap_Controller::getURL(); ?>leaflet/leaflet.css" />
<!--[if lte IE 8]>
    <link rel="stylesheet" href="<?php echo UNL_OpenMap_Controller::getURL(); ?>leaflet/leaflet.ie.css" />
<![endif]-->

<script src="<?php echo UNL_OpenMap_Controller::getURL(); ?>leaflet/leaflet.js"></script>



<?php
// Default dimensions for map
$width  = '960px';
$height = '620px';
if (isset($context->options['format'])
    && ($context->options['format'] == 'mobile' || $context->options['format'] == 'partial')) {
    $width = $height = '100%';
}
?>
<div style="width:<?php echo $width; ?>;height:<?php echo $height; ?>">
    <div id="map" style="width:100%;height:100%;">
        <noscript>
            Get javascript or get walkin'
        </noscript>
    </div>
</div>






<script type="text/javascript">
var $ = WDN.jQuery;
var displayedFeatures = [];
var campuses, buildings;

var setMap = function() {
    WDN.log('set the map');

    $(document).ready(function() {
        if (map.getZoom() < 15) {
            showCampuses();
        } else {
            showBuildings();
        }
    });
};

var showCampuses = function() {
    if (displayedFeatures.indexOf('buildings') > -1) {
        WDN.log('remove buildings');
        $.each(buildings, function(key, loc) {
            map.removeLayer(loc);
        });
        displayedFeatures.pop('buildings');
    }
    if (typeof campuses == 'undefined') {
        $.getJSON('<?php echo UNL_OpenMap_Controller::getURL(); ?>campuses?format=json', function(data) {
            WDN.log('JSON returned campuses');
            var items = [];

            $.each(data, function(key, val) {
                if (val.position.polygon.length > 0) {
                    items[val.code] = [];
                    $.each(val.position.polygon, function(key1, latlon) {
                        items[val.code].push(new L.LatLng(latlon.latitude, latlon.longitude));
                    });
                }
            });

            var keys = [];
            for (var key in items) {
                keys.push(key);
            }

            campuses = [];
            $.each(keys, function(key, code) {
                polygon = new L.Polygon(items[code]);
                map.addLayer(polygon);
                campuses.push(polygon);
            });
            displayedFeatures.push('campuses');
        });
    } else if (displayedFeatures.indexOf('campuses') < 0) {
        WDN.log('campuses already loaded, add them');
        $.each(campuses, function(key, loc) {
            map.addLayer(loc);
        });
        displayedFeatures.push('campuses');
    }

};

var showBuildings = function() {
    if (displayedFeatures.indexOf('campuses') > -1) {
        WDN.log('remove campuses');
        $.each(campuses, function(key, loc) {
            map.removeLayer(loc);
        });
        displayedFeatures.pop('campuses');
    }
    if (typeof buildings == 'undefined') {
        $.getJSON('<?php echo UNL_OpenMap_Controller::getURL(); ?>buildings?format=json', function(data) {
            WDN.log('JSON returned buildings');
            var items = [];

            $.each(data, function(key, val) {
                if (val.position.polygon.length > 0) {
                    items[val.code] = [];
                    $.each(val.position.polygon, function(key1, latlon) {
                        items[val.code].push(new L.LatLng(latlon.latitude, latlon.longitude));
                    });
                }
            });

            var keys = [];
            for (var key in items) {
                keys.push(key);
            }

            buildings = [];
            $.each(keys, function(key, code) {
                polygon = new L.Polygon(items[code]);
                map.addLayer(polygon);
                buildings.push(polygon);
            });
            displayedFeatures.push('buildings');
        });
    } else if (displayedFeatures.indexOf('buildings') < 0) {
        WDN.log('buildings already loaded, add them');
        $.each(buildings, function(key, loc) {
            map.addLayer(loc);
        });
        displayedFeatures.push('buildings');
    }
};




// initialize the map on the "map" div
var map = new L.Map('map');

// create a CloudMade tile layer (or use other provider of your choice)
var unlOSM = new L.TileLayer('<?php echo UNL_OpenMap_Controller::getURL(); ?>images/tiles/open-streets-lnk/{z}/{x}/{y}.png', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
    maxZoom: <?php echo $context->mapMaxZoom; ?>,
    minZoom: <?php echo $context->mapMinZoom; ?>,
    scheme: 'tms'
});

// zoomend is also triggered on load
map.on('zoomend', setMap);

// add the layer to the map set the view to a given center and zoom
map.setView(new L.LatLng(40.827839, -96.685524), <?php echo $context->zoom; ?>).addLayer(unlOSM);






var polygonPoints =
	[
	new L.LatLng(40.815758,-96.694536),
	new L.LatLng(40.815693,-96.706553),
	new L.LatLng(40.817317,-96.706982),
	new L.LatLng(40.818811,-96.707754),
	new L.LatLng(40.819785,-96.708527),
	new L.LatLng(40.821214,-96.708269),
	new L.LatLng(40.821864,-96.707754),
	new L.LatLng(40.822708,-96.70681),
	new L.LatLng(40.826735,-96.698999),
	new L.LatLng(40.830437,-96.700974),
	new L.LatLng(40.831281,-96.699514),
	new L.LatLng(40.831346,-96.696939),
	new L.LatLng(40.828683,-96.696424),
	new L.LatLng(40.828034,-96.695309),
	new L.LatLng(40.824657,-96.695051),
	new L.LatLng(40.824462,-96.692476),
	new L.LatLng(40.823682,-96.692133),
	new L.LatLng(40.823552,-96.690073),
	new L.LatLng(40.822578,-96.689901),
	new L.LatLng(40.822513,-96.687155),
	new L.LatLng(40.821084,-96.687069),
	new L.LatLng(40.820825,-96.689644),
	new L.LatLng(40.817057,-96.690073),
	new L.LatLng(40.816927,-96.693506),
	new L.LatLng(40.815758,-96.694536)
	];
var polygon = new L.Polygon(polygonPoints);
polygon.bindPopup("I am a polygon.");
polygon.on('click', onMapClick);
//map.addLayer(polygon);


//create a marker in the given location and add it to the map
var marker = new L.Marker(new L.LatLng(40.817719, -96.701513));
map.addLayer(marker);

//attach a given HTML content to the marker and immediately open it
marker.bindPopup("City Campus").openPopup();




//Handle clicks
//map.on('click', onMapClick);
var popup = new L.Popup();
function onMapClick(e) {
	var latlngStr = '(' + e.latlng.lat.toFixed(3) + ', ' + e.latlng.lng.toFixed(3) + ')';

	popup.setLatLng(e.latlng);
	popup.setContent("You clicked the map at " + latlngStr);
	map.openPopup(popup);
}

</script>
