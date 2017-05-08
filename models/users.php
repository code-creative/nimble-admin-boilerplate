<?php 

class users 
{

	function __construct($nb) {
		$this->nb = $nb;
	}

	
	function get_user($id)
	{
		$r = $this->nb->db->select("admin_users"," id = '".$id."' LIMIT 1");
		return $r;
	}

	function set_user($id)
	{
		
		$r = array();

		$this->nb->validation->add("user_name",array(
			array("type" => "required", "message" => "Please enter user name")
		));	
		
		$this->nb->validation->add("email",array(
			array("type" => "required", "message" => "Please enter contact email"),
			array("type" => "valid_email", "message" => "Please enter valid email")
		));
		
		if($id == "")
		{
			
			$this->nb->validation->add("password",array(
				array("type" => "required", "message" => "Please enter password"),
				array("type" => "min_length[6]", "message" => "Password must be at least 6 character"),	
				array("type" => "matches[password_conf]", "message" => "Password doesn't match confimation please try again")
			));
			$this->nb->validation->add("password_conf",array(
				array("type" => "required", "message" => "Please enter password confirmation")
			));
			$this->nb->collector->add( array("type" => "POST", "name" => "password", "format" => "_md5") );
		
		}
		
		$this->nb->collector->add(
			array(
				array("type" => "POST", "name" => "user_name", "key" => "name"),
				array("type" => "POST", "name" => "department", "key" => "department"),
				array("type" => "POST", "name" => "email")
			)
		);	
		
		if($this->nb->validation->validate())
		{
			$_user = $this->nb->collector->collect();	
			$_user['user_type'] =  $this->nb->config['user_type'];
			if($id == '')
			{
				$this->nb->db->insert("admin_users", $_user);
				$r = array("success" => TRUE);
			}else{
				$this->nb->db->update("admin_users", $_user,"id = ". $id);
				$r = array("success" => TRUE);
			}
			$this->nb->collector->clear();
		}else{
			$r = array("success" => FALSE, "errors" => $this->nb->validation->get_all_errors());
		}
		
		return $r;
	}

	function list_users()
	{

		$r = $this->nb->db->select("admin_users","status = 1 ORDER BY name");
		return $r;
	
	}
	
	function delete($id)
	{
		$this->nb->db->update("admin_users", array("status" => "-1"),"id = ". $id);
		return TRUE;
	}
	
	function edit()
	{
		$r = array();
		$id = $this->nb->get_post("id");
		$user_name = $this->nb->get_post("user_name");
		$user_email = $this->nb->get_post("user_email");
		$this->nb->db->update("admin_users", array("name" => $user_name,"email" => $user_email),"id = ". $id);
		if(count($this->nb->errors) > 0)
		{
			$r["success"] = FALSE;
			$r["errors"] = $this->nb->errors;
			$r["msg"] = array("There has been a problem editing this user please try again");
		}else{
			$r["success"] = TRUE;
			$r["msg"] = array("User edit successful");
		}
		return $r;
	}
	
	function change_password()
	{
		$r = array();
		$id = $this->nb->get_post("id");
		$password = $this->nb->get_post("new_password");
		$this->nb->db->update("admin_users", array("password" => md5($password)),"id = ". $id);
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
					admin_users
				WHERE
					email = '".$_user["email"]."' 
				AND 
					password = '".$_user['password']."'
				";

			$check =  $this->nb->db->run($sql);

			if(count($check) == 0)
			{
				$this->nb->validation->errors["system"][] = array("message" => 
					"Sorry we could not find your account. Please check your details and try again."
				);
				$r = array("success" => FALSE);
			}else{
				$id = $check[0]["id"];
				$check[0]["operatives"] = $this->get_user_operative_ids($id);
				$this->nb->session->set("user",$check[0]);
				$this->nb->db->update("admin_users", array("last_login" => $this->nb->now), " id = " . $id);
				$r = array("success" => TRUE,"user"=>$check[0]);
			}

		}else{
			$r = array("success" => FALSE);
		}	

		return $r;
		
	}	

	function get_user_operative_ids($user_id)
	{
		$temp = $r = array();
		$r = $this->nb->db->select("operatives", "  manager_id = '$user_id' AND status = 1", "", "id");
		foreach($r as $operative)
		{
			$temp[] = $operative["id"];
		}
		return $temp;
	}


}