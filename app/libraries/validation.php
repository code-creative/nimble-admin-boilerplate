<?

class validation
{
	
	var $fields = array();
	var $errors = array();

	public function __construct($nb) {
		$this->nb = $nb;
	}

	public function add($name,$rules)
	{
		$this->fields[] = array("name" => $name, "rules" => $rules);
	}

	public function validate()
	{
		$rules = array();
		foreach ($this->fields as $field) {
			$value = $this->nb->get_post($field['name']);
			foreach ($field['rules'] as $rule) {
				$param = FALSE;
				$type = $rule["type"];

				if (preg_match("/(.*?)\[(.*)\]/", $type, $match))
				{
					$type	= $match[1];
					$param	= $match[2];
				}
				
				if($this->{$type}($value,$param) == FALSE)
				{
					$this->errors[$field['name']][] = array("message" => $rule['message']);
				}

			}

		}

		if(count($this->errors))
		{
			return false;
		}else{
			return true;
		}

	}

	function has_error($field,$return = "error")
	{
		if(isset($this->errors[$field]))
		{
			return $return;
		}else{
			return "";
		}
	}

	function get_errors($field,$delimiter = "<br/>",$prefix = "", $sufix = "")
	{
		if(isset($this->errors[$field]))
		{
			$e = array();
			foreach ($this->errors[$field] as $err) {
				$e[] = $err["message"];
			}
			return $prefix . implode($e, $delimiter) . $sufix;
		}else{
			return "";
		}
	}

	function get_all_errors($delimiter = "<br/>",$prefix = "", $sufix = "")
	{
		$msg = array();
		foreach ($this->errors as $errors) {
			foreach ($errors as $error) {
				array_push($msg,$error["message"]);
			}
									
		}
		if(count($msg) > 0)
		{
			return $prefix . implode($msg, $delimiter) . $sufix;	
		}else{
			return "";
		}
		
	}

	/* -------------------------------------------------------------- */

	function required($value)
	{
		return ($value == "")? FALSE : TRUE;
		
	}
	
	function matches($str, $field)
	{
		if ( ! isset($_POST[$field]))
		{
			return FALSE;
		}

		$field = $_POST[$field];

		return ($str !== $field) ? FALSE : TRUE;
	}

	function min_length($str, $val)
	{

		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) < $val) ? FALSE : TRUE;
		}

		return (strlen($str) < $val) ? FALSE : TRUE;
	}

	function max_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) > $val) ? FALSE : TRUE;
		}

		return (strlen($str) > $val) ? FALSE : TRUE;
	}

	function exact_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) != $val) ? FALSE : TRUE;
		}

		return (strlen($str) != $val) ? FALSE : TRUE;
	}

	function valid_email($str)
	{
		if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$str))
		{
			return FALSE;
		}
		return TRUE;
	}

	function valid_emails($str)
	{
		if (strpos($str, ',') === FALSE)
		{
			return $this->valid_email(trim($str));
		}

		foreach (explode(',', $str) as $email)
		{
			if (trim($email) != '' && $this->valid_email(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	function alpha($str)
	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}

	function alpha_numeric($str)
	{
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}

	function alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}

	function numeric($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

	}

	function is_numeric($str)
	{
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	}

	function integer($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}

	function decimal($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
	}

	function greater_than($str, $min)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str > $min;
	}

	// --------------------------------------------------------------------

	function less_than($str, $max)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str < $max;
	}

	// --------------------------------------------------------------------

	function is_natural($str)
	{
		return (bool) preg_match( '/^[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	function is_natural_no_zero($str)
	{
		if ( ! preg_match( '/^[0-9]+$/', $str))
		{
			return FALSE;
		}

		if ($str == 0)
		{
			return FALSE;
		}

		return TRUE;
	}


}