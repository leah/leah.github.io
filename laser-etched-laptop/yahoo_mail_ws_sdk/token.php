<?php

require("lib/auth.inc");
require("lib/config.inc");
require("lib/crypto.inc");

// Check to make sure the signature from the login servers is valid.
$valid = sig_validate($SECRET);
if($valid["status"]) {
	// It's valid, store the token and send the user on.
	set_user_token($_GET["token"]);
	header("Location: http://$HOSTNAME$BASE_URL/index.php");
}
else {
	print $valid["error"];
}

?>

