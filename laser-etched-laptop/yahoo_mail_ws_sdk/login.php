<?php

require_once("lib/config.inc");

if(isset($_GET[".done"]) && (strlen($_GET[".done"]) > 0)) {
	setcookie("auth_done_loc", $_GET[".done"]);
}

$auth_url = build_auth_url($SECRET, $APP_ID);

function build_auth_url($secret, $appid) {
	$time = time();
	$queryString = http_build_query(array(
				"appid" => $appid,
				"ts" => $time
				));
	$sig = md5("/WSLogin/V1/wslogin?" . $queryString . $secret);

	$relativeUrl = "/WSLogin/V1/wslogin?" . http_build_query(array(
				"appid" => $appid,
				"ts" => $time,
				"sig" => $sig
				));

	header("Location: https://api.login.yahoo.com$relativeUrl");
}

?>
<html>

	<head>
		<meta http-equiv="refresh" content="0" url="<?php echo $auth_url; ?>">
	</head>

	<body>

	</body>

</html>
