<br>
<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-6">
  	<form action="" method="POST">
		<div class="panel panel-default">
			<div class="panel-body">
				<h2>
					<?if($edit){ echo "Edit " . $operative["operative_name"]; }else{ echo "Add operative"; }?>
				</h2>
				<br>
				<?
					if($errors != "")
					{
				?>
					<div class="alert alert-danger" role="alert"><?=$errors;?></div>
				<?
					}
				?>
				<form id="stockForm" method="POST">
					
					<div class="form-group <?=$this->validation->has_error("operative_name","has-error");?>">
						<label for="operative_name">Operative name</label>
						<input type="text" class="form-control" id="operative_name" name="operative_name" value="<?=$this->get_post("operative_name")?>">
					</div>
					<div class="form-group <?=$this->validation->has_error("tel","has-error");?>">
						<label for="tel">Tel</label>
						<input type="text" class="form-control" id="tel" name="tel" value="<?=$this->get_post("tel")?>">
					</div>
					<div class="form-group <?=$this->validation->has_error("email","has-error");?>">
						<label for="email">Email</label>
						<input type="text" class="form-control" id="email" name="email" value="<?=$this->get_post("email")?>">
					</div>
					
					
					<div class="form-group">
						<label for="manager_id">Manager</label>
						<select name="manager_id" id="manager_id" class="form-control">
							<?foreach ($managers as $manager) {?>
								<option value="<?=$manager['id']?>" <?=($this->get_post("manager_id") == $manager['id'])? 'selected="selected"' : '';?>><?=$manager['name']?></option>
							<?}?>
						</select>
						<input type="hidden" id="manager_name" name="manager_name" value="">
					</div>
					
					<?if($edit == false){?>
					
					<div class="form-group <?=$this->validation->has_error("password","has-error");?>">
						<label for="password">Password</label>
						<input type="text" class="form-control" id="password" name="password" value="<?=$this->get_post("password")?>">
					</div>
					
					<div class="form-group <?=$this->validation->has_error("password_conf","has-error");?>">
						<label for="password_conf">Confirm Password</label>
						<input type="text" class="form-control" id="password_conf" name="password_conf" value="<?=$this->get_post("password_conf")?>">
					</div>
					
					<?}?>
						
						<input type="hidden" id="edit" name="edit" value="<?=$edit?>">

					<button type="submit" class="btn btn-success btn-lg btn-block" data-toggle="modal" data-target="#confirmModal" id="stock-submit-btn">Submit</button>
				</form>
			</div>
		</div>
	</form>
  </div>  
  <div class="col-md-3"></div>
</div>

<script>
	
	$(document).ready(function(){
		$("#manager_id").change(function(){
			var manager_name = $(this).find("option:selected").text();
			$("#manager_name").val(manager_name);
		}).trigger("change");
		$("#profile").change(function(){
			var profile_name = $(this).find("option:selected").text();
			$("#profile_name").val(profile_name);
		}).trigger("change");
	});

</script>
