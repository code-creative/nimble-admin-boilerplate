<?php
$routing = array(
	"default" => array(
		"controller" => "controllers/system",
		"method" => "index",
		"load" => array(
			array("file"=>"models/users.php","name"=>"users")
		)
	),
	"dashboard" => array(
		"controller" => "controllers/system",
		"method" => "dashboard",
		"load" => array(
			array("file"=>"models/auth.php","name"=>"auth"),
			array("file"=>"models/dashboard.php","name"=>"dashboard")
		)
	),
	"operatives" => array(
		"controller" => "controllers/system",
		"method" => "operatives",
		"load" => array(
			array("file"=>"models/auth.php","name"=>"auth"),
			array("file"=>"models/operative.php","name"=>"operative")
		)
	),
	"operative" => array(
		"controller" => "controllers/system",
		"method" => "operative",
		"load" => array(
			array("file"=>"models/auth.php","name"=>"auth"),
			array("file"=>"models/operative.php","name"=>"operative"),
			array("file"=>"models/users.php","name"=>"users")
		)
	),
	"admin-users" => array(
		"controller" => "controllers/system",
		"method" => "admin_users",
		"load" => array(
			array("file"=>"models/auth.php","name"=>"auth"),
			array("file"=>"models/users.php","name"=>"users")
		)
	),
	"admin-user" => array(
		"controller" => "controllers/system",
		"method" => "admin_user",
		"load" => array(
			array("file"=>"models/auth.php","name"=>"auth"),
			array("file"=>"models/users.php","name"=>"users")
		)
	),
	"admin-user-delete" => array(
		"controller" => "controllers/system",
		"method" => "admin_user_delete",
		"load" => array(
			array("file"=>"models/auth.php","name"=>"auth"),
			array("file"=>"models/users.php","name"=>"users")
		)
	),
	"delete-operative" => array(
		"controller" => "controllers/system",
		"method" => "delete_operative",
		"load" => array(
			array("file"=>"models/auth.php","name"=>"auth"),
			array("file"=>"models/operative.php","name"=>"operative")
		)
	),
	"ajax-user-change-password" => array(
		"controller" => "controllers/system",
		"method" => "ajax_user_change_password",
		"load" => array(
			array("file"=>"models/auth.php","name"=>"auth"),
			array("file"=>"models/users.php","name"=>"users")
		)
	),
	"ajax-operative-change-password" => array(
		"controller" => "controllers/system",
		"method" => "ajax_operative_change_password",
		"load" => array(
			array("file"=>"models/auth.php","name"=>"auth"),
			array("file"=>"models/operative.php","name"=>"operative")
		)
	),
	"logout" => array(
		"controller" => "controllers/system",
		"method" => "logout"
	),
	"test" => array(
		"controller" => "controllers/system",
		"method" => "test"
	)
);