<?php 
define("FSROOT", dirname(__FILE__)."/");
define("DEBUG", false);
define("SHOWERRORS", true);
define("DIEONERROR", true);
define("STATICCONTROLLER", TRUE);
define("STATICCONTROLLERNAME", "home"); //required if you want to use multiple controllers, but also use routes without the controller prefix

define("DBHOST", "localhost");
define("DBUSERNAME", "root");
define("DBPASSWORD", '');
define("DBNAME", "dbserver");

define("ENABLECACHE", false);
define("CACHETTL", 600);

define("DEFAULTTEMPLATE", "PublicTemplate");
