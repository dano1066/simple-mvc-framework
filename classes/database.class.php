<?php 
namespace core;
use PDO;
use core\Logger;
class Database
{
	function ExecuteQuery($sql, $fields = null, $autoID = false, $dbstring = null)
	{
		if($dbstring == null) $db = Database::generateDBString();
		else $db = $dbstring;
		$stmt = $db->prepare($sql);
		
		if($fields != null) $status = $stmt->execute($fields);
		else $status = $stmt->execute();
			
		if($status)
		{
			if($autoID == true) return $db->lastInsertId();
			else return $status;
		}
		else {
			$errorstring = "An error occurred in the 'ExecuteQuery' function call\n\tSQL = ".$sql."\n\tFIELDS = ".print_r($fields,true)."\n\tERRORS = ".print_r($stmt->errorInfo());
			Logger::Init()->LogDbError($errorstring);
			return false;
		}
	}
	
	function GetSQLResults($sql, $fields = null, $multiRow = true, $dbstring = null)
	{
		if($dbstring == null) $db = Database::generateDBString();
		else $db = $dbstring;
		$stmt = $db->prepare($sql);
		
		if($fields != null) $status = $stmt->execute($fields);
		else $status = $stmt->execute();
		
		if($status)
		{
			if($multiRow == true) return $stmt->FetchAll(PDO::FETCH_ASSOC);
			else return $stmt->fetch(PDO::FETCH_ASSOC);
		}
		else {
			$errorstring = "An error occurred in the 'GetSQLResults' function call\n\tSQL = ".$sql."\n\tFIELDS = ".str_replace("\n", "", print_r($fields,true))."\n\tERRORS = ".str_replace("\n", "", print_r($stmt->errorInfo(), true));
			Logger::Init()->LogDbError($errorstring);
			return false;
		}
	}
	
	function GetCount($sql, $fields, $dbstring = null)
	{
		if($dbstring == null) $db = Database::generateDBString();
		else $db = $dbstring;
		$stmt = $db->prepare($sql);
		$status = $stmt->execute($fields);
		if($status) return $stmt->fetch()[0];	
		else {
			$errorstring = "An error occurred in the 'GetCount' function call\n\tSQL = ".$sql."\n\tFIELDS = ".print_r($fields,true)."\n\tERRORS = ".print_r($stmt->errorInfo());
			Logger::Init()->LogDbError($errorstring);
			return false;
		}
	}
	
	function generateDBString()
	{
		//return new PDO('mysql:host=localhost;dbname=nerddatabase;charset=utf8', $DBUser, $DBPass);
		return new PDO("mysql:host=".DBHOST.";dbname=".DBNAME.";charset=utf8", DBUSERNAME, DBPASSWORD);
	}
}
