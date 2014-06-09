<?php
$file = $_GET['file'];
$dir = 'd:/www/baccarat/test/';
$testFile = $dir.$file;
echo $testFile.'<br />';

if(file_exists($testFile))
{
	//
	$testInfo = `phpunit $testFile`;
	echo "phpunit $testFile".'<hr />';
	echo '<pre>';
	echo $testInfo;
	echo '</pre>';
}
else
{
	echo 'file not exists.';
}