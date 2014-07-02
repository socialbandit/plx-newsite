<?php
// alexjet.php | alexjet PHP function library | VERSION 3.6
// Copyright 2006-2010 Alexander Media, Inc. - All rights reserved.
// By: 08.14.2006_rg, 02.06.2007_rg, 02.24.2007_rg, 04.16.2007_rg, 11.15.2007_rg, 01.26.2008_rg, 
//     02.23.2008_rg, 03.24.2008_rg, 05.23.2008_rg, 08.05.2008_rg, 09.30.2008_rg, 10.06.2008_rg,
//     12.29.2008_rg, 01.22.2009_rg, 04.27.2009_rg, 05.01.2009_rg, 05.06.2009_rg, 07.24.2009_rg,
//     10.26.2009_rg, 11.24.2009_rg, 12.14.2009_rg, 12.23.2009_rg, 02.23.2010_rg, 04.21.2010_rg,
//     06.10.2010_rg, 09.10.2010_rg, 09.20.2010_rg, 11.06.2010_rg, 11.16.2010_rg, 11.21.2010_rg

// **** Authentication & Member login functions ****
function getUserByUID($UID){
	global $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link) 
		return false;
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
		return false;
	// "Inject" prevention   
	$UID = mysql_real_escape_string($UID);
	// Construct query
	$querystring = 'SELECT * FROM ' . $glbl_dbtableprefix . 'users WHERE UID = \'' . $UID . '\' LIMIT 1';
	// Perform query 
	$result = mysql_query($querystring);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result)
  		return false;
	// Close connection
	mysql_close($link);	   
	return $result;
}

function getAuthenticationFlag() {
	if (isset($_COOKIE['alexjet'])){
		// Verify UID in users table
		$UID = '';
		$usersquery = getUserByUID($_COOKIE['alexjet']); // Returns only one row
		while ($row = mysql_fetch_assoc($usersquery))
			$UID = $row['UID'];
		if ($_COOKIE['alexjet'] == $UID && trim($UID) != '')
			return true;
	}		
	return false;
}

function getMemberAuthenticationFlag() {
	// Initialize data object
	global $glbl_companyname;
	if (isset($_COOKIE['alexjet_mmbr']))
		if (strtolower($_COOKIE['alexjet_mmbr']) == strtolower($glbl_companyname))
			return true;
	return false;
}

function authenticateMember() {
	// Initialize data object
	global $glbl_companyname;
	setcookie('alexjet_mmbr', $glbl_companyname, 0, '/');
	return;
}

function memberLogin() {
	global $glbl_dbtableprefix;
	$validloginflag = false;
	$validloginmessage = '';
	$username = '';
	$code = '';
	$formtokencode = '';
	if (isset($_POST['username']) || isset($_POST['code'])){ // Login form submitted
		// Validation
		if (isset($_POST['username'])) $username = trim($_POST['username']);
		if (isset($_POST['code'])) $code = trim($_POST['code']);
		if (isset($_POST['citycode'])) $formtokencode = trim($_POST['citycode']);
		if ($username == '' || strlen($username) > 15 || $code == '' || strlen($code) > 15 || !pingToken($formtokencode)){ // invalid format
			$validloginflag = false;
			$validloginmessage = 'Invalid. Please try again.';
		}
		else{ // Authenticate
			$validloginflag = false;
			$validloginmessage = 'Invalid. Please try again.';
			// Query database
			$memberliststring = ('%<br>' . $username . ',' . $code . '<br>%');
			$memberlistquery = execQuery('SELECT Content FROM ' . $glbl_dbtableprefix . 'editablecontent WHERE ContentID = \'ADMIN_memberslist\' AND ' .
			                             'CONCAT(\'<br>\', Content, \'<br>\') LIKE \'' . $memberliststring . '\' LIMIT 1');
			while ($row = mysql_fetch_assoc($memberlistquery)){ // User found
				authenticateMember(); // Authenticate
				$validloginflag = true;
				$validloginmessage = '';
			} // while	
		} // else	
	} // if
	else{ // Verify authentication
		if (!getMemberAuthenticationFlag())
			$validloginflag = false;
		else
			$validloginflag = true;
	}
	// Login form
	if (!$validloginflag){
		return '<script type="text/javascript">function vldtmmbrlgnfrm(frmObj){if (jsTrim(frmObj.username.value) == \'\' || jsTrim(frmObj.code.value) == \'\'){alert(\'Both fields are required.\'); return false;} else return true;}</script>' .
			   '<div class="memberLoginTag">' .
		       '<form id="_memberloginform" name="_memberloginform" action="" method="post" onsubmit="javascript:return vldtmmbrlgnfrm(this);"><table border="0">' .
			   '<tr><td>&nbsp;</td><td><span>' . $validloginmessage . '</span></td></tr>' .
			   '<tr><td id="memberLoginTag_username_label">User Name:&nbsp;</td><td id="memberLoginTag_username_field"><input type="text" id="username" name="username" maxlength="15"></td></tr>' .
			   '<tr><td id="memberLoginTag_code_label">Password:&nbsp;</td><td id="memberLoginTag_code_field"><input type="password" id="code" name="code" maxlength="15"></td></tr>' .
			   '<tr><td>&nbsp;</td><td><input type="hidden" name="citycode" id="citycode" value="us8500" /><input type="submit" name="submit" value="Login"></td></tr>' .
			   '</table></form></div>';
	}
	else
		return '';
}

function memberLogout() {
	// reset cookie
	setcookie('alexjet_mmbr', '', 0, '/');
    return;
}

// **** General functions ****
function renderPageCanvas($pagetitle, $pagebody, $jscode, $jscodeonload, $externlibs, $returnpagepath) {
	global $glbl_companyname, $glbl_websiteaddress;
	// Render HTML page canvas
	if (trim($jscode) != '')
		$jscode = '<script type="text/javascript">' . $jscode . '</script>';
	if (trim($jscodeonload) != '')
		$jscodeonload = ' onload="javascript:' . $jscodeonload . '"';
	$externlibs = explode(',', $externlibs); // Convert string to array to extract the external library keywords (if any)
	$externlibs_css = '';
	$externlibs_js = '';
	foreach($externlibs as $externlibs_each) { // Each library keyword
		if (trim($externlibs_each) == 'jquery'){
			$externlibs_js .= '<script type="text/javascript" src="jquery.js"></script>';
		} // if 'jquery'			
		else if (trim($externlibs_each) == 'imgareaselect'){
			$externlibs_css .= '<link rel="stylesheet" type="text/css" href="imgareaselect/imgareaselect-default.css" />';			
			$externlibs_js .= '<script type="text/javascript" src="imgareaselect/jquery.imgareaselect.min.js"></script>';
		} // if 'imgareaselect'
		else if (trim($externlibs_each) == 'dojo'){
			$externlibs_js .= '<script type="text/javascript" src="dojo.js"></script>';
		} // if 'dojo'
		else if (trim($externlibs_each) == 'alexjet'){
			$externlibs_js .= '<script type="text/javascript" src="alexjet.js"></script>';
		} // if 'alexjet'
	} // foreach	
	$indexrootfolder = trim(parse_url($glbl_websiteaddress, PHP_URL_PATH));
	if (strlen($indexrootfolder) >= 1)
		if (substr($indexrootfolder, 0, 1) == '/')
			$indexrootfolder = substr($indexrootfolder, 1, strlen($indexrootfolder)); // Extract leading slash '/' if found
	$pagestring = '<html><head>' .
				  '<title>' . $pagetitle . '</title>' .
	              '<style type="text/css">' .
				  '.copy {color:#000000; font-family:Arial; font-size:11px; font-weight:bold;}' .
  				  '.copy a:link {color:#000000; font-family:Arial; font-size:11px; font-weight:bold; text-decoration:underline;}' .
  				  '.copy a:visited {color:#000000; font-family:Arial; font-size:11px; font-weight:bold; text-decoration:underline;}' .
  				  '.copy a:hover {color:#990000; font-family:Arial; font-size:11px; font-weight:bold; text-decoration:none;}' .
  				  '.copy a:active {color:#000000; font-family:Arial; font-size:11px; font-weight:bold; text-decoration:underline;}' .
				  '.copyselected {color:#990000; font-family:Arial; font-size:11px; font-weight:bold;}' .
  				  '.copyselected a:link {color:#990000; font-family:Arial; font-size:11px; font-weight:bold; text-decoration:underline;}' .
  				  '.copyselected a:visited {color:#990000; font-family:Arial; font-size:11px; font-weight:bold; text-decoration:underline;}' .
  				  '.copyselected a:hover {color:#000000; font-family:Arial; font-size:11px; font-weight:bold; text-decoration:none;}' .
  				  '.copyselected a:active {color:#990000; font-family:Arial; font-size:11px; font-weight:bold; text-decoration:underline;}' .
				  '.copyred {background-color:#990000; color:#ffffff; font-family:Arial; font-size:14px; font-weight:bold;}' .
  				  '.copyred a:link {background-color:#990000; color:#ffffff; font-family:Arial; font-size:14px; font-weight:bold; text-decoration:underline;}' .
  				  '.copyred a:visited {background-color:#990000; color:#ffffff; font-family:Arial; font-size:14px; font-weight:bold; text-decoration:underline;}' .
  				  '.copyred a:hover {background-color:#990000; color:#ffffff; font-family:Arial; font-size:14px; font-weight:bold; text-decoration:none;}' .
  				  '.copyred a:active {background-color:#990000; color:#ffffff; font-family:Arial; font-size:14px; font-weight:bold; text-decoration:underline;}' .
				  '.copygray {background-color:#cccccc; color:#000000; font-family:Arial; font-size:14px; font-weight:bold;}' .
  				  '.copygray a:link {background-color:#cccccc; color:#000000; font-family:Arial; font-size:14px; font-weight:bold; text-decoration:underline;}' .
  				  '.copygray a:visited {background-color:#cccccc; color:#000000; font-family:Arial; font-size:14px; font-weight:bold; text-decoration:underline;}' .
  				  '.copygray a:hover {background-color:#990000; color:#ffffff; font-family:Arial; font-size:14px; font-weight:bold; text-decoration:none;}' .
  				  '.copygray a:active {background-color:#cccccc; color:#000000; font-family:Arial; font-size:14px; font-weight:bold; text-decoration:underline;}' .
				  '.copyredsmall {color:#990000; font-family:Arial; font-size:9px; font-weight:bold;}' .	
				  '.canvas {border-top:1px #000000 solid; border-right: 1px #000000 solid; border-bottom: 1px #000000 solid; ' .
				  'border-left:1px #000000 solid; width:100%; height:100%;}' . '</style>' .
				  $externlibs_css .
				  '<script type="text/javascript">var bypassinitflag=true; var indexrootfolder = \'' . $indexrootfolder . '\';</script>' .
				  $externlibs_js . $jscode . '</head><body' . $jscodeonload . '>' .
				  '<table border="0" cellpadding="0" cellspacing="0" class="canvas"><tr><td valign="top" align="left">' .
				  '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td width="50%" align="left">' .
				  '&nbsp;<span class="copygray">&nbsp;&lt;&nbsp;<a href="' . $returnpagepath . '" title="Return To Parent Page">Return</a>&nbsp;</span>' .
				  '&nbsp;<span class="copyred">&nbsp;' . $pagetitle . '&nbsp;</span>&nbsp;<span class="copygray">&nbsp;' . $glbl_companyname . '&nbsp;</span></td>' .
				  '<td width="50%" align="right">&nbsp;<span class="copyred">&nbsp;By Alexander Media&nbsp;</span>&nbsp;</td></tr></table>' .
			      '<br />' . $pagebody . '</td></tr></table>' .
				  '</body></html>';
	return $pagestring;
}

function getImagesData($uploadfoldername, $imagesortorder, $excludethumbsflag) {
	global $glbl_uploadsrootfolder, $glbl_physicalwebrootlocation, $glbl_thumbnailimages_prefix;
	// Return the names for images found in a given folder
	$files = array();
	$dir = $glbl_physicalwebrootlocation . $glbl_uploadsrootfolder . $uploadfoldername . "/";
	if (!file_exists($dir))
		return $files;
	$dh  = opendir($dir);
  	while (false !== ($filename = readdir($dh))) {
       if(filetype($dir . $filename) == "file"){ // only files
			$fileextension = strrchr($filename, ".");
			if (strtolower($fileextension) == '.jpg' || strtolower($fileextension) == '.jpeg' ||
			    strtolower($fileextension) == '.gif' || strtolower($fileextension) == '.png'){ // allowed extensions
				if ($excludethumbsflag == TRUE){
					if (stristr($filename, $glbl_thumbnailimages_prefix) == FALSE) // allowed files
						$files[] = $filename;
				}		
				else
					$files[] = $filename;
			} // if				
	   } // if
	}
	if (strtolower($imagesortorder) == "random")
		shuffle($files); // randomize array order
	elseif (strtolower($imagesortorder) == "alphadesc"){
		natcasesort($files); // alpha
		$files = array_reverse($files, true); // descending order
	}	
	else // Default
		natcasesort($files); // alpha
	return $files;
}

function fetchImageInformation($ID) {
	// Return image information from the database
	global $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix,
	   	   $glbl_backgroundcolor_default, $glbl_notes_default;
	$backgroundcolor = $glbl_backgroundcolor_default;
	$notes = $glbl_notes_default;
	$keywords = '';
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Make query to select image information
	$query = sprintf('SELECT * FROM ' . $glbl_dbtableprefix . 'imageinformation WHERE ID = \'' . $ID .
					 '\' ORDER BY Updated DESC LIMIT 1');
	// Perform Query
	$result = mysql_query($query);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	while ($row = mysql_fetch_assoc($result)) {
		$backgroundcolor = $row['BackgroundColor'];
		$notes = $row['Notes'];
		$notes = str_replace("'", "\'", $notes); // escape single quote for Javascript 
		$notes = str_replace('\"', '"', $notes); // replace extra slash 
		$notes = str_replace('\\\\', '\\', $notes); // replace extra slash 			
		$keywords = $row['Keywords'];
		$keywords = str_replace("'", "\'", $keywords); // escape single quote for Javascript 	
		$keywords = str_replace('\"', '"', $keywords); // replace extra slash 
		$keywords = str_replace('\\\\', '\\', $keywords); // replace extra slash 	
	}
	// Close connection
	mysql_close($link);	   
	// Add to array
	$imageinfoarray = array('backgroundcolor' => $backgroundcolor,
	                        'notes' => $notes,
							'keywords' => $keywords);
	return 	$imageinfoarray;
}

function fetchEditableContentInformation($ContentID) {
	// Return editable content information from the database
	global $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
	// Initialize	
	$content = '';
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Make query to select image information
	$query = sprintf('SELECT * FROM ' . $glbl_dbtableprefix . 'editablecontent WHERE ContentID = \'' . addslashes($ContentID) . '\'');
	// Perform Query
	$result = mysql_query($query);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	while ($row = mysql_fetch_assoc($result)) {
		$content = $row['Content'];
		$content = str_replace("'", "\'", $content); // escape single quote for Javascript
		$content = str_replace("\r", "", $content); // remove carriage return
		$content = str_replace("\n", "<br>", $content); // replace line feed
		$content = str_replace('\"', '"', $content); // replace extra slash 
		$content = str_replace("\\\\", "\\", $content); // replace extra slash 
	}
	// Close connection
	mysql_close($link);	   
	return $content;
}

function fetchSelectableImageInformation($ImageID, $FolderName) {
	// Return editable content information from the database
	global $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
	// Initialize	
	$imagepath = '';
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Make query to select image information
	$query = sprintf('SELECT ImagePath FROM ' . $glbl_dbtableprefix . 'selectableimages WHERE ImageID = \'' . addslashes($ImageID) . '\' AND FolderName = \'' . addslashes($FolderName) . '\' LIMIT 1');
	// Perform Query
	$result = mysql_query($query);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	while ($row = mysql_fetch_assoc($result)) {
		$imagepath = $row['ImagePath'];
		$imagepath = str_replace("'", "\'", $imagepath); // escape single quote for Javascript
		$imagepath = str_replace("\r", "", $imagepath); // remove carriage return
		$imagepath = str_replace("\n", "<br>", $imagepath); // replace line feed
		$imagepath = str_replace('\"', '"', $imagepath); // replace extra slash 
		$imagepath = str_replace("\\\\", "\\", $imagepath); // replace extra slash 
	}
	// Close connection
	mysql_close($link);	   
	return $imagepath;
}

function getSoundFiles($uploadfoldername) {
	global $glbl_uploadsrootfolder, $glbl_physicalwebrootlocation;
	// Return the names for files found in a given folder
	$files = array();
	$dir = $glbl_physicalwebrootlocation . $glbl_uploadsrootfolder . $uploadfoldername . "/";
	if (!file_exists($dir))
		return $files;
	$dh  = opendir($dir);
  	while (false !== ($filename = readdir($dh))) {
       if(filetype($dir . $filename) == "file"){ // only files
			$fileextension = strrchr($filename, ".");
			if (strtolower($fileextension) == '.mp3' || strtolower($fileextension) == '.mp4') // allowed extensions
				$files[] = $filename;
	   } // if
	} //  while
	natcasesort($files); // alpha
	return $files;
}

function getPDFFiles($uploadfoldername) {
	global $glbl_uploadsrootfolder, $glbl_physicalwebrootlocation;
	// Return the names for files found in a given folder
	$files = array();
	$dir = $glbl_physicalwebrootlocation . $glbl_uploadsrootfolder . $uploadfoldername . "/";
	if (!file_exists($dir))
		return $files;
	$dh  = opendir($dir);
  	while (false !== ($filename = readdir($dh))) {
       if(filetype($dir . $filename) == "file"){ // only files
			$fileextension = strrchr($filename, ".");
			if (strtolower($fileextension) == '.pdf') // allowed extensions
				$files[] = $filename;
	   } // if
	} //  while
	natcasesort($files); // alpha
	return $files;
}

function getSubFolders($uploadfoldername) {
	global $glbl_uploadsrootfolder, $glbl_physicalwebrootlocation;
	// Return the names for directories found under a given folder
	$directories = array();
	$dir = $glbl_physicalwebrootlocation . $glbl_uploadsrootfolder . $uploadfoldername . "/";
	if (!file_exists($dir))
		return $directories;
	$dh  = opendir($dir);
  	while (false !== ($directoryname = readdir($dh)))
       if(filetype($dir . $directoryname) == "dir") // only directories
       		if ($directoryname != '.' && $directoryname != '..')
				$directories[] = $directoryname;
	natcasesort($directories); // alpha
	return $directories;
}

function removeFile($imagepath) {
	global $glbl_physicalwebrootlocation;
	$imagepath = $glbl_physicalwebrootlocation . $imagepath;
	// must be logged in
	if(getAuthenticationFlag() && file_exists($imagepath))
		unlink($imagepath);
}

function renameFile($imagepath, $imagenewname) {
	global $glbl_physicalwebrootlocation;
	$imagepath = $glbl_physicalwebrootlocation . $imagepath;
	// must be logged in
	if(getAuthenticationFlag() && file_exists($imagepath) && trim($imagenewname) != ''){
		$imagenewname_infoarray = pathinfo($imagepath);
		$imagenewname = $imagenewname_infoarray['dirname'] . '/' . $imagenewname;
		rename($imagepath, $imagenewname);
	} // if
}

function resizeImageFile($imagepath, $imagenewpath, $imagewidth, $imageheight, $imagexcoordinate, $imageycoordinate, $copyflag) {
	global $glbl_physicalwebrootlocation;
	$imagepath = $glbl_physicalwebrootlocation . $imagepath;
	$imagenewpath = $glbl_physicalwebrootlocation . $imagenewpath;
	if(!is_numeric($imagewidth)) $imagewidth = 0;
	if(!is_numeric($imageheight)) $imageheight = 0;	
	if(!is_numeric($imagexcoordinate)) $imagexcoordinate = 0;
	if(!is_numeric($imageycoordinate)) $imageycoordinate = 0;
	// must be logged in
	if(getAuthenticationFlag() && file_exists($imagepath) && trim($imagenewpath) != '' && ($imagewidth > 0 || $imageheight > 0)){
		$system = explode(".", $imagepath);
		if(preg_match("/jpg|jpeg/", $system[1]))
			$sourceimage = imagecreatefromjpeg($imagepath);
		elseif(preg_match("/gif/", $system[1]))
			$sourceimage = imagecreatefromgif($imagepath);
		elseif(preg_match("/png/", $system[1]))
			$sourceimage = imagecreatefrompng($imagepath);
		else
			return;	
		$sourceimage_width = imageSX($sourceimage);
		$sourceimage_height = imageSY($sourceimage);
		if($imagewidth > 0 && $imageheight > 0){ // Resize. Do not keep aspect ratio
			$newimage_width = $imagewidth;
			$newimage_height = $imageheight;
		} // if
		elseif($imagewidth > 0){ // Resize on the x-direction, keeping aspect ratio on the y-direction resize
			$newimage_width = $imagewidth;
			$newimage_height = ($imagewidth * $sourceimage_height) / $sourceimage_width;
		}
		elseif($imageheight > 0){ // Resize on the y-direction, keeping aspect ratio on the x-direction resize
			$newimage_width = ($imageheight * $sourceimage_width) / $sourceimage_height;
			$newimage_height = $imageheight;
		}
		$destinationimage = ImageCreateTrueColor($newimage_width, $newimage_height);
		if ($copyflag){ // Copy a portion of the source image into the new image (No resizing)
            // int imagecopy ( resource dst_image, resource src_image, int dst_x, int dst_y, int src_x, int src_y, int src_w, int src_h )
			imagecopy($destinationimage, $sourceimage, 0, 0, $imagexcoordinate, $imageycoordinate, $newimage_width, $newimage_height);
		}	
		else{ // Resize
			// bool imagecopyresampled ( resource dst_image, resource src_image, int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h )	
		    imagecopyresampled($destinationimage, $sourceimage, 0, 0, 0, 0, $newimage_width, $newimage_height, $sourceimage_width, $sourceimage_height);
		}
		if (preg_match("/png/",$system[1]))
			imagepng($destinationimage, $imagenewpath); 
		elseif(preg_match("/gif/",$system[1]))
			imagegif($destinationimage, $imagenewpath);
		else
			imagejpeg($destinationimage, $imagenewpath); 
		imagedestroy($destinationimage); 
		imagedestroy($sourceimage);  
	} // if
}

function serializeQuery2js($queryresultset, $callbackfunction) {
	$jsstrng = '';
	$cnt = 0; // counter
	while ($rowarray = mysql_fetch_assoc($queryresultset)) { // loop over rows in query
		++$cnt;
		if ($cnt == 1)
			$jsstrng = 'var php2jsDtObjct = new Object();' . chr(10);
		$jsstrng .= 'var __rs' . $cnt . ' = new Object();' . chr(10);
		$rowarraykeys = array_keys($rowarray); // get keys (column names) for each row
		foreach ($rowarraykeys as $rowcolumnname){ // loop over keys			
			$value = $rowarray[$rowcolumnname]; // get value
			$value = str_replace("'", "\'", $value); // escape single quote for Javascript
			$value = str_replace("\r", "", $value); // remove carriage return
			$value = str_replace("\n", "<br>", $value); // replace line feed
			$value = str_replace('\"', '"', $value); // replace extra slash 
			$value = str_replace("\\\\", "\\", $value); // replace extra slash 
			$jsstrng .= '__rs' . $cnt . '.' . strtolower($rowcolumnname) . ' = \'' . $value . '\';' . chr(10);
		} // foreach 
		$jsstrng .= 'php2jsDtObjct.rs' . $cnt . ' = __rs' . $cnt . ';' . chr(10);
	} // while 
	if ($jsstrng == '') // query was empty
		$jsstrng = 'var php2jsDtObjct = new Object();' . chr(10) .
				   'php2jsDtObjct.empty = \'nada\';' . chr(10);
	if (strlen($callbackfunction) && $jsstrng != '')
		$jsstrng .= $callbackfunction . '(php2jsDtObjct);' . chr(10);
	return $jsstrng;
}

function execQuery($QueryStatement) {
	// Return query
	global $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename;
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Perform Query
	$result = mysql_query($QueryStatement);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $QueryStatement;
   		die($message);
	}
	// Close connection
	mysql_close($link);	   
	return $result;
}

function makeUC() {
	// Calculate time elapsed (in seconds) since 12/31/2007 @ 23:59:59
	$anchortimepoint = strtotime("31 December 2007 23:59:59");
	// Difference between 'anchortimepoint' and 'now'
	$secssincedayone = strtotime("now") -  $anchortimepoint;
	// Get 1st random number
	$trand = rand("1000", "9999");
	// Convert 'trand' to alpha
	$alphastring = "ABCDEFGHIJ";
	$trand2 = '';
	for ($m=0; $m < strlen($trand); $m++) 
		$trand2 = $trand2 . substr($alphastring, (substr($trand, $m, 1)-1), 1);
	// Get 2nd random number
	$trand = rand("1", "20");
	// Convert 'trand' to alpha (one character)
	$alphastring = "CDEGHIJLMNPQRSTUVWYZ";
	$trand3 = substr($alphastring, $trand, 1);
	// Construct UC
	return ($secssincedayone . $trand2 . $trand3);
}

function makeTimeInSecs($IntervalInSecs) {
	// Calculate time elapsed (in seconds) since 12/31/2007 @ 23:59:59
	$anchortimepoint = strtotime("31 December 2007 23:59:59");
	// Difference between 'anchortimepoint' and 'now'
	$secssincedayone = strtotime("now") -  $anchortimepoint;
	// Add interval in seconds, if 0, current time in "seconds since day one" is returned
	$secssincedayone = $secssincedayone + $IntervalInSecs;
	return $secssincedayone;
}

function makeToken($IntervalInSecs) {
	global $glbl_dbtableprefix;
	// Remove expired tokens
	execQuery('DELETE FROM ' . $glbl_dbtableprefix . 'tokens WHERE Expiration < ' . makeTimeInSecs(0) . ' ');
	// Make token	
	$uctoken = makeUC();
	// Add token to db.	
	execQuery('INSERT INTO ' . $glbl_dbtableprefix . 'tokens (TokenID, Expiration) ' .
			  'VALUES (\'' . $uctoken . '\',  ' . makeTimeInSecs($IntervalInSecs) . ')');
	return $uctoken;
}

function pingToken($TokenID) {
	global $glbl_dbtableprefix;
	// Remove expired tokens
	execQuery('DELETE FROM ' . $glbl_dbtableprefix . 'tokens WHERE Expiration < ' . makeTimeInSecs(0) . ' ');
	// Check if token exists
	$tokensquery = execQuery('SELECT TokenID FROM ' . $glbl_dbtableprefix . 'tokens WHERE TokenID = \'' . $TokenID . '\' LIMIT 1');
	while ($row = mysql_fetch_assoc($tokensquery))
		return true;
	return false;
}

function removeToken($TokenID) {
	global $glbl_dbtableprefix;
	// Remove expired tokens
	execQuery('DELETE FROM ' . $glbl_dbtableprefix . 'tokens WHERE Expiration < ' . makeTimeInSecs(0) . ' ');
	// Delete token
	$tokensquery = execQuery('DELETE FROM ' . $glbl_dbtableprefix . 'tokens WHERE TokenID = \'' . $TokenID . '\' ');
	return true;
}

function execUserDefinedFunction($usrdefinedfunctionname){
	// Execute a user defined function from the custom php script
	global $glbl_physicalwebrootlocation, $glbl_customphpscriptpath, $glbl_templatesfolder;
	if ($glbl_customphpscriptpath != ''){
		include_once($glbl_physicalwebrootlocation . $glbl_customphpscriptpath); // include the custom php script
		if(function_exists($usrdefinedfunctionname))
			$resultval = call_user_func($usrdefinedfunctionname);
	}
	if (isset($resultval))
		return $resultval;
	return;
}

function detectMobileDevice_iPhone(){
	// Detect mobile platfrom is iPhone or iPod	
	if((bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPhone') || (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPod'))
		return true;
	return false;
}

function detectMobileDevice_iPad(){
	// Detect mobile platfrom is iPhone or iPod	
	if((bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad'))
		return true;
	return false;
}

// **** Parsing functions ****
function delimitedSubstring($sourcestring, $beginningstring, $endingstring, $startposition) {
	$startpos = 0;
	$endpos = 0;
	$delimitedstring = "";
	$delimitedstring2 = "";
	$startpos = stripos($sourcestring, $beginningstring, $startposition); // Locate beginning string
	if ($startpos) {
		$endpos = stripos($sourcestring, $endingstring, ($startpos+1)); // Locate ending string
		if ($endpos) {
			$delimitedstring = substr($sourcestring, $startpos, (($endpos - $startpos) + strlen($endingstring))); // Extract substring
			// Delimited substring without beginning and ending strings
			$delimitedstring2 = str_replace($beginningstring, '', $delimitedstring); 
			$delimitedstring2 = str_replace($endingstring, '', $delimitedstring2); 
		}
		else
			$startpos = 0; // Reset
	} // if 
	$delimitedstringarray = array('substring' => $delimitedstring,
								  'innersubstring' => $delimitedstring2,
								  'startpos' => $startpos);
	return $delimitedstringarray;
}

// **** Dynamic page construction functions ****
function fn_makeid($editableareaname) {
	global $sectionid, $pageid;
	$idstring = $pageid . '_' . $editableareaname;
	if ($sectionid != '')
		$idstring = $sectionid . $idstring;
	return $idstring;	
}

// **** Event functions ****
function event_initializeData($uploadfoldername, $editableareas_idlist, $selectableimages_idlist, $imagesortorder) {
	// Initialize data object
	global $glbl_uploadsrootfolder, $glbl_physicalwebrootlocation, $glbl_maxnumberofimages, $glbl_allowpagemanagement,
	       $glbl_thumbnailimages_prefix, $glbl_companyname, $glbl_createdyear;
	$strng = "dt.currentyear='" . date("Y") . "';" . chr(10);  // Current year variable
	$strng = $strng . "dt.companyname='" . $glbl_companyname . "';" . chr(10);
	$strng = $strng . "dt.createdyear='" . $glbl_createdyear . "';" . chr(10);
	// Images structure
	if ($uploadfoldername != ""){
		$filesArray = getImagesData($uploadfoldername, $imagesortorder, TRUE);
		$cnt = 0; // counter
		foreach ($filesArray as $fileentry) {
		   ++$cnt;
		   $imagePath = $glbl_uploadsrootfolder . $uploadfoldername . "/" . $fileentry;
		   $thumbnail = $glbl_uploadsrootfolder . $uploadfoldername . "/" . $glbl_thumbnailimages_prefix . $fileentry;
		   $imageinfoarray = fetchImageInformation($imagePath);
		   extract($imageinfoarray);
		   if ( $cnt <= $glbl_maxnumberofimages ) // gather upto 'maxnumberofimages'
			   $strng = $strng . 
						"dt.imagesnamelist.push(new imageobject('" . $imagePath . "', '" .
						$backgroundcolor . "', '" . $notes . "', '" . $keywords . "', '" .
						$thumbnail . "'));" . chr(10);
		} // foreach
	} //if
	// Editable areas structure
	if ($editableareas_idlist != ""){
		$editableareas_idlist = explode(',', $editableareas_idlist); // Convert string to array
		foreach($editableareas_idlist as $editablearea_id) { // Extract content from db
		   $strng = $strng . 
					"dt.editableareas_array.push(new editableareaobject('" . $editablearea_id . "', '" .
					fetchEditableContentInformation($editablearea_id) . "'));" . chr(10);
		} // foreach	
	} //if
	// Selectable images structure
	if ($selectableimages_idlist != "" && $uploadfoldername != ""){
		$selectableimages_idlist = explode(',', $selectableimages_idlist); // Convert string to array
		foreach($selectableimages_idlist as $selectableimage_id) { // Extract content from db
		   $strng = $strng . 
					"dt.selectableimages_array.push(new selectableimageobject('" . $selectableimage_id . "', '" .
					fetchSelectableImageInformation($selectableimage_id, $uploadfoldername) . "'));" . chr(10);
		} // foreach	
	} //if
	// Login flag when user authenticated
	if (getAuthenticationFlag()){
		$strng .= "loginflag = true;" . chr(10);
		if ($glbl_allowpagemanagement)
			$strng .= "pagemanagementflag = true;" . chr(10);
	}	
    return $strng;
}

function event_commitnotes($imageid, $content) {
	// Initialize data object
	global $glbl_uploadsrootfolder, $glbl_backgroundcolor_default, 
	  	   $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
	// remove unwanted portions from imageid (the imageid is a full url)
	$imageid = strstr($imageid, $glbl_uploadsrootfolder);
	 // must be logged in
	if (!getAuthenticationFlag())
		return '';
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Make query to select image information
	$query = sprintf('SELECT * FROM ' . $glbl_dbtableprefix . 'imageinformation WHERE ID = \'' . $imageid .
	                 '\' ORDER BY Updated DESC LIMIT 1');
	// Perform Query
	$result = mysql_query($query);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	$backgroundcolor = $glbl_backgroundcolor_default;
	$notes = $content;
	if (mysql_num_rows($result) == 0){ // insert into database
		$query = sprintf('INSERT INTO ' . $glbl_dbtableprefix . 'imageinformation (ID, BackgroundColor, Notes) ' .
						 'VALUES (\'' . $imageid . '\', \'' . $backgroundcolor . 
						 '\', \'' .	addslashes($notes) . '\')');
		$result = mysql_query($query); // Perform Query
	}
	else{ // update database
		$query = sprintf('UPDATE ' . $glbl_dbtableprefix . 'imageinformation SET Notes = \'' . addslashes($notes) . '\' ' .
		                 'WHERE  ID = \'' . $imageid . '\'');
		$result = mysql_query($query); // Perform Query
	}
	// Close connection
	mysql_close($link);
    return 'toolbox();';
}

function event_commitbackgroundcolor($imageid, $content) {
	// Initialize data object
	global $glbl_uploadsrootfolder, $glbl_notes_default, 
	  	   $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
	// remove unwanted portions from imageid (the imageid is a full url)
	$imageid = strstr($imageid, $glbl_uploadsrootfolder);
	 // must be logged in
	if (!getAuthenticationFlag())
		return '';
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Make query to select image information
	$query = sprintf('SELECT * FROM ' . $glbl_dbtableprefix . 'imageinformation WHERE ID = \'' . $imageid .
	                 '\' ORDER BY Updated DESC LIMIT 1');
	// Perform Query
	$result = mysql_query($query);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	$backgroundcolor = $content;
	$notes = $glbl_notes_default;
	if (mysql_num_rows($result) == 0){ // insert into database
		$query = sprintf('INSERT INTO ' . $glbl_dbtableprefix . 'imageinformation (ID, BackgroundColor, Notes) ' .
						 'VALUES (\'' . $imageid . '\', \'' . $backgroundcolor . 
						 '\', \'' .	$notes . '\')');
		$result = mysql_query($query); // Perform Query
	}
	else{ // update database
		$query = sprintf('UPDATE ' . $glbl_dbtableprefix . 'imageinformation SET BackgroundColor = \'' . addslashes($backgroundcolor) . '\' ' .
		                 'WHERE  ID = \'' . $imageid . '\'');
		$result = mysql_query($query); // Perform Query
	}
	// Close connection
	mysql_close($link);
}

function event_commitkeywords($imageid, $content, $ajaxflag) {
	// Initialize data object
	global $glbl_uploadsrootfolder, $glbl_notes_default, $glbl_backgroundcolor_default, 
	  	   $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
	// remove unwanted portions from imageid (the imageid is a full url)
	$imageid = strstr($imageid, $glbl_uploadsrootfolder);
	 // must be logged in
	if (!getAuthenticationFlag())
		return '';
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Make query to select image information
	$query = sprintf('SELECT * FROM ' . $glbl_dbtableprefix . 'imageinformation WHERE ID = \'' . $imageid .
	                 '\' ORDER BY Updated DESC LIMIT 1');
	// Perform Query
	$result = mysql_query($query);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	$notes = $glbl_notes_default;
	$backgroundcolor = $glbl_backgroundcolor_default;
	$keywords = $content;
	if (mysql_num_rows($result) == 0){ // insert into database
		$query = sprintf('INSERT INTO ' . $glbl_dbtableprefix . 'imageinformation (ID, BackgroundColor, Notes, Keywords) ' .
						 'VALUES (\'' . $imageid . '\', \'' . $backgroundcolor . 
						 '\', \'' .	$notes . '\', \'' . addslashes($keywords) . '\')');
		$result = mysql_query($query); // Perform Query
	}
	else{ // update database
		$query = sprintf('UPDATE ' . $glbl_dbtableprefix . 'imageinformation SET Keywords = \'' . addslashes($keywords) . '\' ' .
		                 'WHERE  ID = \'' . $imageid . '\'');
		$result = mysql_query($query); // Perform Query
	}
	// Close connection
	mysql_close($link);
    if ($ajaxflag)
    	return 'toolbox();';
    else
    	return;	
}

function event_commiteditablearea($contentid, $content) {
	// Initialize data object
	global $glbl_uploadsrootfolder, $glbl_notes_default, 
	  	   $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
	 // must be logged in
	if (!getAuthenticationFlag())
		return '';
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Make query to select content information
	$query = sprintf('SELECT * FROM ' . $glbl_dbtableprefix . 'editablecontent WHERE ContentID = \'' . addslashes($contentid) . '\'');
	// Perform Query
	$result = mysql_query($query);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	// replace as needed
	$content = str_replace("'", "\'", $content); // escape single quote for Javascript
	$content = str_replace("\r", "", $content); // remove carriage return
	$content = str_replace("\n", "<br>", $content); // replace line feed
	$content = str_replace('\"', '"', $content); // replace extra slash 
	$content = str_replace("\\\\", "\\", $content); // replace extra slash 
	$content = str_replace("%", "%%", $content); // escape percentage sign for MySql
	if (mysql_num_rows($result) == 0){ // insert into database
		$query = sprintf('INSERT INTO ' . $glbl_dbtableprefix . 'editablecontent (ContentID, Content) ' .
						 'VALUES (\'' . addslashes($contentid) . '\', \'' . addslashes($content) . '\')');
		$result = mysql_query($query); // Perform Query
	}
	else{ // update database
		$query = sprintf('UPDATE ' . $glbl_dbtableprefix . 'editablecontent SET Content = \'' . addslashes($content) . '\' ' .
		                 'WHERE  ContentID = \'' . addslashes($contentid) . '\'');
		$result = mysql_query($query); // Perform Query
	}
	// Close connection
	mysql_close($link);
	// User defined function hook
	return execUserDefinedFunction('USRDFNDafter_event_commiteditablearea');
}

function event_commitselectableimage($imageid, $imagepath, $foldername) {
	// Initialize data object
	global $glbl_uploadsrootfolder, $glbl_notes_default, 
	  	   $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
	 // must be logged in
	if (!getAuthenticationFlag())
		return '';
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Make query to select content information
	$query = sprintf('SELECT * FROM ' . $glbl_dbtableprefix . 'selectableimages WHERE ImageID = \'' . addslashes($imageid) . '\' AND FolderName = \'' . $foldername . '\' LIMIT 1');
	// Perform Query
	$result = mysql_query($query);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	// replace as needed
	$imageid = str_replace("'", "\'", $imageid); // escape single quote for Javascript
	$imagepath = str_replace("'", "\'", $imagepath); // escape single quote for Javascript
	$foldername = str_replace("'", "\'", $foldername); // escape single quote for Javascript	
	if (mysql_num_rows($result) == 0){ // insert into database
		$query = sprintf('INSERT INTO ' . $glbl_dbtableprefix . 'selectableimages (ImageID, ImagePath, FolderName) ' .
						 'VALUES (\'' . addslashes($imageid) . '\', \'' . addslashes($imagepath) . '\', \'' . addslashes($foldername) . '\')');
		$result = mysql_query($query); // Perform Query
	}
	else{ // update database
		$query = sprintf('UPDATE ' . $glbl_dbtableprefix . 'selectableimages SET ImagePath = \'' . addslashes($imagepath) . '\' ' .
		                 'WHERE  ImageID = \'' . addslashes($imageid) . '\' AND FolderName = \'' . $foldername . '\'');
		$result = mysql_query($query); // Perform Query
	}
	// Close connection
	mysql_close($link);
}

function event_formprocessing($formvariables) {
	// Process form data, 'fieldlist' contains the desired form fields
	global $glbl_companyname, $glbl_sendformsfromemailaddress, $glbl_sendformstoemailaddress,
	       $glbl_physicalwebrootlocation;
	$outputtype = 'simple';
	// Security validation 'ctcd'
	if (!pingToken($formvariables['citycode']))
		return '// invalid: press the browser [back] button;';
	// When using a custom template for output
	if (isset($formvariables['emailtemplate'])){
		$loc = $glbl_physicalwebrootlocation . $formvariables['emailtemplate'];
		if (file_exists($loc)){ 
			$file = fopen($loc, 'r'); // Read template page
			$formstring = fread($file, filesize($loc));
			fclose($file);
			if (trim($formstring) != '')
				$outputtype = 'custom';
		}
	}
	// When using simple output
	if ($outputtype == 'simple')
		$formstring = 'A form was submitted for: ' . $glbl_companyname . '<br><br>';
	// Loop over form variables
	foreach (explode(',', $formvariables['fieldlist']) as $formfield) {
		$formfieldvalue = '';
		if (isset($formvariables[$formfield]))
			$formfieldvalue = $formvariables[$formfield];
		if (strtolower($formfield) == 'email')
			$formfieldvalue = '<a href="mailto:' . $formfieldvalue . '">' . $formfieldvalue . '</a>';
		if ($outputtype == 'simple')
			$formstring .= str_replace('_', ' ', $formfield) . ': ' . str_replace('\\', '', $formfieldvalue) . '<br>';
		else
			$formstring = str_replace('<!-- {$' . $formfield . '$} -->', $formfieldvalue, $formstring);
	}
	// Add datetimestamp
	if ($outputtype == 'simple')	
		$formstring .= '<br>Date: ' . date("m/d/Y") . ' - ' . date("h:i:s A");
	else
		$formstring = str_replace('<!-- {$datetimestamp$} -->',
								  (date("m/d/Y") . ' - ' . date("h:i:s A")), $formstring);	
	// Send via email
	require_once("htmlMimeMail5/htmlMimeMail5.php"); // htmlMimeMail5 class
    $mail = new htmlMimeMail5(); // Instantiate a new HTML Mime Mail object
    $mail->setFrom($glbl_sendformsfromemailaddress);
    $mail->setReturnPath($glbl_sendformsfromemailaddress);  
    $mail->setSubject('web form | ' . $glbl_companyname);
    $mail->setText(str_replace('<br>', '\r\n', $formstring));
    $mail->setHTML($formstring);
	// Attach uploaded image, if any
	if (isset($_FILES['imagefile']['name']))
		if ($_FILES['imagefile']['type'] == "image/gif" || $_FILES['imagefile']['type'] == "image/jpg" ||
			$_FILES['imagefile']['type'] == "image/jpeg" || $_FILES['imagefile']['type'] == "image/pjpeg" ||
			$_FILES['imagefile']['type'] == "image/png")
			$mail->addAttachment(new fileAttachment($_FILES['imagefile']['tmp_name'], $_FILES['imagefile']['type']));
	// Send the email!
	$mail->send(array($glbl_sendformstoemailaddress), 'smtp');
	// User defined function hook
	execUserDefinedFunction('USRDFNDafter_event_formprocessing');
	// Point to page
	header('Location: ' . $formvariables['afterpage']);
}

function event_ctcd() {
	$jsstrng = '';
	// Make token	
	$uctoken = makeToken(240);
	// Javascript
	$jsstrng = 'ctcd(\'write\', \'' . $uctoken . '\');';
	return $jsstrng;
}

function event_datarequest($requestkey, $callbackfunction, $urlvariables, $formvariables) {
	// Initialize data object
	global $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
    $jsstrng = '';
	$jsaf = getAuthenticationFlag(); // Pre-store authentication flag to avoid db. error querying  'storedqueries' table
	// Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Make query to select stored query information
	$query = sprintf('SELECT * FROM ' . $glbl_dbtableprefix . 'storedqueries WHERE QueryKey = \'' . $requestkey . '\'');
	// Perform Query
	$result = mysql_query($query);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	$querystatement = '';
	$inputfields = '';
	$authenticationrequired = 1;
	while ($row = mysql_fetch_assoc($result)) {
		$querystatement = $row['QueryStatement'];
		$inputfields = str_replace(' ', '', $row['InputFields']);
		$authenticationrequired = $row['AuthenticationRequired'];
	}
	if (strlen($querystatement)) {
		if ($authenticationrequired && !$jsaf) // When authentication required
			return 'Invalid.';
		// Apply input fields
		foreach (explode(',', $inputfields) as $inputfield) { // Convert to array and loop over it
			$fieldvalue = '';
			// Extract corresponding field/value from form or url scope variables when available
			if (isset($urlvariables[$inputfield]))
				$fieldvalue = urldecode($urlvariables[$inputfield]);
			if (isset($formvariables[$inputfield]))
				$fieldvalue = urldecode($formvariables[$inputfield]);
			// Query replacement ** Case Sensitive **
			$querystatement = str_replace(('{$' . $inputfield . '$}'), $fieldvalue, $querystatement);
		} // foreach
		// * Execute stored query
		$query = $querystatement;
		$result = mysql_query($query);
		// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
		if (!$result) {
			$message  = 'Invalid query: ' . mysql_error() . "\n";
			$message .= 'Whole query: ' . $query;
			die($message);
		}
		// Serialize query into a Javascript object
		$jsstrng = serializeQuery2js($result, $callbackfunction);
	} // if
	// Close connection
	mysql_close($link);
	return $jsstrng;
}

function event_makesitemap() {
	// Initialize data object
	global $glbl_physicalwebrootlocation, $glbl_templatesfolder;
	$sitemapstring = '';
	$browsertitle = '';
	$metadescription = '';
	$metakeywords = '';
	$cnt = 0;
	if (!file_exists(($glbl_physicalwebrootlocation . 'sitemap.xml')))
		return $sitemapstring; // Sitemap xml file not found
	$doc = new DOMDocument();
	$doc->load(($glbl_physicalwebrootlocation . 'sitemap.xml')); // Load the xml site map
	$urls = $doc->getElementsByTagName('url'); // Extract <url> elements from xml file
	foreach($urls as $url) { // Loop over <url> elements in xml file
		++$cnt;
		$locs = $url->getElementsByTagName('loc');	// Extract <loc> element
		$loc = $locs->item(0)->nodeValue;
		$originalloc = $loc; // Save full page url
		if (!stristr($loc, 'sitemap')) { // Web page is not sitemap
			$handle = fopen($loc, 'r');
			$filecontent = stream_get_contents($handle);
			fclose($handle);
			// Extract browser title & meta tags to be used later (for replacements) in sitemap.html if available                  
			if ($cnt == 1){ // Extract from the first page listed on the xml
				// Extract [<title>somethinghere</title>] from web page
				extract(delimitedSubstring($filecontent, '<title>', '</title>', 0));
				$browsertitle = $substring;
				// Extract [<meta name="description" content="somethinghere" />] from web page
				extract(delimitedSubstring($filecontent, '<meta name="description"', '/>', 0));
				$metadescription = $substring;
				// Extract [<meta name="keywords" content="somethinghere" />] from web page
				extract(delimitedSubstring($filecontent, '<meta name="keywords"', '/>', 0));
				$metakeywords = $substring;
			} // if                  
		    // Extract value of: [var pagename = 'somethinghere';] from web page
			$filecontent = str_replace(' ', '', $filecontent); // Remove all blank spaces
			extract(delimitedSubstring($filecontent, 'pagename=\'', '\';', 0));
			$pagename = str_replace('\\', '', $innersubstring); // Remove single quote escape (if any)
			if ($pagename == '') // Page name not available use url
		 		$pagename = $originalloc;
			// ** Link to web page
			$sitemapstring .= '<li><a href="' . $originalloc . '" title="' . $pagename . '">' .
			                  $pagename . '</a></li>';
		} // Web page is not sitemap
	} // foreach <url>
	// Add <ul> tags
	$sitemapstring = str_replace('<li></li>', '', $sitemapstring); 
	if ($sitemapstring != '')
		$sitemapstring = '<ul>' . $sitemapstring . '</ul>';
	// Load (include) the sitemap.html template and merge with '$sitemapstring' plus browser title & meta tags replacements
	$loc = $glbl_physicalwebrootlocation . $glbl_templatesfolder . 'sitemap.html';
	if (file_exists($loc)) {
		ob_start();
		include $loc;
        $filecontent = ob_get_contents();
        ob_end_clean();
		$sitemapstring = str_ireplace('<div id="sitemap"></div>', ('<div id="sitemap">' . $sitemapstring . '</div>'), $filecontent); // Add the site map content
		if (trim($browsertitle) != '')
			$sitemapstring = str_ireplace('<title></title>', $browsertitle, $sitemapstring); // Browser title
		if (trim($metadescription) != '')
			$sitemapstring = str_ireplace('<meta name="description" content="" />', $metadescription, $sitemapstring); // Meta description
		if (trim($metakeywords) != '')
			$sitemapstring = str_ireplace('<meta name="keywords" content="" />', $metakeywords, $sitemapstring); // Meta keywords
	}
	// Site map page
 	return $sitemapstring; 
}

function event_renderdynamicpage() {
	// Dynamic pages
	global $path2root, $pageid, $sectionid, $templatepath, $glbl_websiteaddress,
	       $glbl_physicalwebrootlocation, $glbl_templatesfolder, $glbl_loginpagepath, $glbl_customphpscriptpath, $glbl_dbtableprefix;
	// Query pageregistry
	$pageregistryquery = execQuery('SELECT * FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE PageID = \'' . $pageid . '\' AND SectionID = \'' . $sectionid . '\' LIMIT 1');
	while ($row = mysql_fetch_assoc($pageregistryquery)){
		if ($row['MembersOnly'] == 1)
			$requiresloginflag = true;
		else
			$requiresloginflag = false;
		if (($row['PageStatus'] == 'active' && !$requiresloginflag) ||
			($row['PageStatus'] == 'active' && $requiresloginflag && getMemberAuthenticationFlag()) || getAuthenticationFlag()){
			$pagetitlecontentid = $row['PageTitleContentID'];
			$pagelevel = $row['PageLevel'];
			$pagepath = $row['PagePath'];
			$uploadsfolder = str_replace('/', '_', $sectionid) . $pageid;
			if ($glbl_customphpscriptpath != '')
				include($glbl_physicalwebrootlocation . $glbl_customphpscriptpath); // include the custom php script
			include($glbl_physicalwebrootlocation . $templatepath); // include the html template
			exit(0);
		} // if
	} // while
	// Unable to render a dynamic page. Either it does not exist on 'pageregistry' or the page is inactive or is a members only page and the user is not logged in
	if (!$requiresloginflag)
		header('Location: ' . $glbl_websiteaddress); // Redirect to home page
	else
		header('Location: ' . ($glbl_websiteaddress . $glbl_loginpagepath)); // Redirect to login page
	exit(0);			
}

function event_makeadminpage($retry) {
	// implement admin. page & login screen
	global $glbl_websiteaddress;
	if (getAuthenticationFlag())
		return renderPageCanvas('ADMIN',
								('<p><span class="copy">&nbsp;&nbsp;&nbsp;You are logged in. Click <a href="' . $glbl_websiteaddress . '" title="Home">here</a> for the home page.</span></p>'),
								 '', '', 'alexjet', $glbl_websiteaddress);			
	// if not already logged in, render login screen html
	if ($retry == ':(')
		$retry = '	<tr height="25">' .
				 '		<td height="25" align="center" colspan="2"><span class="copyred">&nbsp;Invalid. Try Again.&nbsp;</span></td>' .
				 '	</tr>';
	else
		$retry = '';	
	$jsstring = '<script type="text/javascript">function validateme(){' .
			'	var msg = \'\';' .
			'	if (document.forms[0][\'username\'].value == \'\')' .
			'		msg = msg + \'User Name is required\n\';' .
			'	if (document.forms[0][\'code\'].value == \'\')' .
			'		msg = msg + \'Password is required\n\';' .
			'	if (msg != \'\'){' .
			'		alert(msg);' .
			'		return false;' .
			'	}' .
			'	return true;' .
			'}</script>';
	$htmlstring = '<table border="0" cellpadding="2" cellspacing="2"><tr>' . 
				  '<td style="width:250px;" align="center" valign="top">' . 
				  '<br /><br />' . $jsstring . '<form name="loginform" id="loginform" action="alexjet.php?event=login" method="post" onsubmit="javascript:return validateme();">' .
				  '<table width="100%" border="0" cellspacing="0" cellpadding="0">' . $retry . 
				  '	<tr height="25">' .
				  '		<td height="25" align="right"><span class="copy">User Name:&nbsp;</span></td>' .
				  '		<td height="25" align="left"><input class="copy" type="text" name="username" id="username" size="10" maxlength="15" /></td>' .
				  '	</tr>' .
				  '	<tr height="25">' .
				  '		<td height="25" align="right"><span class="copy">Password:&nbsp;</span></td>' .
				  '		<td height="25" align="left"><input class="copy" type="password" name="code" id="code" size="10" maxlength="15" /></td>' .
				  '	</tr>' .
				  '	<tr height="25">' .
				  '		<td height="25" align="right">&nbsp;</td>' .
				  '		<td height="25" align="left"><br /><input class="copy" type="submit" name="submit" value=" Login > " /></td>' .
				  '	</tr>' .
				  '</table>' .
				  '</form></td></tr></table>';
	$htmlstring .= '<script type="text/javascript">document.forms[0][\'username\'].focus();</script>';
	
	return renderPageCanvas('ADMIN', $htmlstring, '', '', '', $glbl_websiteaddress);  
}

function event_login($username, $code) {
	// authentication and login functionality
	global $glbl_websiteaddress, $mysql_hostname, $mysql_databasename, $mysql_username, $mysql_password, $glbl_dbtableprefix, $const_libraryfolder;
	// First, some very general "system house-keeping"
	pingToken('filler'); // Remove any expired tokens
	// Validation
	$username = str_replace(' ', '', $username);
	$username = str_replace("'", "", $username);	
	$code = str_replace(' ', '', $code);	
	$code = str_replace("'", "", $code);
	if ($username == '' or strlen($username) > 15 or $code == '' or strlen($code) > 15){ // invalid format
		header('Location: ' . $glbl_websiteaddress . $const_libraryfolder . '/?retry=:(');
		exit(0);
	}	
    // Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// "Inject" prevention   
    $username = mysql_real_escape_string($username);
	$code = mysql_real_escape_string($code);
	// Make query to select image information
	$query = sprintf('SELECT * FROM ' . $glbl_dbtableprefix . 'users WHERE UserName = \'' . $username .
	                 '\' AND Code = \'' . $code . '\' LIMIT 1');
	// Perform Query
	$result = mysql_query($query);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	$dbUserName = '';
	$dbCode = '';
	$dbUID = '';
	while ($row = mysql_fetch_assoc($result)){ // to check case sensitiveness
		$dbUserName = $row['UserName'];
		$dbCode = $row['Code'];
		$dbUID = $row['UID'];
	}
	// Close connection
	mysql_close($link);	   
	// success ?
	if ($dbUserName == $username && $dbCode == $code && trim($dbUID) != ''){ 
		setcookie('alexjet', $dbUID, 0, '/');
		authenticateMember(); // For members only pages
		header('Location: ' . $glbl_websiteaddress . $const_libraryfolder);
		exit(0);
	}
	// invalid login information
	header('Location: ' . $glbl_websiteaddress . $const_libraryfolder . '/?retry=:(');
	exit(0);
}

function event_logout() {
	// reset cookie
	if (!getAuthenticationFlag()) // Must be logged in
		return '// Invalid.';
	setcookie('alexjet', '', 0, '/');
	memberLogout();
    return 'window.location.reload(true);'; // reload page
}

function event_memberlogout() {
	// reset cookie
	if (!getMemberAuthenticationFlag()) // Must be logged in as member
		return '// Invalid.';
	memberLogout();
    return 'window.location.reload(true);'; // reload page
}

// **** Global data ****
if (file_exists('config.php'))
	include_once('config.php');

$const_libraryfolder = 'admin';
	
// **** Other libraries ****
if ($glbl_allowpagemanagement && getAuthenticationFlag())
	include_once('alexjet_pgmgt.php');	
if (getAuthenticationFlag()){
	include_once('alexjet_upld.php');
	include_once('alexjet_pginfo.php');
}		
	
// **** Event handlers ****
if (!isset($_GET['event']))
	// return nothing for Javascript
	echo ('return;');
else{
	$event = $_GET['event'];
	switch ($event) {
		case "initializedata":
			$uploadfoldername = '';
			$editableareas_idlist = '';
			$selectableimages_idlist = '';
			$imagesortorder = '';
			if (isset($_GET['uploadfoldername']))
				$uploadfoldername = $_GET['uploadfoldername'];
			if (isset($_GET['editableareas_idlist']))
				$editableareas_idlist = $_GET['editableareas_idlist'];
			if (isset($_GET['selectableimages_idlist']))
				$selectableimages_idlist = $_GET['selectableimages_idlist'];
			if (isset($_GET['imagesortorder']))
				$imagesortorder = $_GET['imagesortorder'];
			echo (event_initializeData($uploadfoldername, $editableareas_idlist, $selectableimages_idlist, $imagesortorder));
			break;
			
		case "commitnotes":
			$imageid = ''; $content = '';
			if (isset($_GET['imageid']))
				$imageid = $_GET['imageid'];
			if (isset($_GET['content']))
				$content = urldecode($_GET['content']);
			echo (event_commitnotes($imageid, $content));
			break;
			
		case "commitbackgroundcolor":
			$imageid = ''; $content = '';
			if (isset($_GET['imageid']))
				$imageid = $_GET['imageid'];
			if (isset($_GET['content']))
				$content = urldecode($_GET['content']);
			echo (event_commitbackgroundcolor($imageid, $content));
			break;
		
		case "commitkeywords":
			$imageid = ''; $content = '';
			if (isset($_GET['imageid']))
				$imageid = $_GET['imageid'];
			if (isset($_GET['content']))
				$content = urldecode($_GET['content']);
			echo (event_commitkeywords($imageid, $content, true));
			break;

		case "commiteditablearea":
			$contentid = ''; $content = '';
			if (isset($_POST['contentid']))
				$contentid = urldecode($_POST['contentid']);
			if (isset($_POST['content']))
				$content = urldecode($_POST['content']);
			echo (event_commiteditablearea($contentid, $content));
			break;

		case "commitselectableimage":
			$imageid = ''; $imagepath = ''; $foldername = '';
			if (isset($_POST['imageid']))
				$imageid = urldecode($_POST['imageid']);
			if (isset($_POST['imagepath']))
				$imagepath = urldecode($_POST['imagepath']);
			if (isset($_POST['foldername']))
				$foldername = urldecode($_POST['foldername']);
			echo (event_commitselectableimage($imageid, $imagepath, $foldername));
			break;

		case "formprocessing":
			if (!isset($_POST['fieldlist']) || !isset($_POST['afterpage']) || !isset($_POST['citycode']))  
				echo ('// invalid: press the browser back button;');
			else
				echo (event_formprocessing($_POST));
			break;

		case "ctcd":
			echo (event_ctcd());
			break;

		case "uploadimagestool":
			$uploadfoldername = '';
			if (isset($_GET['uploadfoldername']))
				$uploadfoldername = urldecode($_GET['uploadfoldername']);
			if (isset($_POST['uploadfoldername']))
				$uploadfoldername = urldecode($_POST['uploadfoldername']);
			$parentpagepath = '';
			if (isset($_GET['parentpagepath']))
				$parentpagepath = urldecode($_GET['parentpagepath']);
			if (isset($_POST['parentpagepath']))
				$parentpagepath = urldecode($_POST['parentpagepath']);
			echo (event_uploadimagestool($uploadfoldername, $parentpagepath, ''));
			break;

		case "uploadimagestool_delete":
			$uploadfoldername = '';
			if (isset($_POST['uploadfoldername']))
				$uploadfoldername = urldecode($_POST['uploadfoldername']);
			$parentpagepath = '';
			if (isset($_POST['parentpagepath']))
				$parentpagepath = urldecode($_POST['parentpagepath']);
			$imagepath = '';
			if (isset($_POST['imagepath']))
				$imagepath = urldecode($_POST['imagepath']);
			$citycode = '';
			if (isset($_POST['citycode'])) 
				$citycode = trim($_POST['citycode']);
			if (pingToken($citycode)){
				removeFile($imagepath);
				event_commitkeywords($imagepath, '', false);
				removeToken($citycode);
			}
			echo (event_uploadimagestool($uploadfoldername, $parentpagepath, ''));
			break;

		case "uploadimagestool_rename":
			$uploadfoldername = '';
			if (isset($_POST['uploadfoldername']))
				$uploadfoldername = urldecode($_POST['uploadfoldername']);
			$parentpagepath = '';
			if (isset($_POST['parentpagepath']))
				$parentpagepath = urldecode($_POST['parentpagepath']);
			$imagepath = '';
			if (isset($_POST['imagepath']))
				$imagepath = urldecode($_POST['imagepath']);
			$imagenewname = '';
			if (isset($_POST['imagenewname']))
				$imagenewname = urldecode($_POST['imagenewname']);
			$citycode = '';
			if (isset($_POST['citycode'])) 
				$citycode = trim($_POST['citycode']);
			if (pingToken($citycode)){	
				renameFile($imagepath, $imagenewname);
				// Update image tag from old to new image name
				$imagenewname_infoarray = pathinfo($imagepath);
				$imagenewname2 = $imagenewname_infoarray['dirname'] . '/' . $imagenewname;
				$imageinfoarray = fetchImageInformation($imagepath);
		    	extract($imageinfoarray);
				event_commitkeywords($imagenewname2, $keywords, false);
				event_commitkeywords($imagepath, '', false);
				removeToken($citycode);
			}
			echo (event_uploadimagestool($uploadfoldername, $parentpagepath, $imagenewname));
			break;

		case "uploadimagestool_resize":
			$uploadfoldername = '';
			if (isset($_POST['uploadfoldername']))
				$uploadfoldername = urldecode($_POST['uploadfoldername']);
			$parentpagepath = '';
			if (isset($_POST['parentpagepath']))
				$parentpagepath = urldecode($_POST['parentpagepath']);
			$imagepath = '';
			if (isset($_POST['imagepath']))
				$imagepath = urldecode($_POST['imagepath']);
			$imagenewpath = '';
			if (isset($_POST['imagenewpath']))
				$imagenewpath = urldecode($_POST['imagenewpath']);
			$imagewidth = '';
			if (isset($_POST['imagewidth']))
				$imagewidth = urldecode($_POST['imagewidth']);
			$imageheight = '';
			if (isset($_POST['imageheight']))
				$imageheight = urldecode($_POST['imageheight']);
			$imagexcoordinate = '';
			if (isset($_POST['imagexcoordinate']))
				$imagexcoordinate = urldecode($_POST['imagexcoordinate']);
			$imageycoordinate = '';
			if (isset($_POST['imageycoordinate']))
				$imageycoordinate = urldecode($_POST['imageycoordinate']);
			$copyflag = '';
			if (isset($_POST['copyflag']))
				$copyflag = urldecode($_POST['copyflag']);
			if ($copyflag == '1')
				$copyflag = true;
			else	
				$copyflag = false;	
			$citycode = '';
			if (isset($_POST['citycode'])) 
				$citycode = trim($_POST['citycode']);
			if (pingToken($citycode)){	
				resizeImageFile($imagepath, $imagenewpath, $imagewidth, $imageheight, $imagexcoordinate, $imageycoordinate, $copyflag);
				removeToken($citycode);
			}	
			echo (event_uploadimagestool($uploadfoldername, $parentpagepath, basename($imagenewpath)));
			break;

		case "uploadimagestool_imagetags":
			$imagepath = '';
			if (isset($_POST['imagepath']))
				$imagepath = urldecode($_POST['imagepath']);
			$tagsstring = '';
			if (isset($_POST['tagsstring']))
				$tagsstring = urldecode($_POST['tagsstring']);
			$readonlyflag = '';
			if (isset($_POST['readonlyflag']))
				$readonlyflag = urldecode($_POST['readonlyflag']);
			if ($readonlyflag == '1')
				$readonlyflag = true;
			else	
				$readonlyflag = false;	
			echo (event_uploadimagestool_imagetags($imagepath, $tagsstring, $readonlyflag));
			break;

		case "datarequest":
			$requestkey = '';
			if (isset($_GET['requestkey']))
				$requestkey = urldecode($_GET['requestkey']);
			if (isset($_POST['requestkey']))
				$requestkey = urldecode($_POST['requestkey']);
			$callbackfunction = '';
			if (isset($_GET['callbackfunction']))
				$callbackfunction = urldecode($_GET['callbackfunction']);
			if (isset($_POST['callbackfunction']))
				$callbackfunction = urldecode($_POST['callbackfunction']);
			echo (event_datarequest($requestkey, $callbackfunction, $_GET, $_POST));
			break;

		case "makesitemap":
			echo (event_makesitemap());
			break;

		case "renderdynamicpage":
			echo (event_renderdynamicpage());
			break;

		case "pageinformationtool":
			$parentpagepath = '';
			if (isset($_GET['parentpagepath']))
				$parentpagepath = urldecode($_GET['parentpagepath']);
			if (isset($_POST['parentpagepath']))
				$parentpagepath = urldecode($_POST['parentpagepath']);
			echo (event_pageinformationtool($parentpagepath));
			break;

		case "pageinformationtool_post":
			$parentpagepath = '';
			if (isset($_POST['parentpagepath']))
				$parentpagepath = urldecode($_POST['parentpagepath']);
			echo (event_fn_pageinformationtool_post($parentpagepath, $_POST));
			break;

		case "pagemanagement":
			$parentpagepath = '';
			if (isset($_GET['parentpagepath']))
				$parentpagepath = urldecode($_GET['parentpagepath']);
			if (isset($_POST['parentpagepath']))
				$parentpagepath = urldecode($_POST['parentpagepath']);
			echo (event_pagemanagementtool($parentpagepath));
			break;

		case 'addpage':
			$pagekeyword = '';
			$templatepath = '';
			$sectionid = '';
			if (isset($_GET['pagekeyword']))
				$pagekeyword = strtolower(trim($_GET['pagekeyword']));
			if (isset($_GET['templatepath']))
				$templatepath = strtolower(trim($_GET['templatepath']));
			if (isset($_GET['sectionid']))
				$sectionid = strtolower(trim($_GET['sectionid']));
			echo(event_fn_addpage($pagekeyword, $templatepath, $sectionid));
			break;

		case 'togglepagestatus':
			$pagekeyword = '';
			$sectionid = '';
			$pagestatus = '';
			if (isset($_GET['pagekeyword']))
				$pagekeyword = strtolower(trim($_GET['pagekeyword']));
			if (isset($_GET['sectionid']))
				$sectionid = strtolower(trim($_GET['sectionid']));
			if (isset($_GET['pagestatus']))
				$pagestatus = strtolower(trim($_GET['pagestatus']));
			echo(event_fn_togglepagestatus($pagekeyword, $sectionid, $pagestatus));
			break;

		case 'togglepagemembersonly':
			$pagekeyword = '';
			$sectionid = '';
			$pagemembersonly = '';
			if (isset($_GET['pagekeyword']))
				$pagekeyword = strtolower(trim($_GET['pagekeyword']));
			if (isset($_GET['sectionid']))
				$sectionid = strtolower(trim($_GET['sectionid']));
			if (isset($_GET['pagemembersonly']))
				$pagemembersonly = strtolower(trim($_GET['pagemembersonly']));
			echo(event_fn_togglepagemembersonly($pagekeyword, $sectionid, $pagemembersonly));
			break;

		case 'movepagesortorder':
			$pagekeyword = '';
			$sectionid = '';
			$direction = '';
			if (isset($_GET['pagekeyword']))
				$pagekeyword = strtolower(trim($_GET['pagekeyword']));
			if (isset($_GET['sectionid']))
				$sectionid = strtolower(trim($_GET['sectionid']));
			if (isset($_GET['direction']))
				$direction = strtolower(trim($_GET['direction']));
			echo(event_fn_movepagesortorder($pagekeyword, $sectionid, $direction));
			break;

		case 'removepage':
			$pagekeyword = '';
			$sectionid = '';
			if (isset($_GET['pagekeyword']))
				$pagekeyword = strtolower(trim($_GET['pagekeyword']));
			if (isset($_GET['sectionid']))
				$sectionid = strtolower(trim($_GET['sectionid']));
			echo(event_fn_removepage($pagekeyword, $sectionid));
			break;

		case 'sectionsmgt_post2db':
			$action = '';
			if (isset($_POST['action']))
				$action = strtolower(trim($_POST['action']));
			echo(event_fn_sectionsmgt_post2db($action, $_POST));
			break;

		case 'sectionsmgt_remove':
			$sectionid = '';
			if (isset($_POST['sectionid3']))
				$sectionid = strtolower(trim($_POST['sectionid3']));
			echo(event_fn_sectionsmgt_remove($sectionid));
			break;

		case "adminpage":
			$retry = '';
			if (isset($_GET['retry']))
				$retry = trim($_GET['retry']);
			echo (event_makeadminpage($retry));
			break;

		case "login":
			$username = '';
			if (isset($_POST['username']))
				$username = trim($_POST['username']);
			$code = '';
			if (isset($_POST['code']))
				$code = trim($_POST['code']);
			echo (event_login($username, $code));
			break;

		case "logout":
			echo (event_logout());
			break;

		case "memberlogout":
			echo (event_memberlogout());
			break;

		case "void":
			break;

		default:
			echo ('return;');
	} // switch
} // if

?>