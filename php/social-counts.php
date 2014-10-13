<?php

	// Facebook
	$facebook_like_share_count = function ( $url ) {
	    $api = file_get_contents( 'http://graph.facebook.com/?id=' . $url );
	    $count = json_decode( $api );
	 
	    return $count->shares; // Return # of shares
	};

	// Twitter
	$twitter_tweet_count = function ( $url ) {
	    $api = file_get_contents( 'https://cdn.api.twitter.com/1/urls/count.json?url=' . $url );
	    $count = json_decode( $api );
	 
	    return $count->count; // Return # of tweets
	};
	 
	// Pinterest
	$pinterest_pins = function ( $url ) {
	    $api = file_get_contents( 'http://api.pinterest.com/v1/urls/count.json?callback%20&url=' . $url );
	    $body = preg_replace( '/^receiveCount\((.*)\)$/', '\\1', $api );
	    $count = json_decode( $body );
	 
	    return $count->count; // Return # of pins
	};

	// LinkedIn
	$linkedin_share = function ( $url ) {
	    $api = file_get_contents( 'https://www.linkedin.com/countserv/count/share?url=' . $url . '&format=json' );
	    $count = json_decode( $api );
	 
	    return $count->count;
	};

	// StumbleUpon
	$stumbleupon = function ( $url ) {
	    $api = file_get_contents( 'https://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $url );
	    $count = json_decode( $api );
	 
	    return $count->result->views; // Return # of views
	};

	// Google Plus
	$google_plusones = function ( $url ) {
	    $curl = curl_init();
	    curl_setopt( $curl, CURLOPT_URL, "https://clients6.google.com/rpc" );
	    curl_setopt( $curl, CURLOPT_POST, 1 );
	    curl_setopt( $curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
	    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
	    $curl_results = curl_exec( $curl );
	    curl_close( $curl );
	    $json = json_decode( $curl_results, true );
	 
	    return intval( $json[0]['result']['metadata']['globalCounts']['count'] ); // Return # of +1's
	};

	$get_url_social_counts = function( $url ){
		$counts = array(
			'facebook' => facebook_like_share_count($url),
			'twitter' => twitter_tweet_count($url),
			'pinterest' => pinterest_pins($url),
			'linkedin' => linkedin_share($url),
			'stumbleupon' => stumbleupon($url),
			'googleplus' => google_plusones($url)
		);
		return $counts;
	}

?>