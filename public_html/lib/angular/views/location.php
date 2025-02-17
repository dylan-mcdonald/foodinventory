<!--  Location Container  -->
<div class="list-group">
	<!--  Location Container  -->
	<div class="list-group-item">
		<h3> Your <em>Location</em></h3>

		<!--  Location Buttons -->
		<div class="location button row">
			<div class="col-md-4">
				<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#LocationModal">
					Add <br> +
				</button>
			</div>
			<div class="col-md-4">
				<button class="btn btn-default" ng-click="editLocation(location)" value="Edit"> E </button>
			</div>
			<div class="col-md-4">
				<button class="btn btn-default" ng-click="deleteLocation(location)" value="Delete"> - </button>
			</div>
		</div>

		<!--  Location Reports -->
		<div class="location reports row">
			<h4>Reports</h4>
			<div class="col-md-12">
				<table id="example" class="table table-striped table-bordered" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Product</th>
							<th>Storage Code</th>
							<th>description</th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<th>Product</th>
							<th>Storage Code</th>
							<th>description</th>
						</tr>
					</tfoot>

					<tbody>
						<tr ng-repeat="location in locations">
							<td>{{ location.productId }}</td>
							<td>{{ location.stroageCode }}</td>
							<td>{{ location.decription }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<!--  Add Location Modal -->
		<div class="modal fade" id="LocationModal">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						<h3 class="modal-title">Add a Location</h3>
					</div>

					<div class="modal-body" ng-controller="LocationController">
						<form class="form-horizontal" ng-submit="addLocation(location);">
							<div class="form-group">
								<label for="storageCode" class="col-sm-3 control-label">Storage Code:</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="storageCode" name="storageCode" placeholder="e.g. BR for (Back Room)" ng-model="location.storageCode"/>
								</div>
							</div>
							<div class="form-group">
								<label for="description" class="col-sm-3 control-label">Description:</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="description" name="description" placeholder="e.g. Back room of linda's house" ng-model="location.description"/>
								</div>
							</div>
						</form>
						<pre>form = {{ location | json }}</pre>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Sign-Up</button>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>