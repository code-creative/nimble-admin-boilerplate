<?php
class collector
{

    var $fields = array();
    var $output = array();

    function __construct($nb)
    {
        $this->nb = $nb;
    }

    function add($fields)
    {
        if(isset($fields[0])){
            if(is_array($fields[0]))
            {
                foreach ($fields as $field) {
                    $this->fields[] = $field;
                }  
            }          
        }else{
            $this->fields[] = $fields;
        }
    }

    function clear()
    {
        $this->fields = array();
        $this->output = array();
    }

    function collect()
    {

        foreach ($this->fields as $field) {
            $type = $field["type"];
            $value = "";
            switch ($type) {
                case '':
                case 'POST':
                        $value = $this->nb->get_post($field["name"]);        
                    break;
                case 'SEGMENT':
                        $value = $this->nb->get_uri($field["name"]);  
                    break;
            }

            if(isset($field["format"]))
            {
                if(method_exists($this,$field["format"]))
                {
                   
                   $value = $this->{$field["format"]}($field,$value);
                } 
            }
            $key = $field["name"];
            if(isset($field["key"]))
            {
                $key = $field["key"];
            }

            $this->output[$key] = $value;

        }//foreach
        
        return $this->output;

    }


    public function _md5($field,$value)
    {
        return md5($value);
    }

    public function _date($field,$value)
    {
        echo $value . " " . $field["date_format"];
        return date($field['date_format'], strtotime($value));
    } 

    public function _uk_db_date($field,$value)
    {
         return date("Y-m-d", strtotime(str_replace('/', '-', $value)) );
    }




}