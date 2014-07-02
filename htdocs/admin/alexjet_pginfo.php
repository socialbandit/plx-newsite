<?php
// alexjet_pginfo.php | alexjet PHP page information function library | VERSION 3.6
// Copyright 2006-2010 Alexander Media, Inc. - All rights reserved.
// By: 11.02.2010_rg, 11.05.2010_rg, 11.26.2010_rg

function event_pageinformationtool($parentpagepath) {
	// Page management tool
	global $glbl_websiteaddress, $glbl_physicalwebrootlocation;
    // Must be logged in
	if (!getAuthenticationFlag())
		return '// Invalid.';
	// Format 'parentpagepath'
	$parentpagepath = trim($parentpagepath);
	if ($parentpagepath == '' || substr($parentpagepath, 0, 1) != '/' || strpos($parentpagepath, '//') !== false) // Cannot be blank, will start with: '/', etc.    
		return '// Invalid.';
	// Read meta file
	$metafilepath = $glbl_physicalwebrootlocation;
	if (strlen($parentpagepath) > 1)
		$metafilepath .= substr($parentpagepath, 1, (strlen($parentpagepath)-1));	
	if (!file_exists($metafilepath)) // Directory will exist	
		return '// Invalid.';
	// Load meta file when it exists
	$metafilepath .= 'meta.php';
	$M_browsertitle = '';
	$M_metadescription = '';
	$M_metakeywords = '';
	if (file_exists($metafilepath))
		include($metafilepath);
	// Render java script functions and data variables
	$jsstring =
			'var jBrwsrTtl = \'' . str_replace("'", "\'", $M_browsertitle) . '\'; ' .
			'var jMtDscrptn = \'' . str_replace("'", "\'", $M_metadescription) . '\'; ' .
			'var jMtKywrds = \'' . str_replace("'", "\'", $M_metakeywords) . '\'; ' .
			'function doPostPageInformation(){' .
			'	jsAjaxFORM(\'alexjet.php?event=pageinformationtool_post\' + \'&uc=\' + jsUC(), \'_pageinformationform\', true);' .
			'   return \'\'; ' .
			'}' .
			'function doUpdatePageInformationFields(){' .
			'	document.forms[0]["browsertitle"].value = jBrwsrTtl; ' . 
			'	document.forms[0]["metadescription"].value = jMtDscrptn; ' .
			'	document.forms[0]["metakeywords"].value = jMtKywrds; ' .
			'   document.forms[0]["browsertitle"].focus();' .
			'   return;' .
			'}';
	$jsstring2 = 'return doUpdatePageInformationFields();';
	// Render html - put all together
	$tempstrng = $parentpagepath;
	if ($tempstrng == '/')
		$tempstrng = 'Root folder';
	$htmlstring = '<form action="javascript:void(0);" name="_pageinformationform" id="_pageinformationform"><table border="0">' . 
				  '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="1"></td>' .
				  '       <td style="width:200px; padding-top:25px; border-bottom-width:1px; border-bottom-color:#990000; border-bottom-style:dashed;" align="right" valign="top">' . '<span class="copy">BROWSER FIELDS</span>' . '</td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed; border-bottom-width:1px; border-bottom-color:#990000; border-bottom-style:dashed;"><img src="clear.gif" border="0" width="10" height="1"></td>' .
				  '		  <td style="width:770px; height:25px; padding-left:10px; padding-top:25px; border-bottom-width:1px; border-bottom-color:#990000; border-bottom-style:dashed;" align="left" valign="top">' . '<span class="copy">Page location:&nbsp;&nbsp;&nbsp;' . $tempstrng . '</span>' . '</td>' . 
				  '   </tr>' . 
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="20"></td>' .
				  '       <td style="width:200px;"></td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed;"><img src="clear.gif" border="0" width="10" height="1"></td>' .
				  '       <td style="width:770px;"></td>' .
				  '   </tr>' .
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="1"></td>' .
				  '       <td style="width:200px;" align="right" valign="top">' . '<span class="copy">Browser Title</span>' . '</td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed;"><img src="clear.gif" border="0" width="10" height="1"></td>' .
				  '       <td style="width:770px; height:50px; padding-left:10px;" align="left" valign="top">' . '<input type="text" id="browsertitle" name="browsertitle" value="' . '' . '" maxlength="120" style="width:500px; color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;" tabindex="1" />' . '</td>' .
				  '   </tr>' .
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="1"></td>' .
				  '       <td style="width:200px;" align="right" valign="top">' . '<span class="copy">Meta Description</span>' . '</td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed;"><img src="clear.gif" border="0" width="10" height="1"></td>' .
				  '       <td style="width:770px; height:100px; padding-left:10px;" align="left" valign="top">' . '<textarea id="metadescription" name="metadescription" rows="6" style="width:500px; color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;" tabindex="2">' . '' . '</textarea>' . '</td>' .
				  '   </tr>' .
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="20"></td>' .
				  '       <td style="width:200px;"></td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed;"><img src="clear.gif" border="0" width="10" height="1"></td>' .
				  '       <td style="width:770px;"></td>' .
				  '   </tr>' .
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="1"></td>' .
				  '       <td style="width:200px;" align="right" valign="top">' . '<span class="copy">Meta Keywords</span>' . '</td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed;"><img src="clear.gif" border="0" width="10" height="1"></td>' .
				  '       <td style="width:770px; height:100px; padding-left:10px;" align="left" valign="top">' . '<textarea id="metakeywords" name="metakeywords" rows="6" style="width:500px; color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;" tabindex="3">' . '' . '</textarea>' . '</td>' .
				  '   </tr>' .
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="20"></td>' .
				  '       <td style="width:200px;"></td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed;"><img src="clear.gif" border="0" width="10" height="1"></td>' .
				  '       <td style="width:770px;"></td>' .
				  '   </tr>' .
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="20"></td>' .
				  '       <td style="width:200px;"></td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed;"><img src="clear.gif" border="0" width="10" height="1"></td>' .
				  '       <td style="width:770px; height:50px; padding-left:10px;" align="left" valign="top"><input type="button" name="PostPageInfo" value="Post Page Information" title="Post Page Information" onClick="javascript:return doPostPageInformation();" style="color:#000000; font-family:Arial; font-size:10.5px; font-weight:bold;"></td>' .
				  '   </tr>' .
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="20"></td>' .
				  '       <td style="width:200px;"></td>' .
				  '       <td style="width:10px;"></td>' .
				  '       <td style="width:770px;"></td>' .
				  '   </tr>' .
			      '</table><input type="hidden" id="parentpagepath" name="parentpagepath" value="' . $parentpagepath . '" /></form>';
	
	return renderPageCanvas('PAGE INFORMATION TOOL', $htmlstring, $jsstring, $jsstring2, 'dojo,alexjet',
	                        (parse_url($glbl_websiteaddress, PHP_URL_SCHEME) . '://' . parse_url($glbl_websiteaddress, PHP_URL_HOST) . $parentpagepath));
}

function event_fn_pageinformationtool_post($parentpagepath, $formvariables) {
	// Post page management tool data
	global $glbl_physicalwebrootlocation;
    // Must be logged in
	if (!getAuthenticationFlag())
		return '// Invalid.';
	// Format 'parentpagepath'
	$parentpagepath = trim($parentpagepath);
	if ($parentpagepath == '' || substr($parentpagepath, 0, 1) != '/' || strpos($parentpagepath, '//') !== false) // Cannot be blank, will start with: '/', etc.    
		return '// Invalid.';
	// Check form fields
	if (!isset($formvariables['browsertitle']) || !isset($formvariables['metadescription']) || !isset($formvariables['metakeywords']))
		return '// Invalid.';
	// Write meta file
	$metafilepath = $glbl_physicalwebrootlocation;
	if (strlen($parentpagepath) > 1)
		$metafilepath .= substr($parentpagepath, 1, (strlen($parentpagepath)-1));	
	if (!file_exists($metafilepath)) // Directory will exist	
		return '// Invalid.';
	$metafilepath .= 'meta.php';
	$M_browsertitle = $formvariables['browsertitle'];
	$M_metadescription = $formvariables['metadescription'];
	$M_metakeywords = $formvariables['metakeywords'];
	// Remove carriage return
	$M_browsertitle = str_replace(chr(10), " ", $M_browsertitle);
	$M_metadescription = str_replace(chr(10), " ", $M_metadescription);
	$M_metakeywords = str_replace(chr(10), " ", $M_metakeywords);
	// Escape single quote
	if (!get_magic_quotes_gpc()){
		$M_browsertitle = str_replace("'", "\'", $M_browsertitle);
		$M_metadescription = str_replace("'", "\'", $M_metadescription);
		$M_metakeywords = str_replace("'", "\'", $M_metakeywords);
	}
	// Create meta file content	
	$pageinfocontent = '<?php' . chr(10) .
					   '// Page information for folder: ' . $parentpagepath . chr(10) .
					   '// Updated: ' . date("m/d/Y") . chr(10) .
					   '$M_browsertitle = \'' . $M_browsertitle . '\';' . chr(10) .
					   '$M_metadescription = \'' . $M_metadescription . '\';' . chr(10) .
					   '$M_metakeywords = \'' . $M_metakeywords . '\';' . chr(10) .
					   '?>';
	// Write file
	$fh = fopen(($metafilepath), 'w') or die("can't open file");
	fwrite($fh, $pageinfocontent);
	fclose($fh);
	// Message for Javascript
	return ('jBrwsrTtl = \'' . $M_browsertitle . '\'; ' .
		    'jMtDscrptn = \'' . $M_metadescription . '\'; ' .
		    'jMtKywrds = \'' . $M_metakeywords . '\'; ' .
		    'doUpdatePageInformationFields(); ' .
		    'alert(\'The page information has been saved.\');');
}

?>