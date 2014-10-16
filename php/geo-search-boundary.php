<?php

/**
 * Returns the max/min lat and lng of an area to search within, given the distance and center point.
 * @param  number $distance Give distance in KM
 * @param  number $lat      Latitude coordinate of center point
 * @param  number $lon      Longitude coordinate of center point
 * @return array           	Array of max/min lat and lng. Useful for comparing the lat/lng of a location to see if it falls within the area.
 */
function getBoundary( $distance, $lat, $lon ) {

	$radius = 6378.1; // Earth radius in km

	// Init variables
	$minLat = 0;
	$minLon = 0;
	$maxLat = 0;
	$maxLon = 0;

	// Latitude and Longitude in radians
	$newLat = deg2rad($lat);
	$newLng = deg2rad($lon);

	// Bearings
	$MIN_LAT = deg2rad(180);
	$MAX_LAT = deg2rad(0);
	$MIN_LON = deg2rad(270);
	$MAX_LON = deg2rad(90);

	// Our angular distance
	$angDist = ($distance*1.60934)/$radius;

	// Calculations based on http://trac.osgeo.org/openlayers/wiki/GreatCircleAlgorithms
	$maxLat = asin(sin($newLat)*cos($angDist)+cos($newLat)*sin($angDist)*cos($MAX_LAT));
	$minLat = asin(sin($newLat)*cos($angDist)+cos($newLat)*sin($angDist)*cos($MIN_LAT));
	$maxLon = $newLng + atan(( sin($angDist)*sin($MAX_LON) )/( cos($newLat)*cos($angDist)-sin($newLat)*sin($angDist)*cos($MAX_LON) ));
	$minLon = $newLng + atan(( sin($angDist)*sin($MIN_LON) )/( cos($newLat)*cos($angDist)-sin($newLat)*sin($angDist)*cos($MIN_LON) ));

	// Convert back to decimal lat/lng coordinate system
	$minLat = rad2deg($minLat);
	$minLon = rad2deg($minLon);
	$maxLat = rad2deg($maxLat);
	$maxLon = rad2deg($maxLon);

	// Set of max/min coordinates to search within.
	return array("minLat"=>$minLat,"minLon"=>$minLon,"maxLat"=>$maxLat,"maxLon"=>$maxLon);
}

?>