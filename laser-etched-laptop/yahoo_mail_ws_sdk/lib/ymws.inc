<?php

require("lib/JSON.php");

function get_ymail_client($appid, $credentials) {
	return new YmailClient($appid, $credentials->wssid, $credentials->cookie);
}

class YmailClient {
	private $_querystring;
	private $_ycookie;

	// Store the application ID, WSSID and the Y cookie.
	function __construct($appid, $wssid, $ycookie) {
		$this->_querystring = http_build_query(array("appid" => $appid, 
				"WSSID" => $wssid));
		$this->_ycookie = $ycookie;
	}

	// Handles all API calls.
	function __call($method, $arguments) {
		// Set up the JSON-RPC request payload.
		$request = new stdclass();
		$request->method = $method;
		$request->params = $arguments;

		// Set up the JSON encoder/decoder.
		$json = new Services_JSON();

		// Init the curl handler.
		$ch = curl_init("http://mail.yahooapis.com/ws/mail/v1/jsonrpc?$this->_querystring");

		// We're doing a POST.
		curl_setopt($ch, CURLOPT_POST, 1);

		// JSON encode the request into the POST data.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json->encode($request));

		// Set the Y cookie.
		curl_setopt($ch, CURLOPT_COOKIE, $this->_ycookie);

		// Tell curl to return the call body.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// Execute the request.
		$rawresponse = curl_exec($ch);

		// Decode the response.
		$response = $json->decode($rawresponse);

		// Close up the curl handler.
		curl_close($ch);

		// Make sure the call was successful.
		if(!is_null($response->error)) {
			throw new YmailException($response->error->message, 
					$response->error->code,
					$response->error->detail);
		}

		return $response->result;
	}
}

class YmailException extends Exception {
	public function __construct($message, $code, $detail) {
		parent::__construct($message);
		$this->errorCode = $code;
		$this->detail = $detail;
	}

	public function getErrorCode() {
		return $this->errorCode;
	}

	public function getDetail() {
		return $this->detail;
	}
}

?>
