<form class="form-signin" method="POST" action="">
<h2 class="form-signin-heading">Please login</h2>
<?if($login_errors != ""){?>
	<div class="alert alert-danger" role="alert"><?=$login_errors;?></div>
<?}?>
 <div class="form-group <?=$this->validation->has_error("email","has-error");?>">
    <label for="email">Email</label>
    <input type="text" class="form-control" id="email" name="email" placeholder="Enter email" value="<?=$this->get_post("email")?>">
  </div>
  <div class="form-group <?=$this->validation->has_error("email","has-error");?>">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="off" value="<?=$this->get_post("password")?>">
  </div>
<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
</form>
