<?php 
use core\Model;
use core\Database;
use core\Helpers;
class User extends Model
{
  public $username;
  public $password;
  
  public function checklogin($inputname, $inputpass)
  {
   //check the login etc
  }
}
?>
