<?php 
namespace core;
class RouteAlias
{
	public $Alias;
	public $Controller;
	public $View;
	public $UseCache;
	
    function __construct($alias, $controller, $view = null, $usecache = false) {
		$this->Alias = $alias;
		$this->Controller = $controller;
		if($view != null) $this->View = $view;
		else $this->View = $alias;
		$this->UseCache = $usecache;
    }
	
	function getURIVariables($uri)
	{
		$fragments = removeBlankElements(explode("/", $uri));
		if(STATICCONTROLLER == FALSE)
		{
			if(isset($fragments[2]))
			{
				$urivariables = array();
				for($i = 2; $i <= count($fragments)-1; $i++) $urivariables[] = $fragments[$i];
				return implode(",", $urivariables);
			}
		}
		else
		{
			if(isset($fragments[1]))
			{
				$urivariables = array();
				for($i = 1; $i <= count($fragments)-1; $i++)  $urivariables[] = $fragments[$i];
				return implode(",", $urivariables);
			}
		}
		return "";
	}
}
