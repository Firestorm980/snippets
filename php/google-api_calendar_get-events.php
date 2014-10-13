<?php

/**
 * Gets events list from a Google calendar. Requires the Google API PHP Client Library.
 * 
 * @param  [array]  $options 		List of parameters passed to the function to filter by. https://developers.google.com/google-apps/calendar/v3/reference/events/list
 * @param  [string] $calendarId 	The Google Calendar ID of the specific calendar to get.
 * @return [array]           		Response from Google. Includes all data from call.
 */
function gapi_gcal_get_events( $options = array(), $calendarId = ''){

	require_once 'inc/Google-PHP-API/autoload.php';
	
	$apiKey = '{{ YOUR KEY HERE }}'; // Key restrictions currently only tested with allowing any referrer.
	$client = new Google_Client();
	$client->setApplicationName('{{ YOUR APP NAME }}');
	$client->setDeveloperKey($apiKey);

	$service = new Google_Service_Calendar($client);
	$events = $service->events->listEvents($calendarId, $options);

	return $events; // Returns raw events data object. To loop through the items, $events['items'].

}

?>