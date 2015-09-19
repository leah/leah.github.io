<?php

function sig_validate($secret) {
	$ts  = $_GET["ts"];    // the current unix time
	$sig = $_GET["sig"];   // the signature of the request
	$relative_url = $_SERVER["REQUEST_URI"];
	$match = array();

	// The signature is a 32 character hex string, and is the last
	// parameter at the end of the request.
	$match_rv = preg_match(  "/^(.+)&sig=(\w{32})$/", $relative_url, $match);

	if ( $match_rv == 1 ) {
		if ($match[2] != $sig ) {
			$rv = array( "status" => false,
					"error"  =>"Duplicate sig parameters passed?: $sig, " . $match[2] );
			return $rv;
		}
	}
	else {
		$rv = array( "status" => false,
				"error"  =>"Missing or invalid sig parameter" );
		return $rv;
	}

	// at this point, the url looks valid, and the sig was parsed from the url
	$relative_url_without_sig = $match[1];

	// Check that the ts parameter is within 600 seconds of the current time
	$current_time = time();
	$clock_skew  = abs($current_time - $ts);
	if ( $clock_skew >= 600 ) {
		$rv = array( "status" => false,
				"error"  => "invalid timestamp: clock_skew is $clock_skew seconds" );
		return $rv;
	}

	// now calculate the signature, and verify that the resulting signature
	// equals what was passed to us
	$sig_input = $relative_url_without_sig . $secret;
	$calculated_sig = md5($sig_input);
	if ( $calculated_sig == $sig ) {
		$rv = array( "status"=> true );
	}
	else {
		$rv = array( "status" => false,
				"error"  => "calculated_sig $calculated_sig does not match sig parameter $sig" );
	}
	return $rv;
}

?>
