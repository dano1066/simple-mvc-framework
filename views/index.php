<?php 
declareTemplateContent("pagetitle", "Main Index Page");


ob_start();
?>
<h1>Thank You</h1>
<p>Thank you for installing this framework. In order for development to go as smooth as possible, make sure you resolve any errors listed below.</p>
<h2>Issues</h2>
<?php
if(count($issues) == 0) echo "<span style='color:green'>No issues found, you are all set!</span>";
else {
	foreach($issues as $issue)
	{
		?>
		<strong><?php echo $issue[0];?></strong>
		<br><?php echo $issue[1];?>
		<?php 
	}
}

$content = ob_get_clean();

declareTemplateContent("maincontent", $content);
