<?php 
use core\Response;
use core\CacheFile;
use core\RouteAlias;
use core\Helpers;
################################################################################################
##										INIT METHODS
################################################################################################
function initialiseFramework()
{
	session_start();
	initIncludes();
	if(!isset($_SESSION['nb_userid']) && isset($_COOKIE["rememberme"]) && isset($_COOKIE['userid'])) Helpers::cookieLogin();
	initRoutes();
	initCache();
	initModels();
}
function initRoutes()
{
	$uri = $_SERVER['REQUEST_URI'];
	//CHECK TO SEE IF THE URI IS AN ALIAS FOR A PRE DEFINED ROUTE
	$route = getRouteAlias($uri);
	if($route != null)
	{
		if(STATICCONTROLLERNAME == false) define("QUERY_CONTROLLER", $route->Controller);
		else define("QUERY_CONTROLLER", STATICCONTROLLERNAME);
		define("QUERY_VIEW", $route->View);
		define("QUERY_IDS", $route->getURIVariables($uri));
		define("USE_CACHE", $route->UseCache);	
	}
	else
	{
		//GET THE URI VARIABLES AND FILTER OUT THE INVALID FIELDS.
		$fragments = removeBlankElements(explode("/", $uri));
		
		//SET THE GLOBAL URI FIELDS
		if($fragments != 0)
		{
			if(STATICCONTROLLER == FALSE)
			{
				define("QUERY_CONTROLLER", $fragments[0]);
				define("QUERY_VIEW", $fragments[1]);
				$urivariables = array();
				for($i = 2; $i <= count($fragments)-1; $i++) $urivariables[] = $fragments[$i];
				define("QUERY_IDS", implode(",", $urivariables));
			}
			//STATIC CONTROLLER ONLY USES ONE CONTROLLER AND DOESNT NEED IT TO BE DEFINED IN THE URL
			else
			{
				define("QUERY_CONTROLLER", STATICCONTROLLERNAME);
				if(isset($fragments[0]) && $fragments[0] != "" && $fragments[0] != null) define("QUERY_VIEW", $fragments[0]);
				else define("QUERY_VIEW", "index");
				$urivariables = array();
				for($i = 1; $i <= count($fragments)-1; $i++)  $urivariables[] = $fragments[$i];
				define("QUERY_IDS", implode(",", $urivariables));
				echo QUERY_CONTROLLER." - ".QUERY_VIEW." - ".QUERY_IDS."<br>";die();
			}
		}
		else
		{
			define("QUERY_CONTROLLER", "home");
			define("QUERY_VIEW", "index");
			define("QUERY_IDS", "");
		}
		define("USE_CACHE", ENABLECACHE); //since it hasnt been defined then simply use whatever was set as the default value for cache
	}
	if(DEBUG == true) echo QUERY_CONTROLLER." - ".QUERY_VIEW." - ".QUERY_IDS."<br>";
}

function initIncludes()
{
	require_once FSROOT."classes/database.class.php";
	require_once FSROOT."classes/route.class.php";
	require_once FSROOT."classes/logger.class.php";	
	require_once FSROOT."classes/view.class.php";
	require_once FSROOT."classes/controller.class.php";
	require_once FSROOT."classes/response.class.php";
	require_once FSROOT."classes/model.class.php";
	require_once FSROOT."classes/cachefile.class.php";
	require_once FSROOT."helpers.php";	
	require_once FSROOT."hooks/init.php";
}

function initCache()
{
	if(USE_CACHE == true) {
		$cache = new CacheFile(QUERY_CONTROLLER, QUERY_VIEW, QUERY_IDS);
		if($cache->Exists() && $cache->HasExpired() == false) {
			if(DEBUG == true) echo "Cache Loaded";
			echo $cache->Load();
			die();
		}
		else {
			if(DEBUG == true) echo "Cache Expired, refreshing";
			define("SAVE_CACHE", true);
		}
	}
}

function initModels()
{
	$files = scandir (FSROOT."/models");
	foreach($files as $file)
	{
		if(pathinfo($file, PATHINFO_EXTENSION) == "php") include FSROOT."/models/".$file;
	}
}
################################################################################################
##										CORE METHODS
################################################################################################

function initialiseController()
{
	$controllerpath = FSROOT."controllers/".QUERY_CONTROLLER.".controller.php";
	if(file_exists($controllerpath))
	{
		require_once $controllerpath;
		if(method_exists(QUERY_CONTROLLER, QUERY_VIEW))
		{
			$method = new ReflectionMethod(QUERY_CONTROLLER, QUERY_VIEW);
			$num = $method->getNumberOfParameters();
			if($num == count(getRequestVars())) 
				$post = @call_user_func_array(QUERY_CONTROLLER."::".QUERY_VIEW, getRequestVars());
			else generate404("Invalid number of arguments", "The number of arguments supplied are invalid. The ".QUERY_VIEW." page requires ". $num. " variable and received ".count(getRequestVars()) );
		}
		else generate404("Invalid View".QUERY_CONTROLLER."::".QUERY_VIEW);

	}
	else generate404("Invalid Controller");
	
	if($post == null) die();
	return $post;
}

function initialiseView($response)
{
	if($response != null && $response->ViewVars["view"] != null)
	{
		ob_start();
		foreach($response->ViewVars as $varkey => $varval){
			${$varkey} = $varval;
		}
		
		$view = $response->ViewVars["view"];
		http_response_code($view->ResponseCode);
		foreach($view->Headers as $header) header($header, true);
		if($_SERVER['REQUEST_METHOD'] == "HEAD") die();
		
		if(file_exists(FSROOT."views/".$view->FileName.".php")) include FSROOT."views/".$view->FileName.".php";
		else generate404("404 - Invalid View", "Critical Error: View '".$view->FileName."' could not be found. ");	
		
		if(file_exists(FSROOT."views/templates/".$view->Template.".php")) include FSROOT."views/templates/".$view->Template.".php";
		else generate404("404 - Invalid Template", "Critical Error: Template '".$view->Template."' could not be found");
		
		$content = ob_get_clean();
		echo $content;	
	}
	else generate404();
	
	if(defined("SAVE_CACHE") && SAVE_CACHE == true)
	{
		$cache = new CacheFile(QUERY_CONTROLLER, QUERY_VIEW, QUERY_IDS);
		$cache->Generate($content);
	}
	
}
################################################################################################
##										CORE HELPERS
################################################################################################
function getRequestVars()
{
	return removeBlankElements(explode(",", QUERY_IDS));
}

function generate404($title = "404 Not Found", $message = "This page could not be found")
{
	global $response;
	http_response_code (404);
	if(file_exists(getViewsDir()."404.php"))
	{
		$response = Response::View("404", DEFAULTTEMPLATE, 404)->AddVar("title", $title)->AddVar("message", $message);
		initialiseView($response);
	}
	else
	{
		echo "<h1>$title</h1>";
		echo "<p>$message</p>";
	}
	die();
}

function generate500($title = "500 - Critical Error", $message = "A Critical error has occurred")
{
	http_response_code (500);
	echo "<h1>$title</h1>";
	echo "<p>$message</p>";
	die();
}

function getRouteAlias($uri)
{
	if(trim($uri) == "/") $fragments = array("");
	else $fragments = removeBlankElements(explode("/", $uri));
	
	if($fragments)
	{
		$alias = $fragments[0];
		require_once FSROOT."routes.php";
		foreach($routes as $route) {
			if($route->Alias == $alias) return $route;		
		}
	}
	return null;
}

function getViewsDir()
{
	return FSROOT."views/";
}
function getDefaultTemplate()
{
	return getViewsDir()."templates/".DEFAULTTEMPLATE.".php";
}
function removeBlankElements($array)
{
	$fragments = array();
	foreach($array as $fragment)
	{
		if($fragment != "") 
		{
			//if they exist strip out any standard get variables from the url.
			if(strpos($fragment, "?") !== false) {
				if(substr(trim($fragment), 0, 1) != "?") {
					echo substr(trim($fragment), 0, 1) ;
					$newfragment = explode("?", trim($fragment))[0];
					if($newfragment != "") $fragments[] = $newfragment;
				}
			}
			else {
				$fragments[] = $fragment;
			}
		}
	}
	
	return $fragments;
}

function renderTemplateContent($variable)
{
	global $response;
	if(isset($response->ViewVars[$variable]) && $response->ViewVars[$variable] != null && $response->ViewVars[$variable] != "") echo $response->ViewVars[$variable];
}

function declareTemplateContent($varname, $varval)
{
	global $response;
	$response->AddVar($varname, $varval);
}

function outputError($errormessage)
{
	echo "<title>An Error Has Occurred</title><div style='width: 80%;margin-left: 10%; margin-right: 10%;padding: 20px;background-color: #ececec;border-radius: 5px;margin-top:50px;'><h1 style='margin-top:0px;'>An Error has occurred</h1>".$errormessage."</div>";
	if(DIEONERROR == true) die();
}
