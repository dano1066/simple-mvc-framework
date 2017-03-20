<?php 
//Add all custom functions here. File will be loaded during the init of the framework.
namespace core;
use core\Database;
class Helpers
{
	
	public function generateSaltedToken()
	{
		$string = sha1("Secure Token");
		$random = sha1(rand().time());
		$salt = sha1($string.$random);

		$token = sha1($salt);

		return $token;
	}
	
	public function generateSlug($string)
	{
		$string = strtolower($string);
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		$string = preg_replace("/[\s-]+/", " ", $string);
		$string = preg_replace("/[\s_]/", "-", $string);
		return $string;
	}
	
	public function getUserIP()
	{
		if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])) return $_SERVER["HTTP_CF_CONNECTING_IP"];
		else return $_SERVER['REMOTE_ADDR'];
	}
	
	public function cookieLogin() {
		$sql = "SELECT ID, UserName FROM Users WHERE rememberme = :rememberme";
		$fields = array(":rememberme" => $_COOKIE['rememberme']);
		$userinfo = Database::GetSQLResults($sql, $fields, false);
		if($userinfo && ($userinfo['ID'] == $_COOKIE['userid']))
		{
			$_SESSION['nb_userid'] = $userinfo['ID'];
			$_SESSION['nb_username'] = $userinfo['UserName'];
		}
		else //if there was a mismatch between the remember me cookie and the userid cookie then delete both.
		{
			setcookie("rememberme", "", time()-3600);
			setcookie("userid", "", time()-3600);
		}
	}

}
