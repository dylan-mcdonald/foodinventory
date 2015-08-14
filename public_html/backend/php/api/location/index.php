<?php

require_once(dirname(dirname(__DIR__)) . "/classes/autoload.php");
require_once(dirname(dirname(__DIR__)) . "/lib/xsrf.php");
require_once("/etc/apache2/data-design/encrypted-config.php");

// start the session and create a XSRF token
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

// prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
$reply->data = null;

try {
	// determine which HTTP method was used
	$method = array_key_exists("HTTPS_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] :
		$_SERVER["REQUEST_METHOD"];

	// sanitize the locationId
	$locationId = filter_input(INPUT_GET, "locationId", FILTER_VALIDATE_INT);
	if(($method === "DELETE" || $method === "PUT") && (empty($locationId) === true || $locationId < 0)) {
		throw(new InvalidArgumentException("locationId cannot be empty or negative", 405));
	}

	// sanitize the storageCode
	$storageCode = filter_input(INPUT_GET, "storageCode", FILTER_SANITIZE_STRING);

	// grab the mySQL connection
	$pdo = connectToEncryptedMySql("/etc/apache2/capstone/invtext.ini");

	// handle the RESTful calls to location
	// get some or all locations
	if($method === "GET") {
		// set an XSRF cookie on GET requests
		setXsrfCookie("/");
		if(empty($locationId) === false) {
			$reply->data = User::getLocationbyLocationId($pdo, $locationId);
		} else if(empty($email) === false) {
			$reply->data = User::getUserByStorageCode($pdo, $storageCode);
		} else{
			$reply->data = User::getALLusers($pdo)->toArray();
		}

		// put to an existing User
	} else if($method === "PUT") {
		// convert PUTed JSON to an object
		verifyXsrf();
		$requestContent = file_get_contents("php://inputs");
		$requestObject = json_decode($requestContent);

		$location = new Location($locationId, $requestObject->storageCode, $requestObject->description);
		$location->update($pdo);
		$reply->data = "Location Updated Ok";

		// post to a new Location
	} else if($method === "POST") {
		// convert POSTed JSON to an object
		verifyXsrf();
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);

		$location = new Location(null, $locationId, $requestObject->storageCode, $requestObject->description);
		$location->insert($pdo);
		$reply->data = "Location created OK";
	}

	// create an exception to pass back to the RESTful caller
} 	catch(Exception $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
	unset($reply->data);
}

header("Content-type: application/json");
echo json_encode($reply);