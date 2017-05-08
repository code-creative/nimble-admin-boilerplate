<?
	
	class utils
	{

		function __construct($nb)
		{
			$this->nb = $nb;
		}

		function yn_helper($value)
		{
			$r = "";
			switch ($value) {
				case '':
				case 'NO':
				case 'N':
				case 'n':
					$r = "No";
					break;
				case 'YES':
				case 'yes':
				case 'y':
					$r = "Yes";
					break;
			}

			return $r;
		}


		function getStartAndEndDate($week, $year) {
			$dto = new DateTime();
			$dto->setISODate($year, $week);
			$ret['week_start'] = $dto->format('Y-m-d');
			$dto->modify('+6 days');
			$ret['week_end'] = $dto->format('Y-m-d');
			return $ret;
		}

		function dropdown_maker($arr, $check = "")
		{
			$html = "";
			foreach ($arr as $option) {
				$html .= "<option value='".$option["value"]."' ".$this->select_helper($option["value"],$check).">". $option["text"]."</option>";
			}
			return $html;
		}

		function select_helper($value,$check)
		{
			return (trim($value) == trim($check))? "selected='selected'" : "";
		}

		function random_string($type = 'alnum', $len = 8)
		{
			switch($type)
			{
				case 'basic'	: return mt_rand();
					break;
				case 'alnum'	:
				case 'numeric'	:
				case 'nozero'	:
				case 'alpha'	:

						switch ($type)
						{
							case 'alpha'	:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
								break;
							case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
								break;
							case 'numeric'	:	$pool = '0123456789';
								break;
							case 'nozero'	:	$pool = '123456789';
								break;
						}

						$str = '';
						for ($i=0; $i < $len; $i++)
						{
							$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
						}
						return $str;
					break;
				case 'unique'	:
				case 'md5'		:

							return md5(uniqid(mt_rand()));
					break;

			}
		}

		function find_tag($tags,$id)
		{
			foreach ($tags as $tag) {
				if($tag["id"] == $id)
				{
					return $tag;
					
				}
			}
		}	

		function sec_time($t,$f=':') // t = seconds, f = separator 
		{
			return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
		}

		function make_csv($data, $delimiter = ',', $enclosure = '"') {
			$contents = null;
			$handle = fopen('php://temp', 'r+');
			foreach ($data as $line) {
				fputcsv($handle, $line, $delimiter, $enclosure);
			}
			rewind($handle);
			while (!feof($handle)) {
				$contents .= fread($handle, 8192);
			}
			fclose($handle);
			return $contents;
		}

function time_sec($time='00:00:00')
{
    list($hours, $mins, $secs) = explode(':', $time);
    return ($hours * 3600 ) + ($mins * 60 ) + $secs;
}					

	function get_color($num) {
	    $hash = md5('color' . $num); // modify 'color' to get a different palette
	    return array(
	        hexdec(substr($hash, 0, 2)), // r
	        hexdec(substr($hash, 2, 2)), // g
	        hexdec(substr($hash, 4, 2))); //b
	}


	function word_censor($str, $censored, $replacement = '')
	{
		if ( ! is_array($censored))
		{
			return $str;
		}

		$str = ' '.$str.' ';

		// \w, \b and a few others do not match on a unicode character
		// set for performance reasons. As a result words like über
		// will not match on a word boundary. Instead, we'll assume that
		// a bad word will be bookeneded by any of these characters.
		$delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

		foreach ($censored as $badword)
		{
			if ($replacement != '')
			{
				$str = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/i", "\\1{$replacement}\\3", $str);
			}
			else
			{
				$str = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/ie", "'\\1'.str_repeat('#', strlen('\\2')).'\\3'", $str);
			}
		}

		return trim($str);
	}

	function highlight_code($str)
	{
		// The highlight string function encodes and highlights
		// brackets so we need them to start raw
		$str = str_replace(array('&lt;', '&gt;'), array('<', '>'), $str);

		// Replace any existing PHP tags to temporary markers so they don't accidentally
		// break the string out of PHP, and thus, thwart the highlighting.

		$str = str_replace(array('<?', '?>', '<%', '%>', '\\', '</script>'),
							array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'), $str);

		// The highlight_string function requires that the text be surrounded
		// by PHP tags, which we will remove later
		$str = '<?php '.$str.' ?>'; // <?

		// All the magic happens here, baby!
		$str = highlight_string($str, TRUE);

		// Prior to PHP 5, the highligh function used icky <font> tags
		// so we'll replace them with <span> tags.

		if (abs(PHP_VERSION) < 5)
		{
			$str = str_replace(array('<font ', '</font>'), array('<span ', '</span>'), $str);
			$str = preg_replace('#color="(.*?)"#', 'style="color: \\1"', $str);
		}

		// Remove our artificially added PHP, and the syntax highlighting that came with it
		$str = preg_replace('/<span style="color: #([A-Z0-9]+)">&lt;\?php(&nbsp;| )/i', '<span style="color: #$1">', $str);
		$str = preg_replace('/(<span style="color: #[A-Z0-9]+">.*?)\?&gt;<\/span>\n<\/span>\n<\/code>/is', "$1</span>\n</span>\n</code>", $str);
		$str = preg_replace('/<span style="color: #[A-Z0-9]+"\><\/span>/i', '', $str);

		// Replace our markers back to PHP tags.
		$str = str_replace(array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
							array('&lt;?', '?&gt;', '&lt;%', '%&gt;', '\\', '&lt;/script&gt;'), $str);

		return $str;
	}

	function highlight_phrase($str, $phrase, $tag_open = '<strong>', $tag_close = '</strong>')
	{
		if ($str == '')
		{
			return '';
		}

		if ($phrase != '')
		{
			return preg_replace('/('.preg_quote($phrase, '/').')/i', $tag_open."\\1".$tag_close, $str);
		}

		return $str;
	}

	function word_wrap($str, $charlim = '76')
	{
		// Se the character limit
		if ( ! is_numeric($charlim))
			$charlim = 76;

		// Reduce multiple spaces
		$str = preg_replace("| +|", " ", $str);

		// Standardize newlines
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		// If the current word is surrounded by {unwrap} tags we'll
		// strip the entire chunk and replace it with a marker.
		$unwrap = array();
		if (preg_match_all("|(\{unwrap\}.+?\{/unwrap\})|s", $str, $matches))
		{
			for ($i = 0; $i < count($matches['0']); $i++)
			{
				$unwrap[] = $matches['1'][$i];
				$str = str_replace($matches['1'][$i], "{{unwrapped".$i."}}", $str);
			}
		}

		// Use PHP's native function to do the initial wordwrap.
		// We set the cut flag to FALSE so that any individual words that are
		// too long get left alone.  In the next step we'll deal with them.
		$str = wordwrap($str, $charlim, "\n", FALSE);

		// Split the string into individual lines of text and cycle through them
		$output = "";
		foreach (explode("\n", $str) as $line)
		{
			// Is the line within the allowed character count?
			// If so we'll join it to the output and continue
			if (strlen($line) <= $charlim)
			{
				$output .= $line."\n";
				continue;
			}

			$temp = '';
			while ((strlen($line)) > $charlim)
			{
				// If the over-length word is a URL we won't wrap it
				if (preg_match("!\[url.+\]|://|wwww.!", $line))
				{
					break;
				}

				// Trim the word down
				$temp .= substr($line, 0, $charlim-1);
				$line = substr($line, $charlim-1);
			}

			if ($temp != '')
			{
				$output .= $temp."\n".$line;
			}
			else
			{
				$output .= $line;
			}

			$output .= "\n";
		}

		// Put our markers back
		if (count($unwrap) > 0)
		{
			foreach ($unwrap as $key => $val)
			{
				$output = str_replace("{{unwrapped".$key."}}", $val, $output);
			}
		}

		// Remove the unwrap tags
		$output = str_replace(array('{unwrap}', '{/unwrap}'), '', $output);

		return $output;
	}

	function ellipsize($str, $max_length, $position = 1, $ellipsis = '&hellip;')
	{
		// Strip tags
		$str = trim(strip_tags($str));

		// Is the string long enough to ellipsize?
		if (strlen($str) <= $max_length)
		{
			return $str;
		}

		$beg = substr($str, 0, floor($max_length * $position));

		$position = ($position > 1) ? 1 : $position;

		if ($position === 1)
		{
			$end = substr($str, 0, -($max_length - strlen($beg)));
		}
		else
		{
			$end = substr($str, -($max_length - strlen($beg)));
		}

		return $beg.$ellipsis.$end;
	}

	function display_men()
	{
		$size = memory_get_usage(true);
		$unit = array('b','kb','mb','gb','tb','pb');
    	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
		
	function db2Uk($_date,$time = false)
	{	
		$t = "";
		
		if($time)
			$t = ' H:i:s';
		
		return 	($_date != "")? date("d/m/Y".$t,strtotime($_date)) : "";
	}

	function uk2Db($_date,$time = false)
	{
		$t = "";
		if($time)
			$t = ' H:i:s';
		
		return 	($_date != "")? date('Y-m-d'.$t, strtotime(str_replace('/', '-', $_date)) ) : "";
	}
	
	function firstOfMonth() {
		return date("Y-m-d H:i:s", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
	}

	function lastOfMonth() {
		return date("Y-m-d H:i:s", strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
	}
		
		
	function auth()
	{
		
		$r = array();
		$auth_key = $this->nb->get_post("auth_key");
		$sql = "SELECT * FROM operatives WHERE auth_key = '".$auth_key."'";
		$check =  $this->nb->db->run($sql);
		if(count($check) == 0)
		{
		
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, HEAD, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type');
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header("Content-Type: application/json");
		
		echo json_encode(array("success" => false, "logout" => true, "check" => $check ));
		
		exit;
		}else{
			return $check[0];
		}
	}
		
	function order_status_map($status)
	{
		$r = "";
		switch($status)
		{
			case -4:
				$r = "Status pending";
				break;
			case -3:
				$r = "File Created";
				break;
			case -2:
				$r = "Awaiting Transfer";
				break;
			case -1:
				$r = "Transfered";
				break;
			case 0:
				$r = "Deleted";
				break;
			case 1:
				$r = "Confirmed";
				break;
			case 2:
				$r = "Uncomplete";
				break;	
			case 3:
				$r = "Complete";
				break;	
			case 4:
				$r = "Canceled";
				break;
		}
		return $r;
	}
	

	function get_vat($value,$tax_rate)
	{
		return ($value/100)*$tax_rate;
	}		
		
		
	function upload($field_name = '', $target_folder = '', $file_name = '', $thumb = FALSE, $thumb_folder = '', $thumb_width = '', $thumb_height = ''){

    
	$target_path = $target_folder;
    $thumb_path = $thumb_folder;
    
	$filename_err = explode(".",$_FILES[$field_name]['name']);
    $filename_err_count = count($filename_err);
    $file_ext = $filename_err[$filename_err_count-1];
		
    if($file_name != ''){
        $fileName = $file_name.'.'.$file_ext;
    }else{
        $fileName = $_FILES[$field_name]['name'];
    }
    
    //upload image path
    $upload_image = $target_path.basename($fileName);
    
    //upload image
    if(move_uploaded_file($_FILES[$field_name]['tmp_name'],$upload_image))
    {
        //thumbnail creation
        if($thumb == TRUE)
        {
            $thumbnail = $thumb_path.$fileName;
            list($width,$height) = getimagesize($upload_image);
            $thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);
            switch($file_ext){
                case 'jpg':
                    $source = imagecreatefromjpeg($upload_image);
                    break;
                case 'jpeg':
                    $source = imagecreatefromjpeg($upload_image);
                    break;

                case 'png':
                    $source = imagecreatefrompng($upload_image);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($upload_image);
                    break;
                default:
                    $source = imagecreatefromjpeg($upload_image);
            }

            imagecopyresized($thumb_create,$source,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
            switch($file_ext){
                case 'jpg' || 'jpeg':
                    imagejpeg($thumb_create,$thumbnail,100);
                    break;
                case 'png':
                    imagepng($thumb_create,$thumbnail,100);
                    break;
                case 'gif':
                    imagegif($thumb_create,$thumbnail,100);
                    break;
                default:
                    imagejpeg($thumb_create,$thumbnail,100);
            }

        }

        return $fileName;
    }
    else
    {
        return false;
    }
}		
		
		
		function trimStrByLen($str, $len = 50)
		{
			return strlen($str) > $len ? substr($str,0,$len) : $str;
		}
		
		
}