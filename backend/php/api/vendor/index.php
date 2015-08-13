<?php
require_once(dirname(dirname(__DIR__)) . "/lib/xsrf.php");
require_once(dirname(dirname(__DIR__)) . "/classes/vendor.php");
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
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	// sanitize the vendorId
	$movementId = filter_input(INPUT_GET, "movementId", FILTER_VALIDATE_INT);
	if(($method === "DELETE" || $method === "PUT") && (empty($movementId) === true || $movementId < 0)) {
		throw(new InvalidArgumentException("movementId cannot be empty or negative", 405));
	}

	// sanitize the fromLocationId
	$fromLocationId = filter_input(INPUT_GET, "fromLocationId", FILTER_VALIDATE_INT);
	if(($method === "DELETE" || $method === "PUT") && (empty($fromLocationId) === true || $fromLocationId < 0)) {
		throw(new InvalidArgumentException("fromLocationId cannot be empty or negative", 405));
	}

	// sanitize the toLocationId
	$toLocationId = filter_input(INPUT_GET, "toLocationId", FILTER_VALIDATE_INT);
	if(($method === "DELETE" || $method === "PUT") && (empty($toLocationId) === true || $toLocationId < 0)) {
		throw(new InvalidArgumentException("toLocationId cannot be empty or negative", 405));
	}

	// sanitize the productId
	$productId = filter_input(INPUT_GET, "productId", FILTER_VALIDATE_INT);
	if(($method === "DELETE" || $method === "PUT") && (empty($productId) === true || $productId < 0)) {
		throw(new InvalidArgumentException("productId cannot be empty or negative", 405));
	}

	// sanitize the userId
	$userId = filter_input(INPUT_GET, "userId", FILTER_VALIDATE_INT);
	if(($method === "DELETE" || $method === "PUT") && (empty($userId) === true || $userId < 0)) {
		throw(new InvalidArgumentException("userId cannot be empty or negative", 405));
	}

	// grab the mySQL connection
	$pdo = connectToEncryptedMySql("/etc/apache2/capstone/invtext.ini");

	// handle all RESTful calls to Movement
	// get some or all Movements
	if($method === "GET") {
		// set an XSRF cookie on GET requests
		setXsrfCookie("/");
		if(empty($movementId) === false) {
			$reply->data = Movement::getMovementByMovementId($pdo, $movementId);
		} else if(empty($fromLocationId) === false) {
			$reply->data = Movement::getMovementByFromLocationId($pdo, $fromLocationId);
		} else if(empty($toLocationId) === false) {
			$reply->data = Movement::getMovementByToLocationId($pdo, $toLocationId);
		} else if(empty($productId) === false) {
			$reply->data = Movement::getMovementByProductId($pdo, $productId);
		} else if(empty($userId) === false) {
			$reply->data = Movement::getMovementByUserId($pdo, $userId);
		} else {
			$reply->data = Movement::getAllMovements($pdo)->toArray();
		}
		// post to a new Movement
	} else if($method === "POST") {
		// convert POSTed JSON to an object
		verifyXsrf();
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);

		$movement = new Movement(null, $requestObject->fromLocationId, $requestObject->toLocationId,
			$requestObject->productId, $requestObject->unitId, $requestObject->userId, $requestObject->cost,
			$requestObject->movementDate, $requestObject->movementType, $requestObject->price);
		$movement->insert($pdo);
		$reply->data = "Movement created OK";
	}

	// create an exception to pass back to the RESTful caller
} catch(Exception $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
	unset($reply->data);
}

header("Content-type: application/json");
echo json_encode($reply);
