<?php

/*
$db = new db("mysql:host=localhost;dbname=mydb", "dbuser", "dbpasswd");

$sql = <<<SQL
CREATE TABLE mytable (
    ID int(11) NOT NULL AUTO_INCREMENT,
    FName varchar(50) NOT NULL,
    LName varchar(50) NOT NULL,
    Age int(11) NOT NULL,
    Gender enum('male','female') NOT NULL,
    PRIMARY KEY (ID)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;
SQL;
$db->run($sql);

$db->insert("mytable", array(
    "FName" => "John",
    "LName" => "Doe",
    "Age" => 26,
    "Gender" => "male"
));

$results = $db->select("mytable");
print_r($results);

$db->update("mytable", array(
    "FName" => "Jane",
    "Gender" => "female"
), "FName = John AND LName = Doe");

$results = $db->select("mytable");
print_r($results);

$db->delete("mytable", "FName = Jane AND LName = Doe");

$results = $db->select("mytable");
print_r($results);
*/

class database extends PDO {
	private $error;
	private $sql;
	private $bind;
	private $errorCallbackFunction;
	private $errorMsgFormat;

	public function __construct($nb) {

		$this->nb = $nb;

		if($this->nb->config['db']['type'] == "mysql")
		{
			$dsn = "mysql:host=".$this->nb->config['db']['host'].";dbname=".$this->nb->config['db']['database']; 
		}else{
			$dns = "sqlite:".$this->nb->config['db']['database'].".sqlite";
		}
		$user = $this->nb->config['db']['user'];
		$passwd = $this->nb->config['db']['password'];

		$options = array(
			PDO::ATTR_PERSISTENT => true, 
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);

		try {
			if($this->nb->config['db']['type'] == "mysql")
			{
				parent::__construct($dsn, $user, $passwd, $options);
			}else{
				parent::__construct($dsn);
			}
		} catch (PDOException $e) {
			echo $this->error = $e->getMessage();
		}

		
	}

	public function delete($table, $where, $bind="") {
		$sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
		$this->run($sql, $bind);
	}

	private function filter($table, $info) {
		$driver = $this->getAttribute(PDO::ATTR_DRIVER_NAME);
		if($driver == 'sqlite') {
			$sql = "PRAGMA table_info('" . $table . "');";
			$key = "name";
		}
		elseif($driver == 'mysql') {
			$sql = "DESCRIBE " . $table . ";";
			$key = "Field";
		}
		else {	
			$sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
			$key = "column_name";
		}	

		if(false !== ($list = $this->run($sql))) {
			$fields = array();
			foreach($list as $record)
				$fields[] = $record[$key];
			return array_values(array_intersect($fields, array_keys($info)));
		}
		return array();
	}

	private function cleanup($bind) {
		if(!is_array($bind)) {
			if(!empty($bind))
				$bind = array($bind);
			else
				$bind = array();
		}
		return $bind;
	}

	public function insert($table, $info) {
		$fields = $this->filter($table, $info);
		$sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ");";
		$bind = array();
		foreach($fields as $field)
			$bind[":$field"] = $info[$field];
		
		return $this->run($sql, $bind);
	}

	public function run($sql, $bind="") {
		$this->sql = trim($sql);
		$this->bind = $this->cleanup($bind);
		$this->error = "";

		try {
			$pdostmt = $this->prepare($this->sql);
			if($pdostmt->execute($this->bind) !== false) {
				if(preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $this->sql))
					return $pdostmt->fetchAll(PDO::FETCH_ASSOC);
				elseif(preg_match("/^(" . implode("|", array("delete", "insert", "update")) . ") /i", $this->sql))
					return $pdostmt->rowCount();
			}	
		} catch (PDOException $e) {
			$this->nb->add_errors($e->getMessage());
			$this->nb->add_errors("sql error: ". $sql);
			return false;
		}
	}

	public function select($table, $where="", $bind="", $fields="*", $limit="") {
		$sql = "SELECT " . $fields . " FROM " . $table;		
		if(!empty($where))
			$sql .= " WHERE " . $where;
		if(!empty($limit))
			$sql .= " LIMIT " . $limit;
		
		$sql .= ";";
		return $this->run($sql, $bind);
	}


	public function update($table, $info, $where, $bind="") {
		$fields = $this->filter($table, $info);
		$fieldSize = sizeof($fields);

		$sql = "UPDATE " . $table . " SET ";
		for($f = 0; $f < $fieldSize; ++$f) {
			if($f > 0)
				$sql .= ", ";
			$sql .= $fields[$f] . " = :update_" . $fields[$f]; 
		}
		$sql .= " WHERE " . $where . ";";

		$bind = $this->cleanup($bind);
		foreach($fields as $field)
			$bind[":update_$field"] = $info[$field];
		
		return $this->run($sql, $bind);
	}
}	
?>
