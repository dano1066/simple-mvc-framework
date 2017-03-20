<?php
namespace core;
class Logger
{
	public static function Init()
	{
		return new static();
	}
	
	function LogDbError($string, $dump = null)
	{
		$filename = Logger::GetLogName("sql");
		$record = $this->GetEntryTimestamp(). $string;
		if($dump != null && $dump != "") $record .= PHP_EOL.$this->LogDivide().PHP_EOL.$dump.$this->LogDivide();
		
		$this->WriteToFile($record, $filename);		
	}
	
	private function WriteToFile($string, $file)
	{
		file_put_contents($file, $string.PHP_EOL, FILE_APPEND);
	}
	
	private function LogDivide()
	{
		return "-----------------------------------------";
	}
	private function GetLogName($type)
	{
		$timestamp = date("ymd");
		$filename = FSROOT."/logs/".$type."/".$timestamp.".log";
		if(!file_exists(dirname($filename))) mkdir(dirname($filename), 0777, true);
		
		return $filename;
	}
	
	private function GetEntryTimestamp()
	{
		return date("y-m-d H:i:s");
	}
}
