<?php

require("lib/auth.inc");
require("lib/config.inc");

$fid = urlencode($_GET["fid"]);
$mid = urlencode($_GET["mid"]);
$pid = urlencode($_GET["pid"]);

$credentials = NULL;
if(!has_user_credentials()) {
	$token = get_user_token();
	$credentials = load_user_credentials($token);
	set_user_credentials($credentials);
}
else if(has_user_credentials()) {
	$credentials = get_user_credentials();
}

$appid = urlencode($APP_ID);
$wssid = urlencode($credentials->wssid);

$downloadurl = "http://mail.yahooapis.com/ya/download?fid=$fid&mid=$mid&pid=$pid&appid=$appid&WSSID=$wssid";

$ch = curl_init($downloadurl);
curl_setopt($ch, CURLOPT_COOKIE, $credentials->cookie);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADERFUNCTION, "readHeader");
$rr = curl_exec($ch);

print $rr;

function readHeader($ch, $header) {
	if(preg_match("/^Content-Type:/", $header) || preg_match("/^Content-Disposition:/", $header)) {
		header($header);
	}
	error_log($header);

	return strlen($header);
}

?>
