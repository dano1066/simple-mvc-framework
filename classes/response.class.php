<?php 
namespace core;
class Response
{
	public $ViewVars = array();
	
	public function __construct() {
		
    }
	
	public function Redirect($path)
	{
		header("Location: ".$path);
		die();
	}
	
	public function View($path = null, $template = "PublicTemplate", $responseCode = 200, $headers = array())
	{
		$responseObj = new self();
		if($path == null) $path = QUERY_VIEW;
		$view = new View($path, $template, $responseCode, $headers);
		$responseObj->AddVar("view", $view);
		
		return $responseObj;
	}
	
	public function AddVar($varname, $varvalue)
	{
		$this->ViewVars[$varname] = $varvalue;
		return $this;
	}
	
}
