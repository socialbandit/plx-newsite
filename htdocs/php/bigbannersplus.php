<?php
// Property of: Parallax Digital Studios, Inc.
// php/bigbannersplus.php
// Updated: 09.03.2011_rg, 09.07.2011_rg, 08.09.2012_rg

// Local variables
$local_phoneno = '770.874.8500';
$local_tollfreephoneno = '888.756.5447';

// Local functions

function local_fetchStaticHTMLObjects($local_staticobjectname){
	global $glbl_physicalwebrootlocation, $glbl_templatesfolder;
	$local_object = '';
	$local_objectpath = $glbl_physicalwebrootlocation . $glbl_templatesfolder . 'static_' . $local_staticobjectname . '.html';
	if (file_exists($local_objectpath)) {
		ob_start();
		include $local_objectpath;
        $local_object = ob_get_contents();
        ob_end_clean();
	}	
	return $local_object;
}

function local_makeStaticProductsLandingPageHTMLObject($contentid){ // For product detail pages to construct static html objects
	global $glbl_dbtableprefix, $glbl_physicalwebrootlocation, $glbl_templatesfolder, $glbl_websiteaddress;
	// Extract the section id
	$local_sectionid = strrchr($contentid , '/');
	$local_sectionid = str_replace($local_sectionid, '', $contentid);
	// Fetch fields for pages under section
	$local_querystrng = ('SELECT    r.SectionID, r.PageID, r.PagePath, t.Content AS LandingTitle, c.Content AS LandingContent, i.ImagePath AS ImagePath ' .
					     'FROM      ' . $glbl_dbtableprefix . 'pageregistry r ' . 
					     'LEFT JOIN ' . $glbl_dbtableprefix . 'editablecontent t ON t.ContentID = REPLACE(r.PageTitleContentID, \'_editablepagetitle\', \'_landingtitle\') ' .
				         'LEFT JOIN ' . $glbl_dbtableprefix . 'editablecontent c ON c.ContentID = REPLACE(r.PageTitleContentID, \'_editablepagetitle\', \'_landingcontent\') ' .
   						 'LEFT JOIN ' . $glbl_dbtableprefix . 'selectableimages i ON i.ImageID = REPLACE(r.PageTitleContentID, \'_editablepagetitle\', \'_pic1\') ' . 
						 'WHERE     r.SectionID = \'' . $local_sectionid . '/' . '\' AND r.LockPage = 0 AND r.PageStatus = \'active\' AND r.MembersOnly != 1 AND CHAR_LENGTH(t.Content) > 1 ' .
						 'ORDER BY  r.SortOrder ');
	$local_query = execQuery($local_querystrng);
	// Construct landing page static object
	$local_landingpagehtmlstring = '';
	$local_carouselhtmlstring = '';	
	$local_cnt = 0;
	$local_itemsperrow = 3;
	while ($row = mysql_fetch_assoc($local_query)){ // Each page found in the query
		$local_pagepath = str_replace('/index.php', '', trim($row['PagePath']));
		$local_landingtitle = trim($row['LandingTitle']);
		$local_landingcontent = trim($row['LandingContent']);		
		$local_imagepath = trim($row['ImagePath']);
		$local_htmlstring = '';
		if ($local_landingtitle != '' && $local_landingcontent != '' && $local_imagepath != ''){
			++$local_cnt;		
			$local_htmlstring = '<td class="productslanding table_space" align="left" valign="top" width="290">' .
								'<h3><a title="' . $local_landingtitle . '" href="/' . $local_pagepath . '">' . $local_landingtitle . '</a></h3><br />' .
								'<a title="' . $local_landingtitle . '" href="/' . $local_pagepath . '">' .
								'<img src="/' . $local_imagepath . '" alt="" width="210" height="122" border="0" /></a><br />' . $local_landingcontent . '</td>';
			if ($local_cnt == 1 && $local_landingpagehtmlstring == '')
				$local_landingpagehtmlstring .= '<tr>';
			else if ($local_cnt == 1)	
				$local_landingpagehtmlstring .= '</tr><tr>';
			$local_landingpagehtmlstring .= $local_htmlstring;
			if ($row['SectionID'] == 'products/')
				$local_carouselhtmlstring .= '<li>' . '<a title="' . $local_landingtitle . '" href="/' . $local_pagepath . '">' .
										     '<img src="/' . $local_imagepath . '" alt="" width="210" height="122" border="0" /></a><br /><span class="carousel_type">' . $local_landingtitle . '</span></li>';
			if ($local_cnt == $local_itemsperrow)
				$local_cnt = 0;
		}	
	} // while
	// Closing tag for last row if needed
	if ($local_cnt > 0)
		$local_landingpagehtmlstring .= str_repeat('<td>&nbsp;</td>', ($local_itemsperrow-$local_cnt)) . '</tr>';
	// Make the table tag
	if (trim($local_landingpagehtmlstring))
		$local_landingpagehtmlstring = '<table width="900" height="300" border="0" cellspacing="0" cellpadding="0">' . $local_landingpagehtmlstring . '</table>';
	// Write static object for landing page to disk	
	$local_loc = $glbl_physicalwebrootlocation . $glbl_templatesfolder . 'static_' . str_replace('/', '_', $local_sectionid) . '.html';
	$local_file = fopen($local_loc, 'w');
	fwrite($local_file, $local_landingpagehtmlstring);
	fclose($local_file);
	// Construct carousel static object
	if ($local_carouselhtmlstring != ''){
		$local_carouselhtmlstring = '<ul id="homecarousel" class="jcarousel-skin-parallax">' . $local_carouselhtmlstring . '</ul>';
		// Write static object for carousel to disk	
		$local_loc = $glbl_physicalwebrootlocation . $glbl_templatesfolder . 'static_carousel.html';
		$local_file = fopen($local_loc, 'w');
		fwrite($local_file, $local_carouselhtmlstring);
		fclose($local_file);
	}
	return;
}

// Customizable function toolkit

function lclCUSTOM_makeHtmlHeadTags(){
	global $glbl_physicalwebrootlocation, $sectionid, $pageid;
	// Read meta file (contains page information)
    $M_browsertitle = '';
	$M_metadescription = '';
    $M_metakeywords = ''; 
	$local_lookinhomepageflag = false;
	if ($sectionid == 'root/' && $pageid == 'home') // Home page
		$local_lookinhomepageflag = true;
	else{
		$local_metafilepath = $glbl_physicalwebrootlocation . str_replace('root/', '', $sectionid) . $pageid . '/meta.php';
		if (file_exists($local_metafilepath))
			include($local_metafilepath);
		else	
			$local_lookinhomepageflag = true;
	}
	if ($local_lookinhomepageflag || (trim($M_browsertitle) == '' && trim($M_metadescription) == '' && trim($M_metakeywords) == '')){
		$local_metafilepath = $glbl_physicalwebrootlocation . 'meta.php';
		if (file_exists($local_metafilepath))
			include($local_metafilepath);
	}		
	// Construct html
    $local_browsertitle = trim($M_browsertitle);
   	$local_metadescription = str_replace('"', '', trim($M_metadescription));
	$local_metakeywords = str_replace('"', '', trim($M_metakeywords));
	return(
		   '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . chr(10) .
		   str_repeat(chr(9), 2) . '<title>' . $local_browsertitle . '</title>' . chr(10) .
		   str_repeat(chr(9), 2) . '<meta name="description" content="' . $local_metadescription . '" />' . chr(10) .
		   str_repeat(chr(9), 2) . '<meta name="keywords" content="' . $local_metakeywords . '" />' . chr(10)
		  );
}

function lclCUSTOM_fetchC($local_contentid){
	$local_content = '';
	if(trim($local_contentid) != ''){
		$local_content = fetchEditableContentInformation(fn_makeid($local_contentid));
		$local_content = str_replace('\\', '', $local_content); 	
	}
	return $local_content;
}

function lclCUSTOM_fetchC2($local_contentid){
	$local_content = '';
	if(trim($local_contentid) != ''){
		$local_content = fetchEditableContentInformation($local_contentid);
		$local_content = str_replace('\\', '', $local_content); 	
	}
	return $local_content;
}

function lclCUSTOM_transform2UL($local_contentstring, $local_separator_token){
	$local_ullist = '';
	$local_contentstring_array = explode($local_separator_token, $local_contentstring);
	foreach($local_contentstring_array as $local_contentstring_each)
		if (trim($local_contentstring_each) != '')
			$local_ullist .= '<li>' . $local_contentstring_each . '</li>';	
	if ($local_ullist != '') 	
		return '<ul>' . $local_ullist . '</ul>';
	else
		return '';	 
}

function lclCUSTOM_makeCopyright(){
	global $glbl_companyname, $glbl_createdyear;
	$local_currentyear = date("Y");
	$local_yearrange = $glbl_createdyear;
	if ($local_yearrange != $local_currentyear)
		$local_yearrange = $glbl_createdyear . '-' . $local_currentyear;
	return('Copyright &copy; ' . $local_yearrange . ' ' . $glbl_companyname . ' - All rights reserved.');
	
}

function lclCUSTOM_makeStaticSitemapXmlFile($local_sitemapflag){ // Query currently works 'well' with 2 page levels
	global $glbl_physicalwebrootlocation, $glbl_dbtableprefix, $glbl_websiteaddress;
	$localCUSTOM_sectionfilterout = ''; // Filter out sections, leave blank if none
	$localCUSTOM_pagefilterout = 'sendfiles-thankyou'; // Filter out specific pages
	if (!getAuthenticationFlag())
		return;
	// Construct static sitemap.xml for Google SEO
	$local_querystrng = ('SELECT    r.PagePath, r.SectionID, r.PageID ' .
					     'FROM      ' . $glbl_dbtableprefix . 'pageregistry r ' . 
					     'WHERE     r.LockPage = 0 AND r.PageStatus = \'active\' AND r.MembersOnly != 1 ' .
					     'ORDER BY  CONCAT(IF(r.PageLevel = 1, \'\', (SELECT SortOrder FROM ' . $glbl_dbtableprefix . 'pageregistry' . ' WHERE PageID = REPLACE(r.SectionID, \'/\', \'\') AND SectionID = \'root/\')), r.SortOrder) ');
	$local_query = execQuery($local_querystrng);
	$local_outputstrng = '';
	while ($row = mysql_fetch_assoc($local_query)){ // Each page found in the query
		if (strpos(($localCUSTOM_sectionfilterout . ','), ($row['SectionID'] . ',')) === false &&
			strpos(($localCUSTOM_pagefilterout . ','), ($row['PageID'] . ',')) === false){ // Filter out sections & specific pages
			$local_pagepath = $glbl_websiteaddress . str_replace('/index.php', '', $row['PagePath']);
			$local_outputstrng .= '<url>' . chr(10) .
								  chr(9) . '<loc>' . $local_pagepath . '/</loc>' . chr(10) .
								  chr(9) . '<changefreq>daily</changefreq>' . chr(10) .
								  chr(9) . '<priority>0.80</priority>' . chr(10) .
							      '</url>' . chr(10);
		} // if
	} // while
	// Reference to sitemap page
	$local_sitemapstring = '';
	if ($local_sitemapflag)
		$local_sitemapstring = '<url>' . chr(10) .
					   		   chr(9) . '<loc>' . $glbl_websiteaddress . 'sitemap/</loc>' . chr(10) .
					   		   chr(9) . '<changefreq>daily</changefreq>' . chr(10) .
					   		   chr(9) . '<priority>0.80</priority>' . chr(10) .
					   		   '</url>' . chr(10);
	// Construct xml file
	$local_xmlstring = '<?xml version="1.0" encoding="UTF-8" ' . chr(63) . '>' . chr(10) .
					   '<urlset' . chr(10) .
					   chr(9) . 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . chr(10) .
				       chr(9) . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . chr(10) .
				       chr(9) . 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . chr(10) .
				       chr(9) . chr(9) . 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . chr(10) . chr(10) .
					   '<url>' . chr(10) .
					   chr(9) . '<loc>' . $glbl_websiteaddress . '</loc>' . chr(10) .
					   chr(9) . '<changefreq>daily</changefreq>' . chr(10) .
					   chr(9) . '<priority>1.00</priority>' . chr(10) .
					   '</url>' . chr(10) .
				       $local_outputstrng .
				       $local_sitemapstring .
				       '</urlset>' ;
	// Write file to disk	
	$local_loc = $glbl_physicalwebrootlocation . 'sitemap.xml';
	$local_file = fopen($local_loc, 'w'); // Read content for callout box
	fwrite($local_file, $local_xmlstring);
	fclose($local_file);
	return;
}

function lclCUSTOM_fetchImagesIntoDivElement($local_uploadfoldername, $local_imagesortorder, $local_divelementid, $local_divclass,
										     $local_imgwidth, $local_imgheight, $local_imageborderwidth, $local_imagebordercolor, $local_imageborderstyle,
										     $local_imgnameprefix, $local_imgattribute, $local_imgattribute_imgnameprefix,
										     $local_imgtagbefore, $local_imgtagafter, $local_maximages, $local_animationJSstring){
	global $glbl_uploadsrootfolder, $glbl_dbtableprefix;
	$local_htmlstring = '';
	$local_excludethumbsflag = true; // Boolean
	// Fetch list of images from disk * $local_imagesortorder (alpha | random | alphadesc)
	$local_filesArray = getImagesData($local_uploadfoldername, $local_imagesortorder, $local_excludethumbsflag);
	// Fetch all image keywords for $local_uploadfoldername (if any)
	$local_keywordsquerystrng = ('SELECT    i.ID, i.Keywords ' .
					             'FROM      ' . $glbl_dbtableprefix . 'imageinformation i ' . 
						         'WHERE     i.ID LIKE \'' . $glbl_uploadsrootfolder . $local_uploadfoldername . '/%\' AND i.ID NOT LIKE \'%thumb_%\' ' .
						         'ORDER BY  i.ID ');
	$local_keywordsquery = execQuery($local_keywordsquerystrng);
	$local_keywordsarray = array();
	while ($row = mysql_fetch_assoc($local_keywordsquery)){ // Each image/keywords found in the query
		$local_keywordsarrayindex = basename($row['ID']);;
		$local_keywordsarrayindex = str_replace('.', '~', $local_keywordsarrayindex);
		$local_keywordsvalue = $row['Keywords'];
		$local_keywordsvalue = str_replace('\\', '', $local_keywordsvalue); // Remove escape character from single quotes (in keywords)
		$local_keywordsvalue = str_replace('"', '', $local_keywordsvalue); // Remove double quotes  (in keywords)		
		$local_keywordsarray[$local_keywordsarrayindex] = $local_keywordsvalue; // Store keywords in array 
	} // while
	// Loop over images
	$local_cnt = 0;
	foreach ($local_filesArray as $local_fileentry){
		++$local_cnt;
		$local_imgkeywords = '';
		if (isset($local_keywordsarray[str_replace('.', '~', $local_fileentry)]))
			$local_imgkeywords = $local_keywordsarray[str_replace('.', '~', $local_fileentry)];
		$local_imgtagstring = '';
		$local_imgtagstylestring = '';
		$local_imgtagbefore_copy = $local_imgtagbefore;
		if (strpos($local_imgtagbefore_copy, 'href=""') !== false) // Found
			$local_imgtagbefore_copy = str_replace('href=""', ('href="/' . $glbl_uploadsrootfolder . $local_uploadfoldername . '/'.  $local_fileentry . '"'), $local_imgtagbefore_copy);
		if (strpos($local_imgtagbefore_copy, 'title=""') !== false) // Found
			$local_imgtagbefore_copy = str_replace('title=""', ('title="' . $local_imgkeywords . '"'), $local_imgtagbefore_copy);
		$local_imgtagstring = $local_imgtagbefore_copy . 
     						  '<img border="0" src="/' . $glbl_uploadsrootfolder . $local_uploadfoldername . '/'.  $local_imgnameprefix . $local_fileentry . '" alt="' . $local_imgkeywords . '" ';
		if (trim($local_imgwidth) != ''){
			$local_imgtagstring .= 'width="' . $local_imgwidth . '" ';	
			$local_imgtagstylestring .= 'width:' . $local_imgwidth . 'px;';	
		} 
		if (trim($local_imgheight) != ''){
			$local_imgtagstring .= 'height="' . $local_imgheight . '" ';	
			$local_imgtagstylestring .= 'height:' . $local_imgheight . 'px;';	
		} 
		if (trim($local_imageborderwidth) != '' && trim($local_imagebordercolor) != '' && trim($local_imageborderstyle) != '')
			$local_imgtagstylestring .= 'border:' . $local_imageborderstyle . ' ' . $local_imageborderwidth . ' ' . $local_imagebordercolor . ';';
		if ($local_imgtagstylestring != ''){
			$local_imgtagstylestring .= 'left:0;top:0;';
			$local_imgtagstring .= 'style="' . $local_imgtagstylestring . '" ';
		}	
		if ($local_imgattribute != '')
			$local_imgtagstring .= $local_imgattribute . '="/' . $glbl_uploadsrootfolder . $local_uploadfoldername . '/'.  $local_imgattribute_imgnameprefix . $local_fileentry . '" ';
		$local_imgtagstring .= '/>' . $local_imgtagafter;
		if ($local_maximages == 0 || ($local_maximages > 0 && $local_cnt <= $local_maximages)) // When $local_maximages = 0, all images are used
			$local_htmlstring .= $local_imgtagstring;
	} // foreach
	if (trim($local_htmlstring) != ''){ // Div tag
		$local_divtagstylestring = '';
		$local_divtagstring = '';
		if (trim($local_imgwidth) != '')
			$local_divtagstylestring .= 'width:' . $local_imgwidth . 'px;';	
		if (trim($local_imgheight) != '')
			$local_divtagstylestring .= 'height:' . $local_imgheight . 'px;';	
		if ($local_divtagstylestring != '')
			$local_divtagstylestring .= 'margin:0;overflow:hidden;padding:0;';
		$local_divtagstring = '<div';
		if (trim($local_divelementid) != '')	
			$local_divtagstring .= ' id="' . $local_divelementid . '"';
		if (trim($local_divclass) != '')	
			$local_divtagstring .= ' class="' . $local_divclass . '"';
		if ($local_divtagstylestring != '')
			$local_divtagstring .= ' style="' . $local_divtagstylestring . '"';					
		$local_divtagstring .= '>';
		$local_htmlstring = $local_divtagstring . $local_htmlstring . '</div>';
		// Animate as needed
		if (trim($local_animationJSstring) != '') 
			$local_htmlstring .= '<script type="text/javascript">' . $local_animationJSstring . '</script>';
	 } // Div tag
	return $local_htmlstring;	
}

function lclCUSTOM_fetchSectionPagesIntoListElement($local_sectionid, $local_listelementid){
	global $glbl_dbtableprefix, $glbl_websiteaddress;
	$local_htmlstring = '';
	// Fetch list of pages under $local_sectionid and construct a list (<ul>)
	$local_querystrng = ('SELECT    r.PagePath, e.Content AS PageTitle ' .
					     'FROM      ' . $glbl_dbtableprefix . 'pageregistry r ' . 
				         'LEFT JOIN ' . $glbl_dbtableprefix . 'editablecontent e ON e.ContentID = r.PageTitleContentID ' .
						 'WHERE     r.SectionID = \'' . $local_sectionid . '\' AND r.LockPage = 0 AND r.PageStatus = \'active\' AND r.MembersOnly != 1 ' .
						 'ORDER BY  r.SortOrder ');
	$local_query = execQuery($local_querystrng);
	$local_outputstrng = '';
	while ($row = mysql_fetch_assoc($local_query)){ // Each page found in the query
		$local_pagepath = $glbl_websiteaddress . str_replace('/index.php', '', $row['PagePath']);
		$local_pagetitle = str_replace('\\', '', $row['PageTitle']);
		$local_outputstrng .= '<li><a href="' . $local_pagepath . '" title="' . $local_pagetitle . '">' . $local_pagetitle . '</a></li>';
	} // while
	if ($local_outputstrng != ''){
		if (trim($local_listelementid) != '')
			$local_htmlstring = '<ul id="' . $local_listelementid . '">' . $local_outputstrng . '</ul>';
		else
			$local_htmlstring = '<ul>' . $local_outputstrng . '</ul>';
	}	
	return $local_htmlstring;
}

function lclCUSTOM_getTopSectionPage($local_sectionid, $local_defaultpath){
	global $glbl_dbtableprefix, $glbl_websiteaddress;
	// Get top page under $local_sectionid
	$local_querystrng = ('SELECT    r.PagePath ' .
					     'FROM      ' . $glbl_dbtableprefix . 'pageregistry r ' . 
						 'WHERE     r.SectionID = \'' . $local_sectionid . '\' AND r.LockPage = 0 AND r.PageStatus = \'active\' AND r.MembersOnly != 1 ' .
						 'ORDER BY  r.SortOrder LIMIT 1 ');
	$local_query = execQuery($local_querystrng);
	$local_pagepath = '';
	while ($row = mysql_fetch_assoc($local_query)){ // ONLY one page is returned
		$local_pagepath = $glbl_websiteaddress . str_replace('/index.php', '', $row['PagePath']);
	} // while
	if ($local_pagepath == '')
		$local_pagepath = $local_defaultpath;
	return $local_pagepath;
}

// CUSTOM events

function USRDFNDafter_event_commiteditablearea(){
	global $contentid;
	if (getAuthenticationFlag()){
		if (strpos($contentid, 'root/home_headline') !== false) // Reload page when the 'home page headline' is modified
			return 'window.location.reload(true);';
		else if (strpos($contentid, '_landingtitle') !== false || strpos($contentid, '_landingcontent') !== false) // When landing pages content is modified 
			return local_makeStaticProductsLandingPageHTMLObject($contentid);
 	}
	return;	
}

function USRDFNDafter_event_addpage(){
	global $sectionid;
	lclCUSTOM_makeStaticSitemapXmlFile(true);
	return;
}

function USRDFNDafter_event_togglepagestatus(){
	global $sectionid;
	lclCUSTOM_makeStaticSitemapXmlFile(true);
	return;
}

function USRDFNDafter_event_togglepagemembersonly(){
	global $sectionid;
	lclCUSTOM_makeStaticSitemapXmlFile(true);
	return;
}

function USRDFNDafter_event_movepagesortorder(){
	global $sectionid;
	lclCUSTOM_makeStaticSitemapXmlFile(true);
	return;
}

function USRDFNDafter_event_removepage(){
	global $sectionid;
	lclCUSTOM_makeStaticSitemapXmlFile(true);
	return;
}

//function USRDFNDafter_event_formprocessing(){
//	if (isset($_POST['xyz']))
//	return;
//}

//function USRDFNDafter_event_uploadimagestool(){
//	global $uploadfoldername, $parentpagepath;
//	return;
//}
	
?>