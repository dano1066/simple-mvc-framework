<?php 
namespace core;
class CacheFile
{
	public $Path;
	public $Controller;
	public $View;
	public $Vars; 
	
	function __construct($controller, $view, $vars) {
		$urlvars = explode(",", $vars);
		
		$this->Vars = $urlvars;
		$this->Controller = $controller;
	    $this->View = $view;
	
		$this->Path = $this->GenerateFilePath($controller, $view, $urlvars);
    }
	
	public function Load()
	{
		$html = file_get_contents($this->Path);	 
		return $html;
	}
	
	public function Exists()
	{
		return file_exists($this->Path);
	}
	
	public function HasExpired()
	{
		if(file_exists($this->Path))
		{
			$lastmod = $this->CheckAge();
			$diff = time() - $lastmod;
			if($diff >= CACHETTL) return true;
			else return false;
		}
		else return true;
	}
	
	public function DeleteCacheFile()
	{
		unlink($this->GenerateFilePath());
	}
	
	public function CheckAge()
	{
		return filemtime($this->Path);
	}
	
	public function GenerateFilePath()
	{
		$filename = implode('-', $this->Vars);
		if($filename == "") $filename = "default";
		return $this->GenerareDirectory().$filename.".html";
	}
	
	public function GenerareDirectory()
	{
		return FSROOT."cache/".$this->Controller."/".$this->View."/";
	}
	
	public function Generate($content)
	{
		@mkdir($this->GenerareDirectory(), 0755, true);
		touch($this->GenerateFilePath($this->Controller, $this->View, $this->Vars));
		return file_put_contents($this->GenerateFilePath($this->Controller, $this->View, $this->Vars), $content);
	}
}
