<!DOCTYPE html>
<html lang="en" ng-app="FoodInventory">
	<head>
		<meta charset="utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>

		<!-- Bootstrap Latest compiled and minified CSS -->
		<link type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>

		<!-- Optional Bootstrap theme -->
		<link type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" rel="stylesheet"/>

		<!-- LINK TO YOUR CUSTOM CSS FILES HERE -->
		<link type="text/css" href="../lib/css/styles.css" rel="stylesheet"/>

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script type="text/javascript" src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js"></script>
		<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
		<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.12.1/additional-methods.min.js"></script>

		<!-- Latest compiled and minified Bootstrap JavaScript, all compiled plugins included -->
		<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

		<!-- angular.js -->
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.3/angular.min.js"></script>
		<script type="text/javascript" src="../food-inventory.js"></script>
		<script type="text/javascript" src="../services/notification.js"></script>
		<script type="text/javascript" src="../controllers/notification.js"></script>

		<!-- Page Title -->
		<title>Administration</title>
	</head>

	<body>
		<!--  Store Header  -->
		<header>
			<h1 class="text-center">Admin Panel</h1>
			<h2 class="text-center">Do Your Thing</h2>
		</header>

		<!-- Admin Tabs  -->
		<section ng-controller="TabController as tab">
			<ul class="nav nav-pills">
				<li ng-class="{ active:tab.isSet(1) }">
					<a href="" ng-click="tab.setTab(1)">Product</a>
				</li>
				<li ng-class="{ active:tab.isSet(2) }">
					<a href="" ng-click="tab.setTab(2)">Movement</a>
				</li>
				<li ng-class="{ active:tab.isSet(3) }">
					<a href="" ng-click="tab.setTab(2)">Vendor</a>
				</li>
				<li ng-class="{ active:tab.isSet(4) }">
					<a href="" ng-click="tab.setTab(2)">Location</a>
				</li>
				<li ng-class="{ active:tab.isSet(5) }">
					<a href="" ng-click="tab.setTab(2)">Notification</a>
				</li>
			</ul>

			<!--  Product Tab's Contents  -->
			<div ng-show="tab.isSet(1)">
				<product></product>
			</div>

			<!--  Movement Tab's Contents  -->
			<div ng-show="tab.isSet(2)">
				<movement>
					<!--  Movements Container  -->
					<div class="list-group">
						<!--  Movement Container  -->
						<div class="list-group-item" ng-repeat="movement in movements">
							<h3> Movement <em>Number One</em></h3>

							<!--  Movement Buttons -->
							<div class="movement button row">
								<div class="col-md-4">
									<button class="btn btn-default" ng-click="addMovement(movement)" value="ADD">+</button>
								</div>
								<div class="col-md-4">
									<button class="btn btn-default" ng-click="editMovement(movement)" value="Edit">E</button>
								</div>
								<div class="col-md-4">
									<button class="btn btn-default" ng-click="deleteMovement(movement)" value="Delete">-</button>
								</div>
							</div>


							<ul>
								<h4>Reports</h4>
								<li ng-repeat="product in movement.products">
									<blockquote>
										<strong>{{product.title}} </strong>
										{{product.description}}
										<cite class="clearfix">-{{product.sku}} on {{product.leadtime}}</cite>
									</blockquote>
								</li>
							</ul>

							<!--  Review Form -->
							<form name="MovementForm" class="container" ng-controller="MovementController as MovementCtrl" ng-submit="MovementForm.$valid && MovementCtrl.addMovement(movement)" novalidate>

								<!--  Live Preview -->
								<blockquote ng-show="review">
									<strong>{{reviewCtrl.review.stars}} Stars</strong>
									{{reviewCtrl.review.body}}
									<cite class="clearfix">-{{reviewCtrl.review.author}}</cite>
								</blockquote>

								<!--  Review Form -->
								<h4>Submit a Movement</h4>
								<fieldset class="form-group">
									<input ng-model="MovementCtrl.cost" type="text" class="form-control" placeholder="119.95" title="Cost" required />
								</fieldset>
								<fieldset class="form-group">
									<input ng-model="MovementCtrl.movementDate" type="date" class="form-control" placeholder="08/24/2015" title="Movement Date" required />
								</fieldset>
								<fieldset class="form-group">
									<input ng-model="MovementCtrl.movementType" type="text" class="form-control" placeholder="RM" title="Movement Type" required />
								</fieldset>
								<fieldset class="form-group">
									<input ng-model="MovementCtrl.price" type="text" class="form-control" placeholder="139.95" title="Price" required />
								</fieldset>
								<fieldset class="form-group">
									<div> MovementForm is {{movementForm.$valid}}</div>
									<input type="submit" class="btn btn-primary pull-right" value="Submit Movement" />
								</fieldset>
							</form>
						</div>
					</div>
				</movement>
			</div>

			<!--  Vendor Tab's Contents  -->
			<div ng-show="tab.isSet(3)">
				<vendor></vendor>
			</div>

			<!--  Location Tab's Contents  -->
			<div ng-show="tab.isSet(4)">
				<location></location>
			</div>

			<!--  Notification Tab's Contents  -->
			<div ng-show="tab.isSet(5)">
				<notification>
					<!--  Notifications Container  -->
					<div class="list-group">
						<!--  Notification Container  -->
						<div class="list-group-item" ng-repeat="notification in notifications">
							<h3> Notification <em>Number One</em></h3>

							<!--  Notification Buttons -->
							<div class="notification button row">
								<div class="col-md-4">
									<button class="btn btn-default" ng-click="addNotification(notification)" value="ADD">+</button>
								</div>
								<div class="col-md-4">
									<button class="btn btn-default" ng-click="editNotification(notification)" value="Edit">E</button>
								</div>
								<div class="col-md-4">
									<button class="btn btn-default" ng-click="deleteNotification(notification)" value="Delete">-</button>
								</div>
							</div>


							<ul>
								<h4>Reports</h4>
								<li ng-repeat="product in movement.products">
									<blockquote>
										<strong>{{product.title}} </strong>
										{{product.description}}
										<cite class="clearfix">-{{product.sku}} on {{product.leadtime}}</cite>
									</blockquote>
								</li>
							</ul>

							<!--  Review Form -->
							<form name="MovementForm" class="container" ng-controller="MovementController as MovementCtrl" ng-submit="MovementForm.$valid && MovementCtrl.addMovement(movement)" novalidate>

								<!--  Live Preview -->
								<blockquote ng-show="review">
									<strong>{{reviewCtrl.review.stars}} Stars</strong>
									{{reviewCtrl.review.body}}
									<cite class="clearfix">-{{reviewCtrl.review.author}}</cite>
								</blockquote>

								<!--  Review Form -->
								<h4>Submit a Movement</h4>
								<fieldset class="form-group">
									<input ng-model="MovementCtrl.cost" type="text" class="form-control" placeholder="119.95" title="Cost" required />
								</fieldset>
								<fieldset class="form-group">
									<input ng-model="MovementCtrl.movementDate" type="date" class="form-control" placeholder="08/24/2015" title="Movement Date" required />
								</fieldset>
								<fieldset class="form-group">
									<input ng-model="MovementCtrl.movementType" type="text" class="form-control" placeholder="RM" title="Movement Type" required />
								</fieldset>
								<fieldset class="form-group">
									<input ng-model="MovementCtrl.price" type="text" class="form-control" placeholder="139.95" title="Price" required />
								</fieldset>
								<fieldset class="form-group">
									<div> MovementForm is {{movementForm.$valid}}</div>
									<input type="submit" class="btn btn-primary pull-right" value="Submit Movement" />
								</fieldset>
							</form>
						</div>
					</div>
				</notification>
			</div>

	</body>
</html>