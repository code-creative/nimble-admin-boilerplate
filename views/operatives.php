<br>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<a href="?/operative" class="btn btn-primary pull-right" id="add-operative-btn">Add operative <span class="glyphicon glyphicon-plus"></span> </a>
				<h2>Operatives</h2>
				<br>
				<form class="form-inline tac" method="POST">
				  <div class="form-group">
				    <label for="operative_name">Name</label>
				    <input type="text" class="form-control" id="operative_name" name="operative_name" placeholder="" value="<?=$this->get_post("operative_name")?>">
				  </div>
				  <div class="form-group">
				    <label for="tel">Tel</label>
				    <input type="text" class="form-control" id="tel" name="tel"  placeholder="" value="<?=$this->get_post("tel")?>">
				  </div>
				  <button type="submit" class="btn btn-success">search</button>
				</form>
				<br>
				<table class="table table-bordered">
					<thead>
						<tr>
						<th>#</th>
						<th>Name</th>
						<th>Email</th>
						<th>Tel</th>
						<th>Manager</th>
						<th>Profile</th>
						<th>Department</th>
						<th>Last login</th>
						<th class="tac">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?
					$i = 1;
					foreach($operatives as $operative)
					{
					?>
					<tr>
						<td><?=$i?></td>
						<td><?=$operative["name"];?></td>
						<td><?=$operative["email"];?></td>
						<td><?=$operative["tel"];?></td>
						<td><?=$operative["manager_name"];?></td>
						<td><?=$this->utils->db2Uk($operative["last_login"],true);?></td>
						<td class="tac" style="width:110px;">
							<div class="btn-group pull-right">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									Actions <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<li><a href="?/operative/<?=$operative["id"]?>"><span class="glyphicon glyphicon-edit"></span> Edit</a>
									</li>
									<li><a href="#" data-id="<?=$operative["id"]?>" data-toggle="modal" data-target="#changePassword" class="password-model"><span class="glyphicon glyphicon-lock"></span> Change password</a>
									</li>
									<li><a href="?/delete-operative/<?=$operative["id"]?>" data-confirm="Are you sure you want to delete this users"><span class="glyphicon glyphicon-remove-sign"></span> Delete</a>
									</li>
								</ul>
							</div>
						</td>
					</tr>
					<?
						$i++;
					}  
					?>
				  </tbody>
				</table>	
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="changePassword" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Change Password</h4>
			</div>
			<div class="modal-body">
				<form action="">
					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" class="form-control" id="password" placeholder="">
					</div>
					<div class="form-group">
						<label for="conf_password">Confirm Password</label>
						<input type="password" class="form-control" id="conf_password" placeholder="">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary"  data-dismiss="modal" id="change_password">Save</button>
			</div>
		</div>
	</div>
</div>
<script src="{template_path}js/operatives.js"></script>