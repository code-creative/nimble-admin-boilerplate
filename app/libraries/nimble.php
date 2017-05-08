<?php 

class nimble
{
	
	
	protected $controller = array();
	protected $method = array();
	protected $route = null;
	protected $uri_offsite = 0;

	public $messages = array();
	public $errors = array();
	public $post = array();
	public $get = array();
	public $files = array();
	public $segments = array();
	public $now = null;
	public $is_post = false;
	public $environment = "dev";

	function init()
	{
		
		include_once(dirname(__FILE__) ."/routing.php");
		include_once(dirname(__FILE__) ."/$this->environment.config.php");
		
		$this->now = date('Y-m-d H:i:s');
		
		$uri = $this->uri();

		if( count($uri) == 0 || $uri[0] == "")
		{
			$this->ref = "default";
		}else{
			$this->ref = $uri[0];
		}
		
		for ($i=0; $i < count($uri); $i++) { 
			$this->segments[] = $uri[$i];
		}

		foreach ($_GET as $key => $value) {
			$this->get[$key] = $value;
		}
		
		foreach ($_FILES as $key => $value) {
			$this->files[$key] = $value;
		}	
		 

		$this->is_post = (count($_POST)>0)?TRUE:FALSE;

		foreach ($_POST as $key => $value) {
			$this->post[$key] = $value;
		}

		if($config)
		{
			$this->config = $config;

			if(isset($this->config['auto_load']))
			{
				foreach ($this->config['auto_load'] as $c) {
					if(isset($c['alias']))
					{
						$this->load($c['file'],$c['name'],$c['alias']);
					}else{
						$this->load($c['file'],$c['name']);
					}
					
				}
			}

		}

		if(isset($routing[$this->ref]))
		{
			
			$this->route = $routing[$this->ref];
			$this->controller_path = $this->route['controller'];
			$path_segments = explode("/", $this->controller_path);
			$this->controller_name = end($path_segments);
			if(isset($this->route['load']))
			{
				foreach ($this->route['load'] as $c) {
					if(isset($c['alias']))
					{
						$this->load($c['file'],$c['name'],$c['alias']);
					}else{
						$this->load($c['file'],$c['name']);
					}
					
				}
			}

			if(file_exists($this->controller_path . ".php"))
			{
				include_once($this->controller_path . ".php");
				$controller = new $this->controller_name($this);
				$controller->{$this->route['method']}($this);
			}else{
				$this->errors[]= 'controller not found';
			}	

		}else{
			echo 'routing error';
		}




	}

	public function load($file,$class_name,$alias = "")
	{
		if (file_exists($file))
		{
			if(!isset($this->{$class_name}))
			{
				include_once($file);
				if($alias != "")
				{
					$this->{$alias} = new $class_name($this);
				}else{
					$this->{$class_name} = new $class_name($this);
				}
				
			}//if(!isset	
		}else{    
			$this->errors[]= 'could not find '.$class_name.' to load in ' .$file;
		}//if (file_exists
	}//load
		
	function view($file, $data=array(), $string=false)
	{
		
		if(sizeof($data) > 0)
			extract($data, EXTR_SKIP);

		if (file_exists($file)) {
			if($string) {
				ob_start();
				include_once($file);
				$content = ob_get_contents();
				ob_end_clean();
				return $content;
			} else {
				include_once($file);
			}
		} else {
			$this->errors[] = "Can't load template file: " . $file;
			return false;
		}
		return true;
	}

	function get_post($name)
	{
		return (isset($this->post[$name]))? $this->post[$name] : '';
	}

	function get_get($name)
	{
		return (isset($this->get[$name]))? $this->get[$name] : '';
	}

	function get_uri($index)
	{
		return urldecode((isset($this->segments[$index]))? $this->segments[$index] : '');
	}	
	
	function get_files($name)
	{
		return (isset($this->files[$name]))? $this->files[$name] : '';
	}

	function add_messages($msg)
	{
		$this->messages[] = $msg;
	}

	function clear_messages()
	{
		$this->messages = array();
	}

	function add_errors($error)
	{
		$this->errors[] = $error;
	}

	function clear_errors()
	{
		$this->errors = array();
	}

	function display_errors(){

	}		

	function uri()
	{	

		$requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$scriptName = explode('/',$_SERVER['SCRIPT_NAME']);		
		for($i= 0;$i < sizeof($scriptName); $i++)
		{
			if (($requestURI[$i] == $scriptName[$i]) || ($requestURI[$i] == "?"))
			{
				unset($requestURI[$i]);
			}
		}
		$pieces = array_values($requestURI);
		return $pieces;

	}//uri
		

	function base_url($path = ''){
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http";
		$path = ($path != '')? '/'. $path : '';
		return $protocol . "://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) . $path;
	}

}