<?php

require("lib/auth.inc");
require("lib/config.inc");

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

$uploadurl = "http://mail.yahooapis.com/ya/upload?appid=$appid&WSSID=$wssid";

header("Content-Type: text/plain");

var_dump($_FILES);

$uploads = array();
for($i = 0; $i < count($_FILES["attachment"]["tmp_name"]); $i++) {
	$filename = $_FILES["attachment"]["tmp_name"][$i];
	$type = $_FILES["attachment"]["type"][$i];
	// $uploads["attachment_" . count($uploads)] = "@$filename;type=$type";
	$uploads["attachment_" . count($uploads)] = "@$filename";
}

var_dump($uploads);

$ch = curl_init($uploadurl);
curl_setopt($ch, CURLOPT_COOKIE, $credentials->cookie);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADERFUNCTION, "readHeader");
curl_setopt($ch, CURLOPT_POSTFIELDS, $uploads);
$rr = curl_exec($ch);

print $rr;

function readHeader($ch, $header) {
	print($header);

	return strlen($header);
}

?>
