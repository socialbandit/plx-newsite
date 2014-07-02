<?php
// 404.php | alexjet library . 404 error page | VERSION 3.6
// Copyright 2006-2010 Alexander Media, Inc. - All rights reserved.
// By: 07.25.2008_rg

// **** Global data ****
if (file_exists('config.php')){
	include_once("config.php");
	header('Location: ' . $glbl_websiteaddress); /* Redirect browser */
	exit(0);
}

echo('Page Not Found.');

?>