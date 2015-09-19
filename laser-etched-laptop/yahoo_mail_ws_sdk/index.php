<?php

	// Pull in some helpers.
	require("lib/auth.inc");
	require("lib/config.inc");
	require("lib/ymws.inc");

	// See if we already have either credentials or a token.
	$credentials = NULL;
	if(!has_user_credentials() && has_user_token()) {
		// No credentials, but we do have a token. Use that to 
		// fetch some credentials.
		$credentials = load_user_credentials(get_user_token());
		set_user_credentials($credentials);
	}
	else if(has_user_credentials()) {
		// We already have credentials. Double check and make sure they 
		// aren't already expired.
		$credentials = get_user_credentials();
		if(time() > $credentials->timeout) {
			// Credentials are expired, load new ones.
			$credentials = load_user_credentials(get_user_token());
			set_user_credentials($credentials);
		}
	}

	// Check to see if we have credentials now.
	if(!is_null($credentials)) {
		// We have credentials...load the client.
		$client = get_ymail_client($APP_ID, $credentials);

		// Create a new batch request.
		$batchRequest = new stdclass();
		$batchRequest->call = array();

		// Construct the new GetUserData call. It takes no parameters. 
		// When done, add it to the batch.
		$getUserData = new stdclass();
		$getUserData->GetUserData = new stdclass();
		array_push($batchRequest->call, $getUserData);

		// Construct the new ListFolders call. It takes no parameters. 
		// When done, add it to the batch.
		$listFolders = new stdclass();
		$listFolders->ListFolders = new stdclass();
		array_push($batchRequest->call, $listFolders);

		// Construct the new ListMessages call. It takes 3 parameters: 
		// the starting message, the number of messages and a folder ID.
		$listMessages = new stdclass();
		$listMessages->ListMessages = new stdclass();
		$listMessages->ListMessages->startInfo = 0;
		$listMessages->ListMessages->numInfo = 25;

		// Check to see if a folder ID was specified. If not, use the 
		// Inbox as a default. When done, add to the batch.
		if(!isset($_GET["folder"])) {
			$listMessages->ListMessages->fid = "Inbox";
		}
		else {
			$listMessages->ListMessages->fid = $_GET["folder"];
		}
		array_push($batchRequest->call, $listMessages);

		// Execute the batch request. This will throw an exception 
		// if something goes wrong.
		$response = $client->BatchExecute($batchRequest);

		// Break up the 3 responses for convenience.
		$userData = $response->response[0]->GetUserDataResponse;
		$folders = $response->response[1]->ListFoldersResponse;
		$messages = $response->response[2]->ListMessagesResponse;
	}

?>
<html>

	<head>
		<title>Yahoo! Mail SDK</title>
		<link rel="stylesheet" href="style.css"/>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="icon" href="favicon.ico" type="image/x-icon">
	</head>

	<body>
		<!-- If there are no credentials, tell the user to log in. -->
		<?php if(is_null($credentials)) { ?>
		<table width="100%" height="100%">
			<tr>
				<td align="center" valign="middle">
					You must <a href="<?php echo build_login_url(); ?>">log in</a> first.
				</td>
			</tr>
		</table>
		<?php } else { ?>
		<table>
			<tr>
				<!-- List the folders -->
				<td valign="top">
				<?php
				foreach($folders->folder as $folder) { 
					// System folders first.
					if($folder->isSystem) {
				?>
					<a href="?folder=<?php echo urlencode($folder->folderInfo->fid); ?>"><?php echo $folder->folderInfo->name; ?></a> (<?php echo $folder->unread; ?>)<br>
				<?php
					}
				}
				?>
				<hr>
				<?php
				foreach($folders->folder as $folder) { 
					// Now the non system folders.
					if(!$folder->isSystem) {
				?>
					<a href="?folder=<?php echo urlencode($folder->folderInfo->fid); ?>"><?php echo $folder->folderInfo->name; ?></a> (<?php echo $folder->unread; ?>)<br>
				<?php
					}
				}
				?>
				</td>
				<!-- List the messages -->
				<td valign="top">
					<table>
						<tr>
							<th>From</th>
							<th>Subject</th>
							<th>Date</th>
							<th>Size</th>
						</tr>
				<?php
				foreach($messages->messageInfo as $message) {
				?>
						<tr>
							<td><?php echo $message->from->name; ?> </td>
							<td><?php echo $message->subject; ?></td>
							<td><?php echo date("F jS, Y", $message->receivedDate); ?></td>
							<td><?php echo $message->size; ?></td>
						</tr>
				<?php
				}
				?>
					</table>
				</td>
			</tr>
		</table>
		<?php } ?>
	</body>

</html>
