<?php 
use core\RouteAlias;
$routes = array();
$CacheEnabled = true;
$CacheDisabled = false;

$routes[] = new RouteAlias("", "home", "index", $CacheEnabled);
$routes[] = new RouteAlias("index", "home", null, $CacheEnabled);
$routes[] = new RouteAlias("login", "home", null, $CacheDisabled);
$routes[] = new RouteAlias("search", "home", null, $CacheDisabled);

?>
