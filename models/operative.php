<?php 

class operative 
{

	function __construct($nb) {
		$this->nb = $nb;
	}


	function login()
	{
		
		$r = array();
		
		$this->nb->validation->add("email",array(
			array("type" => "required", "message" => "Please enter email"),
			array("type" => "valid_email", "message" => "Please enter valid email")
		));	

		$this->nb->validation->add("password",array(
			array("type" => "required", "message" => "Please enter password"),
			array("type" => "min_length[6]", "message" => "Password must be at least 6 character")
		));

		$this->nb->collector->add(
			array(
				array("type" => "POST", "name" => "email", "key" => "email"),
				array("type" => "POST", "name" => "password", "key" => "password", "format" => "_md5")
			)
		);	

		if($this->nb->validation->validate())
		{
			
			$_user = $this->nb->collector->collect();
			$this->nb->collector->clear();
			

			$sql = "
				SELECT 
					*
				FROM 
					operatives
				WHERE
					email = '".$_user["email"]."' 
				AND 
					password = '".$_user['password']."' 
				AND 
				status = 1 
				LIMIT 1
				";

			$check =  $this->nb->db->run($sql);
		
			if(count($check) == 0)
			{
				$this->nb->validation->errors["system"][] = array("message" => 
					"Sorry we could not find your account. Please check your details and try again."
				);
				$r = array("success" => FALSE);
			}else{
				
				$operative_id = $check[0]["id"];
				$auth_key = $this->nb->utils->random_string("unique");
				$check[0]["auth_key"] = $auth_key;
				$check[0]["wolseley_locations"] = $this->nb->config["wolseley_locations"];
				
				$data = array(
					"last_login" => $this->nb->now,
					"auth_key" => $auth_key
				);
				
				$this->nb->db->update("operatives",$data," id = " . $operative_id);
				
				
				$r = array("success" => TRUE,"user"=>$check[0]);
			}

		}else{
			$r = array("success" => FALSE);
		}	

		return $r;
		
	}

	function get_operative($id)
	{
		$r = $this->nb->db->select("operatives"," id = '".$id."' LIMIT 1");
		return $r;
	}
	
	function get_stock_items($id,$profile_id)
	{
		$r = $this->nb->db->select("operatives_stock_items"," operative_id = '".$id."' AND profile_id = '".$profile_id."' AND status = 1 ORDER BY product_code");
		return $r;
	}
	
	function set_operative($id)
	{
		
		$r = array();
		$edit = $this->nb->get_post("edit");

		$this->nb->validation->add("operative_name",array(
			array("type" => "required", "message" => "Please enter operative name")
		));	
		
		$this->nb->validation->add("tel",array(
			array("type" => "required", "message" => "Please enter operative tel")
		));	

		$this->nb->validation->add("email",array(
			array("type" => "required", "message" => "Please enter operative email"),
			array("type" => "valid_email", "message" => "Please enter valid email address")
		));

		if($edit != 1)
		{
			
			$this->nb->validation->add("password",array(
				array("type" => "required", "message" => "Please enter password"),
				array("type" => "min_length[6]", "message" => "Password must be at least 6 character"),
				array("type" => "matches[password_conf]", "message" => "Password doesn't match confimation please try again")
			));	
			
			$this->nb->validation->add("password_conf",array(
				array("type" => "required", "message" => "Please enter password confimation")
			));	

			$this->nb->collector->add(
				array(
					array("type" => "POST", "name" => "password", "key" => "password", "format" => "_md5")
				)
			);	

		}
	
		$this->nb->collector->add(
			array(
				array("type" => "POST", "name" => "operative_name" , "key" => "name"),
				array("type" => "POST", "name" => "tel"),
				array("type" => "POST", "name" => "email"),
				array("type" => "POST", "name" => "manager_id"),
				array("type" => "POST", "name" => "manager_name"),
				array("type" => "POST", "name" => "profile"),
				array("type" => "POST", "name" => "profile_name"),
				array("type" => "POST", "name" => "department"),
				array("type" => "POST", "name" => "default_location")
			)
		);
		
		if($this->nb->validation->validate())
		{
			$_operative = $this->nb->collector->collect();	
			if($id == '')
			{
				$_operative['added_on'] = date('Y-m-d H:i:s');
				$this->nb->db->insert("operatives", $_operative);
				$operative_id = $this->nb->db->lastInsertId();
				$this->nb->profiles->insert_all_operative_items($_operative["profile"],$operative_id);
				$r = array("success" => TRUE);
			}else{
				$_operative['last_edited'] = date('Y-m-d H:i:s');
				$this->nb->db->update("operatives", $_operative,"id = ". $id);
				$this->nb->profiles->update_all_operative_items($_operative["profile"],$id);
				$r = array("success" => TRUE);
			}
			$this->nb->collector->clear();
		}else{
			$r = array("success" => FALSE, "errors" => $this->nb->validation->get_all_errors());
		}
		return $r;
	}

	function list_operatives()
	{

		$name = $this->nb->get_post("operative_name");
		$tel = $this->nb->get_post("tel");
		$order_by = $this->nb->get_post("order_by");

		$where = " status = 1 ";
		$and = " ";

		if($name != "")
		{
			$where .= " AND operative_name LIKE '%".$name."%'";
		}


		if($tel != "")
		{
			$where .= " AND tel LIKE '%".$tel."%'";
		}

		$r = $this->nb->db->select("operatives",$where);
		
		return $r;
 
	}
	
	function get_all_operatives()
	{
		$r = $this->nb->db->select("operatives"," status = 1 ORDER BY name");
		return $r;
	}
		

	function delete_operative($id)
	{
		$this->nb->db->update("operatives",array("status" => "DELETED"), " id = " . $id);
		return TRUE;
	}

	function set_operative_item()
	{
		
		$id = $this->nb->get_post("id");
		$product_description = $this->nb->get_post("product_description");

		
		if($id == "")
		{
			
			$current_qty = $this->nb->get_post("current_qty");
			$starting_qty = $this->nb->get_post("starting_qty");
			$product_code = $this->nb->get_post("product_code");
			$wolseley_code = $this->nb->get_post("wolseley_code");
			$operative_id = $this->nb->get_post("operative_id");
			$profile_id = $this->nb->get_post("profile_id");
			
			
				$insert = array(
					"operative_id" => $operative_id,
					"profile_id" => $profile_id,
					"product_code" => $product_code,
					"wolseley_code" => $wolseley_code,
					"product_description" => $product_description,
					"current_qty" => $current_qty,
					"starting_qty" => $starting_qty,
					"is_profile_item" => 0,
					"is_basket" => 1,
					"added_on" => $this->nb->now
				);
				
				$this->nb->db->insert("operatives_stock_items", $insert);
			
		}else{
			$current_qty = $this->nb->get_post("current_qty");
			$starting_qty = $this->nb->get_post("starting_qty");
			$update = array(
				"product_description" => $product_description,
				"current_qty" => $current_qty,
				"starting_qty" => $starting_qty
			);
			$this->nb->db->update("operatives_stock_items", $update , " id = " . $id);
		}
		
		return array("success"=> true);	
	}
	
	function delete_operative_item()
	{
		
		$id = $this->nb->get_post("id");
		
		$update = array(
			"current_qty" => 0,
			"starting_qty" => 0,
			"profile_id" => 0,
			"is_profile" => 0,
			"status" => -1
		);
		$this->nb->db->update("operatives_stock_items", $update , " id = " . $id);
		return array("success"=> true);	
	}	
	
	function change_password()
	{
		$r = array();
		$id = $this->nb->get_post("id");
		$password = $this->nb->get_post("new_password");
		$this->nb->db->update("operatives", array("password" => md5($password)),"id = ". $id);
		if(count($this->nb->errors) > 0)
		{
			$r["success"] = FALSE;
			$r["errors"] = $this->nb->errors;
			$r["msg"] = array("There has been a problem change this usesrs password please try again");
		}else{
			$r["success"] = TRUE;
			$r["msg"] = array("User password changed successful");
		}
		return $r;
	}
	

	function api_get_stock()
	{
		
		$this->nb->utils->auth();
		
		$r = array();
		$user_id = $this->nb->get_post("user_id");
		$profile_id = $this->nb->get_post("profile_id");
		$stock =  $this->get_stock_items($user_id,$profile_id);
		$stock = (count($stock) > 0)? $stock : array();
		$r["data"] = $stock;
		$r["success"] = true;
		return $r;
	}
	
	function api_set_stock_qty()
	{
		
		$this->nb->utils->auth();
		
		$r = array();
		$item_id = $this->nb->get_post("item_id");
		$new_qty = $this->nb->get_post("new_qty");
		$this->nb->db->update("operatives_stock_items",array("current_qty" => $new_qty)," id = " . $item_id);
		$r["success"] = true;
		return $r;		
	}
	
	function api_get_orders()
	{
		$this->nb->utils->auth();
		$r = array();
		$user_id = $this->nb->get_post("user_id");
		$r["orders"] = $this->nb->db->select("orders"," status = 1 AND ordered_by_id = '".$user_id."' ORDER BY ordered_on");
		$r["order_items"] = $this->nb->db->select("order_items"," status = 1");
		$r["success"] = true;
		return $r;	
	}
	
}