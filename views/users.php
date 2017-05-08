<br>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body">

				<a href="?/admin-user" class="btn btn-primary pull-right" id="add-users-btn">Add user <span class="glyphicon glyphicon-plus"></span> </a>

				<h2>Admin Users</h2>
				<br>
				<table class="table table-bordered">
				  <thead>
					  <tr>
						  <th style="width:50px;">#</th>
						  <th>Name</th>
						  <th>Email</th>
						  <th class="tac" style="width:50px;">Edit</th>
						  <th class="tac" style="width:110px;">Actions</th>
					  </tr>
				  </thead>
				  <tbody>
					<?
					$i = 1;
					if(count($users) > 0){
					foreach($users as $user)
					{
					?>
					<tr>
						<td><?=$i?></td>
						<td><?=$user["name"];?></td>
						<td><?=$user["email"];?></td>
						<td class="tac"><a href="?/admin-user/<?=$user["id"]?>" class="btn btn-default" data-id="<?=$user["id"]?>" > <span class="glyphicon glyphicon-edit"></span></a> </td>
						<td class="tac" style="width:110px;">
							<div class="btn-group pull-right">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									Actions <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<li><a href="?/admin-user-delete/<?=$user["id"]?>" data-confirm="Are you sure you want to delete this users"><span class="glyphicon glyphicon-remove-sign"></span> Delete</a>
									</li>
									<li><a href="#" data-id="<?=$user["id"]?>" data-toggle="modal" data-target="#changePassword" class="password-model"><span class="glyphicon glyphicon-lock"></span> Change password</a>
									</li>
								</ul>
							</div>
						</td>
					</tr>
					<?
						$i++;
					}  
					}else{
					?>
					<tr>
						<td class="tac" colspan="5">
							No admin users found
						</td>
					</tr>
					<?
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

<script>
	
	$(document).ready(function(){
		
		
		$(".password-model").click(function(){
			var user_id = $(this).attr("data-id");
			$("body").attr("data-selected-user",user_id);
			return true;
		});
		
		$("#changePassword").on("show.bs.modal",function(){
			$("#password").val("");
			$("#conf_password").val("");
		});
		
		$("#change_password").click(function(){
			var user_id = $("body").attr("data-selected-user"),  p = $("#password").val(),
				p_conf = $("#conf_password").val(),
				error = [];
			
				if(p == "")
				{
					error.push("Please enter password");	
				}
			
				if(p_conf == "")
				{
					error.push("Please confirm password");
				}
			
				if(p != p_conf)
				{
					error.push("The password you entered needs to match confirmation");
				}
			
				if(error.length > 0)
				{
					alert(error.join("\n"));
					return false;
				}else{
					$.post("?/ajax-user-change-password",{
						id:user_id,
						new_password:p
					},function(d){	
						alert(d.msg.join("\n"));
					},"json");
					
				}
			
			 $("body").removeAttr("data-selected-user")
		});
		
	});

</script>