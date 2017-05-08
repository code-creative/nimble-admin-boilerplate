<?
	
	class template
	{

		var $base;
		var $views = array();
		var $content = array();

		public function __construct($nb) {
			$this->nb = $nb;
		}		

		public function set_base($view, $data = null)
		{
			$this->base = $this->nb->view($view,$data,true);
		}

		public function parse_template()
		{
			$pattern = '/\{(.*?)\}/';
			preg_match_all($pattern, $this->base, $matches,PREG_SET_ORDER);	
			if(is_array($matches) && count($matches) > 0){
				foreach($matches as $row){
					$field = explode("|", $row[1]);
					if(method_exists($this,$field[0]))
					{
						$this->base = str_replace($row[0], $this->{$field[0]}($field), $this->base);
					}
				}	
			}
		}


	public function parse_static_template($data,$temp,$extend = array())
	{
		
		$pattern = '/\{(.*?)\}/';

		if(count($extend))
		$data = array_merge($data, $extend);

		$data = $data[0];

		preg_match_all($pattern, $temp, $matches,PREG_SET_ORDER);		
		if(is_array($matches) && count($matches) > 0){
			foreach($matches as $row){
				$feild = explode("|", $row[1]);
				if(isset($data[$feild[0]])){
					$feild[0] = $data[$feild[0]];
					$temp = str_replace($row[0], $feild[0], $temp);
				}
			}	
		}
		return $temp;	
	}


		public function view($field)
		{	
			return $this->nb->view($field[1],null,true);
		}		

		public function format($field)
		{
			$r = $field[0];
			if(isset($field[1]) && $r != '')
			{
				switch ($field[1]) {
					case 'date':
						$r = date($field[2], strtotime($field[1]));
						break;	
					case 'md5':
						$r = md5($field[0]);
						break;					
				}
			}
			return $r;
		}			

		public function add_view($tag, $view, $data = null)
		{
			$this->base = str_replace($tag, $this->nb->view($view,$data,true), $this->base);
		}	

		public function set($find,$replace)
		{

			$this->base = str_replace($find, $replace, $this->base);	
		}
		
		public function get_base()
		{
			return $this->base;
		}

		public function add_content($replace)
		{
			$this->content[] = $replace;
		}

		public function render()
		{	
			foreach($this->content as $replacer)
			{
				$this->set($replacer['find'],$replacer['replace']);
			}	
		}

	}