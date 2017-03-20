<?php 
namespace core;
class Model
{
	public $TableName;
	public $PrimaryKey;
	private $SchemaInfo;
	
	public function __construct()
	{
		// Your "heavy" initialization stuff here
	}
	public static function Get()
	{
		return new static();
	}
	
	public function ByID($id)
	{
		$sql = "SELECT * FROM ".$this->TableName." WHERE ".$this->GetPrimaryKey()." = :id LIMIT 1"; // LIMIT 1 TO PREVENT ISSUES WITH BAD DB SCHEMA
		$fields = array(":id" => $id);
		$rowdata = Database::GetSQLResults($sql, $fields, false);
		foreach($rowdata as $col => $val)
		{
			$this->{$col} = $val;
		}
		return $this;
	}
	
	public function ByField($fieldname, $fieldvalue)
	{
		$sql = "SELECT * FROM ".$this->TableName." WHERE ".$fieldname." = :id LIMIT 1"; // LIMIT 1 TO PREVENT ISSUES WITH BAD DB SCHEMA
		$fields = array(":id" => $fieldvalue);
		$rowdata = Database::GetSQLResults($sql, $fields, false);
		foreach($rowdata as $col => $val)
		{
			$this->{$col} = $val;
		}
		return $this;
	}
	
	public function Save()
	{
		$sqlfields = "";
		$sqlvalues = "";
		$fieldarray = array();
		foreach($this->GetSchema() as $column)
		{
			if($column["Extra"] != "auto_increment")
			{
				$sqlfields .= $column["Field"].",";
				$sqlvalues .= ":".$column["Field"].",";
				$fieldarray[":".$column["Field"]] = $this->{$column["Field"]};
			}
		}
		$sql = "INSERT INTO ".$this->TableName."(".$sqlfields.") VALUES (".$sqlvalues.")";
		$insertresult = Database::ExecuteQuery($sql, $fields, true);
		if($insertresult != false) $this->{GetPrimaryKey()} = $insertresult;
	}
	
	public function Delete()
	{
		$sql = "DELETE FROM ".$this->TableName." WHERE ".GetPrimaryKey()." = :id";
		$fields = array(":id" => $this->{GetPrimaryKey()});
		Database::ExecuteQuery($sql, $fields);
		unset($this);
	}
	
	private function GetPrimaryKey()
	{
		if(count($this->PrimaryKey) != 0) return $this->PrimaryKey;
		else{
			$result = $this->GetSchema();
			foreach($result as $col){
				if($col["Key"] == "PRI") {
					$this->PrimaryKey = $col["Field"];
					return $this->PrimaryKey; 
				}
			}
		}
	}
	
	private function GetSchema()
	{
		if($this->SchemaInfo == null) $this->SchemaInfo = Database::GetSQLResults("describe ".$this->TableName);
		return $this->SchemaInfo;
	}
}
