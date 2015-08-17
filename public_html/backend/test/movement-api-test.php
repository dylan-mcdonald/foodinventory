<?php
// grab the project test parameters
require_once("inventorytext.php");

// grab the autoloader for all Composer classes
require_once(dirname(dirname(dirname(__DIR__))) . "/vendor/autoload.php");

// grab the class(s) under scrutiny
require_once(dirname(__DIR__) . "/php/classes/autoload.php");

/**
 * Full PHPUnit test for the Movement API
 *
 * This is a complete PHPUnit test of the Movement API.
 * It is complete because *ALL* mySQL/PDO enabled methods
 * are tested for both invalid and valid inputs.
 *
 * @see Movement/index.php
 * @author Christopher Collopy <ccollopy@cnm.edu>
 **/
class MovementAPITest extends InventoryTextTest {
	/**
	 * valid cost to use
	 * @var float $VALID_cost
	 **/
	protected $VALID_cost = 3.50;

	/**
	 * invalid cost to use
	 * @var float $INVALID_cost
	 **/
	protected $INVALID_cost = 4.75689;

	/**
	 * valid movementDate to use
	 * @var DateTime $VALID_movementDate
	 **/
	protected $VALID_movementDate = null;

	/**
	 * invalid movementDate to use
	 * @var DateTime $INVALID_movementDate
	 **/
	protected $INVALID_movementDate = "2015/26/09 14:25:50";

	/**
	 * valid movementType to use
	 * @var string $VALID_movementType
	 **/
	protected $VALID_movementType = "TR";

	/**
	 * invalid movementType to use
	 * @var string $INVALID_movementType
	 **/
	protected $INVALID_movementType = "RET";

	/**
	 * valid price to use
	 * @var float $VALID_price
	 **/
	protected $VALID_price = 5.75;

	/**
	 * invalid price to use
	 * @var float $INVALID_price
	 **/
	protected $INVALID_price = 4.75689;

	/**
	 * programatic web platform
	 * @var Guzzle $guzzle
	 **/
	protected $guzzle = null;

	/**
	 * creating a null fromLocation
	 * object for global scope
	 * @var Location $fromLocation
	 **/
	protected $fromLocation = null;

	/**
	 * creating a null toLocation
	 * object for global scope
	 * @var Location $toLocation
	 **/
	protected $toLocation = null;

	/**
	 * creating a null Product
	 * object for global scope
	 * @var Product $product
	 **/
	protected $product = null;

	/**
	 * creating a null UnitOfMeasure
	 * object for global scope
	 * @var UnitOfMeasure $unitOfMeasure
	 **/
	protected $unitOfMeasure = null;

	/**
	 * creating a null User
	 * object for global scope
	 * @var User $user
	 **/
	protected $user = null;


	public function setUp() {
		parent::setUp();

		$this->guzzle = new \GuzzleHttp\Client(['cookies' => true]);
		$this->VALID_movementDate = 20150926084525;

		$userId = null;
		$firstName = "Jim";
		$lastName = "Jim";
		$root = 1;
		$attention = "Urgent: ";
		$addressLineOne = "123 House St.";
		$addressLineTwo = "P.O Box. 9965";
		$city = "Tattoine";
		$state = "AK";
		$zipCode = "52467";
		$email = "jim@naboomail.nb";
		$phoneNumber = "5052253231";
		$salt = bin2hex(openssl_random_pseudo_bytes(32));
		$hash = hash_pbkdf2("sha512","password1234", $salt,262144, 128);

		$this->user = new User($userId, $lastName, $firstName, $root, $attention, $addressLineOne, $addressLineTwo, $city, $state, $zipCode, $email, $phoneNumber, $salt, $hash);
		$this->user->insert($this->getPDO());

		$vendorId = null;
		$contactName = "Trevor Rigler";
		$vendorEmail = "trier@cnm.edu";
		$vendorName = "TruFork";
		$vendorPhoneNumber = "5053594687";

		$vendor = new Vendor($vendorId, $contactName, $vendorEmail, $vendorName, $vendorPhoneNumber);
		$vendor->insert($this->getPDO());

		$productId = null;
		$vendorId = $vendor->getVendorId();
		$description = "A glorius bead to use";
		$leadTime = 10;
		$sku = "TGT354";
		$title = "Bead-Green-Blue-Circular";

		$this->product = new Product($productId, $vendorId, $description, $leadTime, $sku, $title);
		$this->product->insert($this->getPDO());

		$locationId = null;
		$description = "Back Stock";
		$storageCode = 13;

		$this->fromLocation = new Location($locationId, $storageCode, $description);
		$this->fromLocation->insert($this->getPDO());

		$locationId = null;
		$description = "Front Stock";
		$storageCode = 12;

		$this->toLocation = new Location($locationId, $storageCode, $description);
		$this->toLocation->insert($this->getPDO());

		$unitId = null;
		$unitCode = "pk";
		$quantity = 10.50;

		$this->unitOfMeasure = new UnitOfMeasure($unitId, $unitCode, $quantity);
		$this->unitOfMeasure->insert($this->getPDO());
	}

	/**
	 * test grabbing a Movement by valid movementId
	 **/
	public function testGetValidMovementByMovementId() {
		// create a new Movement
		$newMovement = new Movement(null, $this->fromLocation->getLocationId(), $this->toLocation->getLocationId(), $this->product->getProductId(), $this->unitOfMeasure->getUnitId(), $this->user->getUserId(), $this->VALID_cost, $this->VALID_movementDate, $this->VALID_movementType, $this->VALID_price);
		$newMovement->insert($this->getPDO());

		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?movementId=' . $newMovement->getMovementId());
		var_dump($this->guzzle);
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by invalid movementId
	 **/
	public function testGetInvalidMovementByMovementId() {
		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?movementId=' . InventoryTextTest::INVALID_KEY);
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}
	/**
	 * test grabbing a Movement by valid fromLocationId
	 **/
	public function testGetValidMovementByFromLocationId() {
		// create a new Movement
		$newMovement = new Movement(null, $this->fromLocation->getLocationId(), $this->toLocation->getLocationId(), $this->product->getProductId(), $this->unitOfMeasure->getUnitId(), $this->user->getUserId(), $this->VALID_cost, $this->VALID_movementDate, $this->VALID_movementType, $this->VALID_price);
		$newMovement->insert($this->getPDO());

		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?movementId=' . $newMovement->getFromLocationId());
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by invalid fromLocationId
	 **/
	public function testGetInvalidMovementByFromLocationId() {
		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?fromLocationId=' . InventoryTextTest::INVALID_KEY);
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by valid toLocationId
	 **/
	public function testGetValidMovementByToLocationId() {
		// create a new Movement
		$newMovement = new Movement(null, $this->fromLocation->getLocationId(), $this->toLocation->getLocationId(), $this->product->getProductId(), $this->unitOfMeasure->getUnitId(), $this->user->getUserId(), $this->VALID_cost, $this->VALID_movementDate, $this->VALID_movementType, $this->VALID_price);
		$newMovement->insert($this->getPDO());

		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?movementId=' . $newMovement->getToLocationId());
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by invalid toLocationId
	 **/
	public function testGetInvalidMovementByToLocationId() {
		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?fromLocationId=' . InventoryTextTest::INVALID_KEY);
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by valid productId
	 **/
	public function testGetValidMovementByProductId() {
		// create a new Movement
		$newMovement = new Movement(null, $this->fromLocation->getLocationId(), $this->toLocation->getLocationId(), $this->product->getProductId(), $this->unitOfMeasure->getUnitId(), $this->user->getUserId(), $this->VALID_cost, $this->VALID_movementDate, $this->VALID_movementType, $this->VALID_price);$newMovement->insert($this->getPDO());
		$newMovement->insert($this->getPDO());

		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?movementId=' . $newMovement->getProductId());
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by invalid productId
	 **/
	public function testGetInvalidMovementByProductId() {
		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?fromLocationId=' . InventoryTextTest::INVALID_KEY);
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by valid userId
	 **/
	public function testGetValidMovementByUserId() {
		// create a new Movement
		$newMovement = new Movement(null, $this->fromLocation->getLocationId(), $this->toLocation->getLocationId(), $this->product->getProductId(), $this->unitOfMeasure->getUnitId(), $this->user->getUserId(), $this->VALID_cost, $this->VALID_movementDate, $this->VALID_movementType, $this->VALID_price);
		$newMovement->insert($this->getPDO());

		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?movementId=' . $newMovement->getUserId());
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by invalid userId
	 **/
	public function testGetInvalidMovementByUserId() {
		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?fromLocationId=' . InventoryTextTest::INVALID_KEY);
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by valid movementDate
	 **/
	public function testGetValidMovementByMovementDate() {
		// create a new Movement
		$newMovement = new Movement(null, $this->fromLocation->getLocationId(), $this->toLocation->getLocationId(), $this->product->getProductId(), $this->unitOfMeasure->getUnitId(), $this->user->getUserId(), $this->VALID_cost, $this->VALID_movementDate, $this->VALID_movementType, $this->VALID_price);
		$newMovement->insert($this->getPDO());

		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?movementId=' . $newMovement->getMovementDate());
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by invalid movementDate
	 **/
	public function testGetInvalidMovementByMovementDate() {
		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?fromLocationId=' . $this->INVALID_movementDate);
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by valid movementType
	 **/
	public function testGetValidMovementByMovementType() {
		// create a new Movement
		$newMovement = new Movement(null, $this->fromLocation->getLocationId(), $this->toLocation->getLocationId(), $this->product->getProductId(), $this->unitOfMeasure->getUnitId(), $this->user->getUserId(), $this->VALID_cost, $this->VALID_movementDate, $this->VALID_movementType, $this->VALID_price);
		$newMovement->insert($this->getPDO());

		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?movementId=' . $newMovement->getMovementType());
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test grabbing a Movement by invalid movementType
	 **/
	public function testGetInvalidMovementByMovementType() {
		// grab the data from guzzle and enforce the status' match our expectations
		$response = $this->guzzle->get('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/?fromLocationId=' . $this->INVALID_movementType);
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		$this->assertSame(200, $movement->status);
	}

	/**
	 * test deleting a Movement
	 **/
	public function testPostValidMovement() {
		// create a new Movement
		$newMovement = new Movement(null, $this->fromLocation->getLocationId(), $this->toLocation->getLocationId(), $this->product->getProductId(), $this->unitOfMeasure->getUnitId(), $this->user->getUserId(), $this->VALID_cost, $this->VALID_movementDate, $this->VALID_movementType, $this->VALID_price);
		$newMovement->insert($this->getPDO());

		// grab the data from guzzle and enforce the status' match our expectations

		$response = $this->guzzle->post('https://bootcamp-coders.cnm.edu/~invtext/backend/php/api/movement/', ['json' => json_encode($newMovement)]);
		$this->assertSame($response->getStatusCode(), 200);
		$body = $response->getBody();
		$movement = json_decode($body);
		var_dump($movement->message);
		$this->assertSame(200, $movement->status);

		// delete Movement from mySQL
		$movement->delete($this->getPDO());
	}
}