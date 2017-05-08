<br>
<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-6">
  	<form action="" method="POST">
		<div class="panel panel-default">
			<div class="panel-body">
				<h2>
					<?if($edit){ echo "Edit " . $this->get_post("user_name"); }else{ echo "Add user"; }?>
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
					<div class="form-group <?=$this->validation->has_error("user_name","has-error");?>">
						<label for="user_name">User name</label>
						<input type="text" class="form-control" id="user_name" name="user_name" value="<?=$this->get_post("user_name")?>">
					</div>
					<div class="form-group <?=$this->validation->has_error("email","has-error");?>">
						<label for="email">Email</label>
						<input type="text" class="form-control" id="email" name="email" value="<?=$this->get_post("email")?>">
					</div>
					<div class="form-group">
						<label for="department">Departments</label>
						<select name="department" id="department" class="form-control">
							<?foreach ($this->config["departments"] as $key => $value) {?>
								<option value="<?=$key?>" <?=($this->get_post("department") == $key)? 'selected="selected"' : '';?>><?=$value?></option>
							<?}?>
						</select>
					</div>
					<?if(!$edit){?>
					<div class="form-group <?=$this->validation->has_error("password","has-error");?>">
						<label for="password">Password</label>
						<input type="password" class="form-control" id="password" name="password" value="<?=$this->get_post("password")?>">
					</div>
					
					<div class="form-group <?=$this->validation->has_error("password_conf","has-error");?>">
						<label for="email">Confirm</label>
						<input type="password" class="form-control" id="password_conf" name="password_conf" value="<?=$this->get_post("password_conf")?>">
					</div>
					<?}?>

					<input type="hidden" name="edit" value="<?=$edit;?>">
					<button type="submit" class="btn btn-success btn-lg btn-block" data-toggle="modal" data-target="#confirmModal" id="stock-submit-btn">Submit</button>
				</form>
			</div>
		</div>
	</form>
  </div>  
  <div class="col-md-3"></div>
</div>