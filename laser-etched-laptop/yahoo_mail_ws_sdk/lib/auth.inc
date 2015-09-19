<?php

// For more details regarding authentication, visit developer.yahoo.com 
// and read the documentation on "Browser Based Authentication".
//
//     http://developer.yahoo.com/auth/
// 

// Need the config...it has the secret and the application ID.
require_once("config.inc");

// Stores the user token in a cookie.
function set_user_token($token) {
	// Store the token in a cookie for up to 14 days. Tokens are 
	// only good for 14 days.
	$timeout = time() + (60 * 60 * 24 * 14);
	setcookie("ytoken", $token, $timeout);
}

// Checks to see if the user already has a token.
function has_user_token() {
	$ytoken = $_COOKIE["ytoken"];
	return isset($ytoken) && (strlen($ytoken) > 0);
}

// Gets the user token.
function get_user_token() {
	// Get the token out of the cookie.
	return $_COOKIE["ytoken"];
}

// Stores the user credentials in a cookie.
function set_user_credentials($credentials) {
	// Store the credentials in a cookie with a timeout determined 
	// by the login servers. Store the object as a serialized PHP string.
	setcookie("ycred", serialize($credentials), $credentials->timeout);
}

// Checks to see if the user already has credentials.
function has_user_credentials() {
	$ycred = $_COOKIE["ycred"];
	return isset($ycred) && (strlen($ycred) > 0);
}

// Gets the user credentials.
function get_user_credentials() {
	// Get the token out of the cookie.
	return unserialize(urldecode($_COOKIE["ycred"]));
}

// Given a token, fetch new user credentials.
function load_user_credentials($token) {
	global $APP_ID;
	global $SECRET;

	// Build up the URL to send the credential request to. Generate the 
	// signature first.
	$ts = time();
	$sig = md5("/WSLogin/V1/wspwtoken_login?appid=$APP_ID&token=$token&ts=$ts" . "$SECRET");

	// Construct the final URL.
	$url = "https://api.login.yahoo.com/WSLogin/V1/wspwtoken_login?appid=$APP_ID&token=$token&ts=$ts&sig=$sig";

	// Set up curl and fetch some credentials.
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $store = curl_exec( $ch );
    $xml = curl_exec( $ch );

	// Pick out the Y cookie, the WSSID and the timeout from the 
	// XML response.
    if (  preg_match( "/(Y=.*)/", $xml, $match_array ) == 1 ) {
        $COOKIE = $match_array[1];
    }
    if (  preg_match( "/<WSSID>(.+)<\/WSSID>/", $xml, $match_array ) == 1 ) {
        $WSSID = $match_array[1];
    }
    if (  preg_match( "/<Timeout>(.+)<\/Timeout>/", $xml, $match_array ) == 1 ) {
        $timeout = $match_array[1];
    }

	// Store the credentials in a new object.
    $credentials = new stdclass();
    $credentials->cookie = $COOKIE;
    $credentials->wssid = $WSSID;
    $credentials->timeout   = time() + $timeout;

	return $credentials;
}

// Build a login URL. Sends the user to login.php.
function build_login_url() {
	$params = array(".done" => $_SERVER["REQUEST_URI"]);
	return "login.php?" . http_build_query($params);
}

?>
