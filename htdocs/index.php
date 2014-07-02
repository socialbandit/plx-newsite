<?php

// Property of: Parallax Digital Studios, Inc.
// index.php
// Updated: 03.05.2011_rg

$path2root = ''; // end with / or blank if already in the root

// Implement home page
$pageid = 'home';
$sectionid = 'root/';
$uploadsfolder = 'home';
$_GET['event'] = 'void';
include($path2root . 'admin/config.php');
include($path2root . 'admin/alexjet.php');
if ($glbl_customphpscriptpath != ''){
	include($glbl_physicalwebrootlocation . $glbl_customphpscriptpath); // include the custom php script
	$templatepath = $glbl_templatesfolder . 'home.php';
	include($glbl_physicalwebrootlocation . $templatepath); // include the html template
}

?>