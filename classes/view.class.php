<?php 
namespace core;
class View
{
	public $Template;
	public $FileName;
	public $ResponseCode;
	public $Headers = array();
		
	function __construct($filename = "", $template = "", $response = 200, $headers = array()) {
		if($filename != "") $this->FileName = $filename;
		else $this->FileName = QUERY_VIEW;
		if($template != "") $this->Template = $template;
		$this->ResponseCode = $response;
		$this->Headers = $headers;
		return $this;
	}
}
