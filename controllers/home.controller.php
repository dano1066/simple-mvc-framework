<?php 
use core\Response;
use core\Database;
use core\Helpers;
use core\CacheFile;
class home
{
	function index()
	{
		$issues = array();
		$canlog = is_writable (FSROOT."/logs/sql/");
		try{
			$sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".DBNAME."'";
			Database::GetSqlResults($sql);
			$dbexists = true;
		}
		catch (Exception $e) {
			$dbexists = false;
		}

		//check the results
		if($canlog == false) $issues[] = array("Unable to write to log directory.", "The framework must be able to write to the main log directory. Please update the file system so that your web server can write files to ". FSROOT."logs/");
		if($dbexists == false) $issues[] = array("Database '".DBNAME."' does not exist", "DB information can be set in the config.php file. If you do not need to use SQL then you can ignore this message.");
		return Response::View()->AddVar("issues", $issues);
	}
}
