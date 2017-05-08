<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?=$this->config["app_title"]?></title>
    <!-- Bootstrap core CSS -->
    <link href="{template_path}css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{template_path}css/bootstrap-datepicker.min.css">
    <link href="{template_path}css/select2.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="{template_path}css/dashboard.css" rel="stylesheet">
    <link href="{template_path}css/font-awesome.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<?
	$user = array();
	if($this->logged_in)
	{
		$user = $this->session->get("user");
	}
?>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
				<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
						</button>
						<span class="navbar-brand" href="#"><?=$this->config["app_title"]?> <sup><? if($this->logged_in) { echo $user["name"]; }?></sup></a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
				<? if($this->logged_in) { ?>         
				<ul class="nav navbar-nav navbar-right">
					<li class="<?=($current_page == 'dashboard')? 'active ' : '';?>"><a href="?/dashboard">Dashboard</a></li>
					<li class="<?=($current_page == 'operatives')? 'active ' : '';?>"><a href="?/operatives">Operatives</a></li>
					<li class="<?=($current_page == 'admin-users')? 'active ' : '';?>"><a href="?/admin-users">Admin users</a></li>
					<li><a href="?/logout">Logout</a></li>
				</ul>
				<?}else{?>
				<ul class="nav navbar-nav navbar-right">
					<li class="active"><a href="?/logout">Login</a></li>
				</ul>
				<?}?>
			</div>
		</div>
	</nav>
		<script src="{template_path}js/jquery-2.1.3.min.js"></script>
		<script src="{template_path}js/bootstrap.min.js"></script>
		<script src="{template_path}js/bootstrap-datepicker.min.js"></script>
		<script src="{template_path}js/select2.min.js"></script>
		<script src="{template_path}js/typeahead.jquery.min.js"></script>
		<script src="{template_path}js/moment.min.js"></script>
		<script src="{template_path}js/onload.js"></script>
		<div class="container-fluid">
		<!-- MAIN CONTENT [START] -->
			{page_content}
		<!-- MAIN CONTENT [END] -->
		</div>
</body>
</html>
