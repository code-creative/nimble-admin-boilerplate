<?php
class system
{

	function __construct($nb)
	{
		$this->nb = $nb;
	}

	function index()
	{
		
		$view_data = array();
		$view_data["login_errors"] = "";
		
		if($this->nb->is_post)
		{
			$r = $this->nb->users->login();
			if($r["success"] == true)
			{
				header( 'Location: ?/dashboard' ) ;
			}else{
				if(count($this->nb->validation->errors) > 0)
				{
					$view_data["login_errors"] = $this->nb->validation->get_all_errors();
				}
			}
		}
		
		$this->nb->template->set_base("views/template.php");
		$this->nb->template->parse_template();
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		$this->nb->template->add_view("{page_content}","views/user-login.php",$view_data);		
		$this->nb->template->render();
		echo $this->nb->template->get_base();
		
	}

	function dashboard()
	{
		$this->nb->template->set_base("views/template.php");
		$this->nb->template->parse_template();
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		$this->nb->template->add_view("{page_content}","views/user-dashboard.php");		
		$this->nb->template->render();
		echo $this->nb->template->get_base();		
	}

	function operatives()
	{
		$view_data = $template_data = array();
		$template_data["current_page"] = "operatives";
		if($this->nb->is_post)
		{
			$search_store = array(
				"search_name" => $this->nb->get_post("search_name"),
				"tel" => $this->nb->get_post("tel")
			);
			$this->nb->session->set("search_store",json_encode($search_store));
		}else{
			$search_store = $this->nb->session->get("search_store");
			if($search_store)
			{
				$search_store = json_decode($search_store);
				foreach ($search_store as $key => $value) {
					if($value != "")
					{
						$this->nb->post[$key] = $value;
					}
				}
			}
		}
		
		$view_data["operatives"] = $this->nb->operative->list_operatives();
		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		$this->nb->template->add_view("{page_content}","views/operatives.php",$view_data);		
		$this->nb->template->render();
		echo $this->nb->template->get_base();	
	}

	function operative()
	{
		$id = $this->nb->get_uri(1);
		$view_data = $template_data = array();
		$view_data['errors'] = "";
		$view_data["id"] = $id;
		$view_data['operative'] = array();
		$view_data["edit"] = FALSE;
		$view_data["edit_password"] = FALSE;
		$view_data["managers"] = $this->nb->users->list_users();
		if($this->nb->is_post)
		{
			$r = $this->nb->operative->set_operative($id);
			if($r["success"] == true)
			{
				header( 'Location: ?/operatives' ) ;
			}else{
				$view_data["errors"] = $r["errors"];
			}
		}else{
			
			if($id != "")
			{
				$operative = $this->nb->operative->get_operative($id);
				if(count($operative) == 0)
				{
					header( 'Location: ?/operatives' ) ;
				}else{
					$operative = $operative[0];
					$operative["operative_name"] = $operative["name"];
					unset($operative["name"]);
					foreach ($operative as $key => $value) {
						$this->nb->post[$key] = $value;
					}
					$view_data['operative'] = $operative;
				}
				$view_data["edit"] = TRUE;
			}
		}
		
		$template_data["current_page"] = "operatives";
		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		$this->nb->template->add_view("{page_content}","views/operative.php",$view_data);		
		$this->nb->template->render();
		echo $this->nb->template->get_base();	
		
	}

	function delete_operative()
	{
		$id = $this->nb->get_uri(1);
		$r = $this->nb->operative->delete_operative($id);
		if($r)
		{
			header( 'Location: ?/operatives') ;
		}
	}
	
	
	
	function admin_users()
	{
		$view_data = $template_data = array();	
		$template_data["current_page"] = "admin-users";
		$view_data["users"] = $this->nb->users->list_users();
		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		$this->nb->template->add_view("{page_content}","views/users.php",$view_data);		
		$this->nb->template->render();
		echo $this->nb->template->get_base();	
		
	}
	
	function admin_user()
	{
		$id = $this->nb->get_uri(1);	
		$view_data = $template_data = array();
		$view_data["errors"] = "";
		$template_data["current_page"] = "admin-users";
		if($this->nb->is_post)
		{
			$r = $this->nb->users->set_user($id);
			if($r["success"] == true)
			{
				header( 'Location: ?/admin-users' ) ;
			}else{
				$view_data["errors"] = $r["errors"];
			}
		}
		if($id != "")
		{
			$view_data["edit"] = TRUE;
			$r = $this->nb->users->get_user($id);
			$r = $r[0];
			$r["user_name"] = $r["name"];
			unset($r['name']);
			unset($r['password']);
			foreach ($r as $key => $value) {
				$this->nb->post[$key] = $value;
			}
		}else{
			$view_data["edit"] = FALSE;
		}
		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		$this->nb->template->add_view("{page_content}","views/user.php",$view_data);		
		$this->nb->template->render();
		echo $this->nb->template->get_base();	
	}
	
	function admin_user_delete()
	{
		$id = $this->nb->get_uri(1);
		$r = $this->nb->users->delete($id);
		if($r)
		{
			header( 'Location: ?/admin-users') ;
		}
	}
	
	function items()
	{
		
		$list_type = $this->nb->get_uri(1);

		$view_data = $template_data = array();
		$template_data["current_page"] = "items";
		$link = "";
		
		if($this->nb->is_post)
		{
			$item_id = $this->nb->get_post("item_id");
			if(!empty($_FILES['image']['name'])){
				$upload_img = $this->nb->utils->upload('image','basket_images/images/',$item_id,TRUE,'basket_images/thumbs/','100','100');
				$this->nb->db->update("basket_items2",array("img_ref"=>$upload_img)," id = " . $item_id);
			}
		}
		
		if($list_type == "master")
		{
			$view_data["items"] = $this->nb->items->list_master_items();
			$link = "?/items/master/";
		}else{
			$view_data["items"] = $this->nb->items->list_basket_items2();
			$link = "?/items/basket/";
		}
		
		$view_data["page_links"] = $this->nb->paginator->build_links($link,true);
		
		$view_data["list_type"] = $list_type;
		$view_data["current_page"] = $this->nb->get_uri(2);
		$view_data["search_term"] = $this->nb->get_uri(3);

		$this->nb->template->set_base("views/template.php",$template_data);
		
		$this->nb->template->parse_template();
		
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		
		$this->nb->template->add_view("{page_content}","views/items.php",$view_data);		
		
		$this->nb->template->render();
		
		echo $this->nb->template->get_base();

	}

	function profiles()
	{

		$view_data = $template_data = array();
		
		$template_data["current_page"] = "profiles";
		$view_data["profiles"] = $this->nb->profiles->list_profiles();

		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		
		$this->nb->template->add_view("{page_content}","views/profiles.php",$view_data);
		$this->nb->template->render();
		
		echo $this->nb->template->get_base();
			
	}

	function profile()
	{
		$id = $this->nb->get_uri(1);
		
		$view_data = $template_data = array();
		$view_data["errors"] = "";
		$template_data["current_page"] = "profiles";
		
		if($this->nb->is_post)
		{
			$r = $this->nb->profiles->set_profile($id);
			if($r["success"] == true)
			{
				header( 'Location: ?/profiles' ) ;
			}else{
				$view_data["errors"] = $r["errors"];
			}
		}
		
		if($id != "")
		{
			$view_data["edit"] = TRUE;

			$r = $this->nb->profiles->get_profile($id);
			$r = $r[0];

			foreach ($r as $key => $value) {
				$this->nb->post[$key] = $value;
			}

		}else{
			$view_data["edit"] = FALSE;
		}
		
		$this->nb->template->set_base("views/template.php",$template_data);
		
		$this->nb->template->parse_template();
		
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		
		$this->nb->template->add_view("{page_content}","views/profile.php",$view_data);		
		$this->nb->template->render();
		
		echo $this->nb->template->get_base();		
	}

	function delete_profile()
	{
		$id = $this->nb->get_uri(1);
		$r = $this->nb->profiles->delete($id);
		if($r)
		{
			header( 'Location: ?/profiles') ;
		}
	}	

	function profile_items()
	{
		
		$view_data = $template_data = array();
		
		$id = $this->nb->get_uri(1);
		
		
		$view_data["item_search"] = array();
	
		$template_data["current_page"] = "profiles";
		$view_data["items"] = $this->nb->profiles->list_profile_items($id);
		$view_data["profile"] = $this->nb->profiles->get_profile($id)[0];
		$view_data["profile_id"] = $id;
		
		if($this->nb->is_post)
		{
			$term = $this->nb->get_post("term");
			if($term != "")
			{
				$view_data["item_search"] = $this->nb->profiles->search_basket($term);
			}
		}

		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		
		$this->nb->template->add_view("{page_content}","views/profile-items.php",$view_data);
		$this->nb->template->render();
		
		echo $this->nb->template->get_base();
		
	}
	
	function operative_items()
	{
		
		$view_data = $template_data = array();
		$id = $this->nb->get_uri(1);
		
		
		$view_data["item_search"] = array();
	
		$template_data["current_page"] = "operatives";
		$view_data["operative"] = $this->nb->operative->get_operative($id)[0];
		$view_data["operative_id"] = $id;
		$view_data["profile_id"] = $view_data["operative"]["profile"];
		

		
		if($this->nb->is_post)
		{
			$term = $this->nb->get_post("term");
			if($term != "")
			{
				$view_data["item_search"] = $this->nb->profiles->search_basket($term);
			}
		}
		
		$view_data["items"] = $this->nb->operative->get_stock_items($id,$view_data["operative"]["profile"]);
		
		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		
		$this->nb->template->add_view("{page_content}","views/operative-items.php",$view_data);
		$this->nb->template->render();
		
		echo $this->nb->template->get_base();
	}
	
	function orders()
	{
		$view_data = $template_data = array();
		
		$template_data["current_page"] = "orders";
		

		$dates = $this->nb->utils->getStartAndEndDate(date("W"), date("Y"));
		$default = array(
			"status" => "all-active",
			"operative" => "",
			"repair_reference" => "",
			"order_ref" => "",
			"profile" => "",
			"manager" => "",
			"repair_type" => "",
			"repair_zone" => "",
			"pickup_location_code" => "",
			"order_start_date" => $this->nb->utils->db2Uk($dates["week_start"]),
			"order_end_date" => $this->nb->utils->db2Uk($dates["week_end"])
		);
		
		$view_data = array_merge($view_data, $default);
		
		if($this->nb->is_post)
		{
			
			$search_store = array(
				"status" => $this->nb->get_post("status"),
				"operative" => $this->nb->get_post("operative"),
				"order_ref" => $this->nb->get_post("order_ref"),
				"repair_reference" => $this->nb->get_post("repair_reference"),
				"profile" => $this->nb->get_post("profile"),
				"manager" => $this->nb->get_post("manager"),
				"repair_type" => $this->nb->get_post("repair_type"),
				"repair_zone" => $this->nb->get_post("repair_zone"),
				"pickup_location_code" => $this->nb->get_post("pickup_location_code"),
				"order_start_date" => $this->nb->get_post("order_start_date"),
				"order_end_date" => $this->nb->get_post("order_end_date")
			);
			
			
			
			$view_data = array_merge($view_data, $search_store);
			
			$this->nb->session->set("search_store",json_encode($search_store));
			
		}else{
			$search_store = $this->nb->session->get("search_store");
			if($search_store)
			{
				$search_store = json_decode($search_store);
				
				foreach ($search_store as $key => $value) {
					if($value != "")
					{
						$this->nb->post[$key] = $value;
						$view_data[$key] = $value;
					}
				}
			}else{
				foreach ($default as $key => $value) {
					if($value != "")
					{
						$this->nb->post[$key] = $value;
					}
				}	
			}
		}
		
		$view_data["orders"] = $this->nb->orders->get_orders();
		$view_data["operatives"] = $this->nb->orders->get_operatives();
		$view_data["profiles"] = $this->nb->orders->get_profiles();
		$view_data["admin_users"] = $this->nb->orders->get_users();
		
		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		
		$this->nb->template->add_view("{page_content}","views/orders.php",$view_data);
		$this->nb->template->render();
		
		echo $this->nb->template->get_base();
	}
	
	function order_details(){
		
		$view_data = $template_data = array();
		$id = $this->nb->get_uri(1);
		$order_ref = $this->nb->get_uri(2);
		
		$view_data["errors"] = "";
		$template_data["current_page"] = "orders";	
		
		
		if($this->nb->is_post)
		{
			$this->nb->orders->cancel_order($order_ref);
			header( 'Location: ?/orders' ) ;
		}	
		
		$view_data["orders"] =  $this->nb->orders->get_order($id);
		
		$this->nb->template->set_base("views/template.php",$template_data);
		
		$this->nb->template->parse_template();
		
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		
		$this->nb->template->add_view("{page_content}","views/order_details.php",$view_data);		
		$this->nb->template->render();
		
		echo $this->nb->template->get_base();		
	
	}
	
	function update_orderitem_quantity(){				
		
		$r = $this->nb->orders->update_order_item();
		$this->response($r);
		
	}	
	
	function  update_collected_quantity(){
		include_once('models/api.php');
		$this->api = new api($this->nb);
		$r = $this->api->save_collection();
		$this->response($r);		
	}	
	
	function export_orders()
	{
		$type = $this->nb->get_uri(1);
		$csv_data = $this->nb->orders->export_orders($type);
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=orders.csv');
		echo $this->nb->utils->make_csv($csv_data);
	}
	
	function reports_operatives_stock()
	{
		
		$view_data = $template_data = array();
		$template_data["current_page"] = "reports";
		$dates = $this->nb->utils->getStartAndEndDate(date("W"), date("Y"));
		$default = array(
			"operative" => "",
			"profile" => "",
			"term" => "",
			"profile_item" => ""
		);
		
		$view_data = array_merge($view_data, $default);
		
		if($this->nb->is_post)
		{

			$search_store = array(
				"operative" => $this->nb->get_post("operative"),
				"profile" => $this->nb->get_post("profile"),
				"profile_item" => $this->nb->get_post("profile_item"),
				"term" => $this->nb->get_post("term")
			);
			
			$view_data = array_merge($view_data, $search_store);
			
			$this->nb->session->set("op_report_store",json_encode($search_store));
			
		}else{
			$search_store = $this->nb->session->get("op_report_store");
			if($search_store)
			{
				$search_store = json_decode($search_store);
				
				foreach ($search_store as $key => $value) {
					if($value != "")
					{
						$this->nb->post[$key] = $value;
						$view_data[$key] = $value;
					}
				}
			}else{
				foreach ($default as $key => $value) {
					if($value != "")
					{
						$this->nb->post[$key] = $value;
					}
				}	
			}

		}
		
		$view_data["stock"] = $this->nb->reports->operative_stock();
		
		$view_data["operatives"] = $this->nb->orders->get_operatives();
		$view_data["profiles"] = $this->nb->orders->get_profiles();
		$view_data["page_links"] = $this->nb->paginator->build_links("?/reports-operatives-stock/",true);
		
		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		
		$this->nb->template->add_view("{page_content}","views/report_operative_stock.php",$view_data);
		$this->nb->template->render();
		
		echo $this->nb->template->get_base();
	}
	
	function reports_operatives_stock_export()
	{
		$csv_data = $this->nb->reports->operatives_stock_export();
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=operative_stock.csv');
		echo $this->nb->utils->make_csv($csv_data);
	}
	
	function reports_products()
	{
		
		$view_data = $template_data = array();
		$template_data["current_page"] = "reports";
		$dates = $this->nb->utils->getStartAndEndDate(date("W"), date("Y"));
		$default = array(
			"profile" => "",
			"term" => "",
			"order_start_date" => $this->nb->utils->db2Uk($dates["week_start"]),
			"order_end_date" => $this->nb->utils->db2Uk($dates["week_end"])
		);
		

		$view_data = array_merge($view_data, $default);
		
		if($this->nb->is_post)
		{

			$search_store = array(
				"profile" => $this->nb->get_post("profile"),
				"term" => $this->nb->get_post("term"),
				"order_start_date" => $this->nb->get_post("order_start_date"),
				"order_end_date" => $this->nb->get_post("order_end_date")
			);
			
			$view_data = array_merge($view_data, $search_store);
			
			$this->nb->session->set("product_report_store",json_encode($search_store));
			
		}else{
			$search_store = $this->nb->session->get("product_report_store");
			if($search_store)
			{
				$search_store = json_decode($search_store);
				
				foreach ($search_store as $key => $value) {
					if($value != "")
					{
						$this->nb->post[$key] = $value;
						$view_data[$key] = $value;
					}
				}
			}else{
				foreach ($default as $key => $value) {
					if($value != "")
					{
						$this->nb->post[$key] = $value;
					}
				}	
			}
		}
		
		$view_data["product"] = $this->nb->reports->reports_products();
		
		$view_data["operatives"] = $this->nb->orders->get_operatives();
		$view_data["profiles"] = $this->nb->orders->get_profiles();
		$view_data["page_links"] = $this->nb->paginator->build_links("?/reports-products/",true);
		
		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		
		$this->nb->template->add_view("{page_content}","views/report_products.php",$view_data);
		$this->nb->template->render();
		
		echo $this->nb->template->get_base();
		
	}
	
	function reports_products_export()
	{
		//reports_products_export
		$csv_data = $this->nb->reports->reports_products_export();
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=products_report.csv');
		echo $this->nb->utils->make_csv($csv_data);		
	}	
	
	function reports_orders()
	{
		$view_data = $template_data = array();
		
		$template_data["current_page"] = "reports";
		
		$dates = $this->nb->utils->getStartAndEndDate(date("W"), date("Y"));
		$default = array(
			"status" => "all-active",
			"operative" => "",
			"order_ref" => "",
			"profile" => "",
			"manager" => "",
			"repair_type" => "",
			"repair_zone" => "",
			"pickup_location_code" => "",
			"order_start_date" => $this->nb->utils->db2Uk($dates["week_start"]),
			"order_end_date" => $this->nb->utils->db2Uk($dates["week_end"])
		);
		
		$view_data = array_merge($view_data, $default);
		
		if($this->nb->is_post)
		{

			$search_store = array(
				"status" => $this->nb->get_post("status"),
				"operative" => $this->nb->get_post("operative"),
				"order_ref" => $this->nb->get_post("order_ref"),
				"profile" => $this->nb->get_post("profile"),
				"manager" => $this->nb->get_post("manager"),
				"repair_type" => $this->nb->get_post("repair_type"),
				"repair_zone" => $this->nb->get_post("repair_zone"),
				"pickup_location_code" => $this->nb->get_post("pickup_location_code"),
				"order_start_date" => $this->nb->get_post("order_start_date"),
				"order_end_date" => $this->nb->get_post("order_end_date")
			);
			
			$view_data = array_merge($view_data, $search_store);
			
			$this->nb->session->set("reports_orders",json_encode($search_store));
			
		}else{
			$search_store = $this->nb->session->get("reports_orders");
			if($search_store)
			{
				$search_store = json_decode($search_store);
				
				foreach ($search_store as $key => $value) {
					if($value != "")
					{
						$this->nb->post[$key] = $value;
						$view_data[$key] = $value;
					}
				}
			}else{
				foreach ($default as $key => $value) {
					if($value != "")
					{
						$this->nb->post[$key] = $value;
					}
				}	
			}
		}
		

		
		$view_data["orders"] = $this->nb->reports->reports_orders();
		$view_data["operatives"] = $this->nb->orders->get_operatives();
		$view_data["profiles"] = $this->nb->orders->get_profiles();
		$view_data["admin_users"] = $this->nb->orders->get_users();
		
		$this->nb->template->set_base("views/template.php",$template_data);
		$this->nb->template->parse_template();
		
		$this->nb->template->add_content(
			array("find" => "{template_path}", "replace" => $this->nb->base_url("template/"))
		);
		
		$this->nb->template->add_view("{page_content}","views/report_orders.php",$view_data);
		$this->nb->template->render();
		
		echo $this->nb->template->get_base();
	}
		
	function reports_orders_export()
	{
		$type = $this->nb->get_uri(1);
		$csv_data = $this->nb->reports->export_orders($type);
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=report_'.$type.'.csv');
		echo $this->nb->utils->make_csv($csv_data);
	}
	
	function response($data)
	{
		
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type');
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header("Content-Type: application/json");
		
		echo json_encode($data);
		
	}
	
	/*------------------------- AJAX ------------------------*/
	function ajax_user_change_password()
	{
		header('Content-Type: application/json');
		$r = $this->nb->users->change_password();
		echo json_encode($r);
	}
	

	function ajax_operative_change_password()
	{
		header('Content-Type: application/json');
		$r = $this->nb->operative->change_password();
		echo json_encode($r);
	}
	
	function ajax_set_profile_item()
	{
		header('Content-Type: application/json');
		$r = $this->nb->profiles->set_profile_item();
		echo json_encode($r);		
	}
	
	function ajax_delete_profile_item()
	{
		header('Content-Type: application/json');
		$r = $this->nb->profiles->delete_profile_item();
		echo json_encode($r);		
	}
	
	function ajax_set_operative_item()
	{
		header('Content-Type: application/json');
		$r = $this->nb->operative->set_operative_item();
		echo json_encode($r);	
	}
	
	function ajax_delete_operative_item()
	{
		header('Content-Type: application/json');
		$r = $this->nb->operative->delete_operative_item();
		echo json_encode($r);
	}

	function ajax_delete_img()
	{
		header('Content-Type: application/json');
		$r = $this->nb->items->delete_img();
		echo json_encode($r);
	}
	
	function ajax_save_tags()
	{
		header('Content-Type: application/json');
		$r = $this->nb->items->save_tags();
		echo json_encode($r);
	}
	
	/*------------------------- AJAX ------------------------*/
	
	function logout()
	{
		$this->nb->session->destroy();
		header( 'Location: ?/') ;
	}
	
	function test()
	{
		$this->nb->load("models/api.php","api");
		$this->nb->api->set_order_statues("4f5dacfa-aa8c-68df-13a7-d9359818a9d2");
		//$this->nb->load("models/orders.php","orders");
		//$this->nb->orders->process_pos();
		print_r($this->nb->errors);
	}
	
}