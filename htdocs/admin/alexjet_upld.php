<?php
// alexjet_upld.php | alexjet PHP upload tool function library | VERSION 3.6
// Copyright 2006-2010 Alexander Media, Inc. - All rights reserved.
// By: 08.14.2006_rg ... 11.16.2009_rg, 12.23.2009_rg, 09.21.2010_rg, 11.06.2010_rg, 11.21.2010_rg, 11.27.2010_rg

function replaceExtraPeriods($filename){
	$newfilename = '';
	$filenamearray = explode(".", $filename);
	if (count($filenamearray) > 2){
		$cnt = 0;	
		foreach ($filenamearray as $eachtoken){	
			$cnt++;
			if ($cnt == count($filenamearray))
				$newfilename .= '.';
			elseif ($cnt > 1)					
				$newfilename .= '_';
			$newfilename .= $eachtoken;
		} //foreach
	}
	else
		$newfilename = $filename;
	return $newfilename;	
}

function event_uploadimagestool($uploadfoldername, $parentpagepath, $showonstartupimagename) {
	// Upload images tool
	global $glbl_uploadsrootfolder, $glbl_physicalwebrootlocation, $glbl_websiteaddress, $glbl_thumbnailimages_prefix, $const_libraryfolder;
	$uc = makeUC();
    // Must be logged in
	if (!getAuthenticationFlag())
		return 'Invalid.';
	// Process file upload when needed
	$displaymessage = '';
	if (isset($_FILES['imagefile']['name'])){
		if ($_FILES['imagefile']['type'] != "image/gif" && $_FILES['imagefile']['type'] != "image/jpg" &&
			$_FILES['imagefile']['type'] != "image/jpeg" && $_FILES['imagefile']['type'] != "image/pjpeg" &&
			$_FILES['imagefile']['type'] != "image/png" && $_FILES['imagefile']['type'] != "audio/mpeg" &&
			$_FILES['imagefile']['type'] != "application/pdf"){
			$displaymessage = '&nbsp;Invalid file type. Only JPG, GIF, PNG, MPEG & PDF files are allowed.&nbsp;';
		}
		else{
			$citycode = '';
			if (isset($_POST['citycode'])) 
				$citycode = trim($_POST['citycode']);
			if (pingToken($citycode)){
				$uploadfilename = str_replace(' ', '', $_FILES['imagefile']['name']); // Automatically remove spaces in file name
				$uploadfilename = replaceExtraPeriods($uploadfilename);
				$uploadfile = $glbl_physicalwebrootlocation . $glbl_uploadsrootfolder . $uploadfoldername .
				              "/" . basename($uploadfilename);
				move_uploaded_file($_FILES['imagefile']['tmp_name'], $uploadfile);
				chmod($uploadfile, 0644);
				$displaymessage = '&nbsp;Your file (' . $uploadfilename . ') was uploaded.&nbsp;';
				$showonstartupimagename = $uploadfilename;
				removeToken($citycode);
				// User defined function hook
				execUserDefinedFunction('USRDFNDafter_event_uploadimagestool');
			} // if pingToken()			
		}
	}
	$jsrunonload = ''; // Any java script to be run on <body> load event
	// Image listing
	$filesList = '';
	$filesList_thumbs = '';
	if ($uploadfoldername != ''){  
		$filesArray = getImagesData($uploadfoldername, 'alpha', FALSE);
		foreach ($filesArray as $fileentry){
			$imagePath =  $glbl_uploadsrootfolder . $uploadfoldername . "/" . $fileentry;
			$onclickString = 'displayImage(\'' . $glbl_websiteaddress . $imagePath . '\', \'' . $imagePath . '\');';
			$imageLinkID = '_lnkd_' . str_replace('/', '_', $imagePath);
			if(substr(basename($imagePath), 0, strlen($glbl_thumbnailimages_prefix)) != $glbl_thumbnailimages_prefix)
				$filesList .= '<li><span id="' . $imageLinkID . '" class="copy"><a href="javascript:void(0);" title="View Image" onclick="javascript:' . $onclickString . '">' . $fileentry . '</a></span></li>';
			else // A thumbnail file
				$filesList_thumbs .= '<li><span id="' . $imageLinkID . '" class="copy"><a href="javascript:void(0);" title="View Image" onclick="javascript:' . $onclickString . '">' . $fileentry . '</a></span></li>';
			if(trim($showonstartupimagename) != '' && trim($showonstartupimagename) == $fileentry)
				$jsrunonload .= $onclickString;	
		} // foreach
		if (trim($filesList_thumbs) != '')
			$filesList .= $filesList_thumbs;
	}
	// Sound file listing
	$soundFilesList = '';
	if ($uploadfoldername != ''){
		$soundFilesArray = getSoundFiles($uploadfoldername);
		foreach ($soundFilesArray as $soundfileentry){
		    $soundfilePath =  $glbl_uploadsrootfolder . $uploadfoldername . "/" . $soundfileentry;
			$onclickString = 'displaySoundMovieFile(\'' . $glbl_websiteaddress . $soundfilePath . '\', \'' . $soundfilePath . '\');';
			$soundFileLinkID = '_lnkd_' . str_replace('/', '_', $soundfilePath);
		    $soundFilesList .= '<li><span id="' . $soundFileLinkID . '" class="copy"><a href="javascript:void(0);" title="Play Sound/Movie File" onclick="javascript:' . $onclickString . '">' . $soundfileentry . '</a></span></li>';
			if(trim($showonstartupimagename) != '' && trim($showonstartupimagename) == $soundfileentry)
				$jsrunonload .= $onclickString;	
		} // foreach
	}
	// PDF file listing
	$PDFFilesList = '';
	if ($uploadfoldername != ''){
		$PDFFilesArray = getPDFFiles($uploadfoldername);
		foreach ($PDFFilesArray as $PDFfileentry){
		    $PDFfilePath =  $glbl_uploadsrootfolder . $uploadfoldername . "/" . $PDFfileentry;
			$onclickString = 'displayPDFFile(\'' . $glbl_websiteaddress . $PDFfilePath . '\', \'' . $PDFfilePath . '\');';
			$PDFFileLinkID = '_lnkd_' . str_replace('/', '_', $PDFfilePath);
		    $PDFFilesList .= '<li><span id="' . $PDFFileLinkID . '" class="copy"><a href="javascript:void(0);" title="List PDF File" onclick="javascript:' . $onclickString . '">' . $PDFfileentry . '</a></span></li>';
			if(trim($showonstartupimagename) != '' && trim($showonstartupimagename) == $PDFfileentry)
				$jsrunonload .= $onclickString;	
		} // foreach
	}
	// Javascript functions
	$jsstring = 'var currimagepath = "";' .
				'function displayImage(i, p){hideToolsMenuCollapsibleAreas();document.getElementById("imageplaceholder").src = (i + \'?' . $uc . '\');switchLinkClass(currimagepath, p);currimagepath = p;' .
				'document.getElementById("imageplaceholder_name").innerHTML = \'File location:&nbsp;&nbsp;&nbsp;\' + i;' .
				'document.getElementById("imageplaceholder_delete").innerHTML = "<br /><a href=\'javascript:void(0);\' ' .
				'title=\'Delete Image\' onclick=\'javascript:deleteImage();\'>&nbsp;Delete Image&nbsp;</a>";' .
				'document.getElementById("imageplaceholder_rename").innerHTML = "<a href=\'javascript:void(0);\' ' .
				'title=\'Rename Image\' onclick=\'javascript:renameImage_showFields();\'>&nbsp;Rename Image&nbsp;</a>";' .
				'document.getElementById("imageplaceholder_embed").innerHTML = "<a href=\'javascript:void(0);\' ' .
				'title=\'Embed\' onclick=\'javascript:embed_showFields(\"imgtag\");\'>&nbsp;Embed&nbsp;</a>";' .
				'document.getElementById("imageplaceholder_resize").innerHTML = "<a href=\'javascript:void(0);\' ' .
				'title=\'Resize Image\' onclick=\'javascript:resizeImage_showFields();\'>&nbsp;Resize Image&nbsp;</a>";' .
				'document.getElementById("imageplaceholder_tags").innerHTML = "<a href=\'javascript:void(0);\' ' .
				'title=\'Update Image Tags\' onclick=\'javascript:tags_showFields();\'>&nbsp;Tag(s)&nbsp;</a>&nbsp;&gt;";}' .

	            'function displaySoundMovieFile(i, p){hideToolsMenuCollapsibleAreas();document.getElementById("imageplaceholder").src = \'clear.gif\';switchLinkClass(currimagepath, p);currimagepath = p;' .
				'document.getElementById("imageplaceholder_name").innerHTML = \'File location:&nbsp;&nbsp;&nbsp;\' + i + ' .
 				'\'<br /><br /><div align="center" style="height:30px;width:150px;padding-top:10px;border-width:1px;border-color:#990000;border-style:solid;">' .
			    '<a href="javascript:void(0);" title="Play Sound/Movie File" onclick="javascript:window.location = ' .
				chr(92) . chr(39) . '\' + i + \'' . chr(92) . chr(39) . ';">Play Sound/Movie File</a></div>\';' .
				'document.getElementById("imageplaceholder_delete").innerHTML = "<br /><a href=\'javascript:void(0);\' ' .
				'title=\'Delete Sound/Movie File\' onclick=\'javascript:deleteImage();\'>&nbsp;Delete Sound/Movie File&nbsp;</a>";' . 
				'document.getElementById("imageplaceholder_rename").innerHTML = "<a href=\'javascript:void(0);\' ' .
				'title=\'Rename Sound/Movie File\' onclick=\'javascript:renameImage_showFields();\'>&nbsp;Rename Sound/Movie Image&nbsp;</a>";' .
				'document.getElementById("imageplaceholder_embed").innerHTML = "<a href=\'javascript:void(0);\' ' .
				'title=\'Embed\' onclick=\'javascript:embed_showFields(\"atag\");\'>&nbsp;Embed&nbsp;</a>";' .
				'document.getElementById("imageplaceholder_resize").innerHTML = "";' .
				'document.getElementById("imageplaceholder_tags").innerHTML = "";}' .
			    
	            'function displayPDFFile(i, p){hideToolsMenuCollapsibleAreas();document.getElementById("imageplaceholder").src = \'clear.gif\';switchLinkClass(currimagepath, p);currimagepath = p;' .
				'document.getElementById("imageplaceholder_name").innerHTML = \'File location:&nbsp;&nbsp;&nbsp;\' + i + ' .
 				'\'<br /><br /><div align="center" style="height:30px;width:100px;padding-top:10px;border-width:1px;border-color:#990000;border-style:solid;">' .
				'<a href="javascript:void(0);" title="Open PDF File" onclick="javascript:window.location = ' .
				chr(92) . chr(39) . '\' + i + \'' . chr(92) . chr(39) . ';">Open PDF File</a>\';' .
				'document.getElementById("imageplaceholder_delete").innerHTML = "<br /><a href=\'javascript:void(0);\' ' .
				'title=\'Delete PDF File\' onclick=\'javascript:deleteImage();\'>&nbsp;Delete PDF File&nbsp;</a>";' . 
				'document.getElementById("imageplaceholder_rename").innerHTML = "<a href=\'javascript:void(0);\' ' .
				'title=\'Rename PDF File\' onclick=\'javascript:renameImage_showFields();\'>&nbsp;Rename PDF File&nbsp;</a>";' .
				'document.getElementById("imageplaceholder_embed").innerHTML = "<a href=\'javascript:void(0);\' ' .
				'title=\'Embed\' onclick=\'javascript:embed_showFields(\"atag\");\'>&nbsp;Embed&nbsp;</a>";' .
				'document.getElementById("imageplaceholder_resize").innerHTML = "";' .
				'document.getElementById("imageplaceholder_tags").innerHTML = "";}' .

				'function deleteImage(){hideToolsMenuCollapsibleAreas();jsSetClass(\'imageplaceholder_delete\', \'copyred\');var answer = confirm("Proceed to Delete?\\n\\n[" + currimagepath + "]\\n\\n");if(answer)' .
				'{document.forms[1]["imagepath"].value = currimagepath;document.deletefileform.submit();}resetToolMenuClass();}' . 

				'function renameImage_showFields(){hideToolsMenuCollapsibleAreas();jsSetClass(\'imageplaceholder_rename\', \'copyred\');var jRnmMgNm = jsGetToken(currimagepath, \'last\', \'/\', \'\');' . 
				'jsSetValue(\'_toolmenucollapsiblearea_renameimagenewname\', jRnmMgNm);' . 
				'jsSetStyleDisplay(\'_toolmenucollapsiblearea_rename\', \'block\');}' .
				
				'function renameImage(){var jRnmMgNm = jsTrim(jsGetElementValue(\'_toolmenucollapsiblearea_renameimagenewname\').replace(/ /g, \'\'));' . 
			    'if(jsIsEmpty(jRnmMgNm)){alert(\'Image Rename:\\n\\nPlease provide new image name,\\n(cannot be blank).\');return false;}' .
			    'if(jRnmMgNm.charAt(0) == \'.\'){alert(\'Image Rename:\\n\\nPlease provide new image name,\\n(file name and extension).\');return false;}' .
				'if(jRnmMgNm == jsGetToken(currimagepath, \'last\', \'/\', \'\')){alert(\'Image Rename:\\n\\nPlease provide new image name.\');return false;}' .
				'if(jsGetToken(jRnmMgNm, \'last\', \'.\', \'\') != jsGetToken(currimagepath, \'last\', \'.\', \'\')){alert(\'Image Rename:\\n\\nExtension cannot be modified,\\n(use the same extension type).\');return false;}' .
				'if(fileEntryExists(jRnmMgNm)){alert(\'Image Rename:\\n\\nAn image with this same name already exists,\\nprovide a new name.\');return false;}' .
				'var answer = confirm("Proceed to Rename?\\n\\n[" + currimagepath + "]\\n\\n" + "New image name will be:\\n[" +  jRnmMgNm + "]\\n\\n");' .
				'if(answer){document.forms[2]["imagepath"].value = currimagepath;document.forms[2]["imagenewname"].value = jRnmMgNm;document.renamefileform.submit();}}' .

				'function renameImage_hideFields(){jsSetValue(\'_toolmenucollapsiblearea_renameimagenewname\', \'\');' . 
				'jsSetStyleDisplay(\'_toolmenucollapsiblearea_rename\', \'none\');resetToolMenuClass();}' .

				'function embed_showFields(jTyp){hideToolsMenuCollapsibleAreas();jsSetClass(\'imageplaceholder_embed\', \'copyred\');makeHtmlForEmbed(jTyp, \'none\');}' .

				'function makeHtmlForEmbed(jTyp, jLgn){if(jTyp == \'imgtag\'){var jLgnStrng = \'\';' .
				'if(jLgn == \'left\'){jLgnStrng = \' style="float:left;"\';}else if(jLgn == \'right\'){jLgnStrng = \' style="float:right;"\';}' .
				'jsSetValue(\'_toolmenucollapsiblearea_emdedstring\', \'<img src="/\' + currimagepath + \'" border="0" alt=""\' + jLgnStrng + \' />\');makeHtmlForEmbed_alt();jsSetStyleDisplay(\'_toolmenucollapsiblearea_emdedstring_alignarea\', \'block\');}' .
				'else{jsSetValue(\'_toolmenucollapsiblearea_emdedstring\', \'<a href="/\' + currimagepath + \'" title="">\' + currimagepath + \'</a>\');jsSetStyleDisplay(\'_toolmenucollapsiblearea_emdedstring_alignarea\', \'none\');}' .
				'jsSetStyleDisplay(\'_toolmenucollapsiblearea_embed\', \'block\');}' .

				'function makeHtmlForEmbed_alt(){var jTgsNptStrng = \'\';' .
				'var jDt = (\'imagepath=\' + currimagepath + \'&tagsstring=\' + jTgsNptStrng + \'&readonlyflag=1\');' .
				'$.ajax({type:"POST", url:"alexjet.php?event=uploadimagestool_imagetags&uc=' . $uc . '", data:jDt, async:true, dataType:"script", ' .
				'success:function(){jsSetValue(\'_toolmenucollapsiblearea_emdedstring\', jsGetElementValue(\'_toolmenucollapsiblearea_emdedstring\').replace(/alt=""/, (\'alt="\' + jRsltStrng + \'"\')));}});}' .

				'function embed_hideFields(){jsSetValue(\'_toolmenucollapsiblearea_emdedstring\', \'\');' . 
				'jsResetEmbedStringRadio();jsSetStyleDisplay(\'_toolmenucollapsiblearea_embed\', \'none\');resetToolMenuClass();}' .

				'function jsResetEmbedStringRadio(){' .
				'var jRdBjt = document.forms[4][\'_toolmenucollapsiblearea_emdedstring_align\'];' .
				'for(i = 0; i < (jRdBjt.length-1); i++){' .
				'if(i == 0){jRdBjt[i].checked = true;}else{jRdBjt[i].checked = false;}}}' .

				'function resizeImage_showFields(){hideToolsMenuCollapsibleAreas();jsSetClass(\'imageplaceholder_resize\', \'copyred\');var jRnmMgNm = jsGetToken(currimagepath, \'last\', \'/\', \'\');' .
				'resizeImage_startImageAreaSelection();' .
				'jsSetValue(\'_toolmenucollapsiblearea_resizeimagename\', jRnmMgNm);jsSetStyleDisplay(\'_toolmenucollapsiblearea_resize\', \'block\');}' .

				'function resizeImage_hideFields(){resizeImage_endImageAreaSelection();' .
				'jsSetValue(\'_toolmenucollapsiblearea_resizeimagename\', \'\');' .
				'jsGetElement(\'_toolmenucollapsiblearea_resizeimagename_thumb_check\').checked = false;' . 
				'jsGetElement(\'_toolmenucollapsiblearea_resizeimagename_overwrite_check\').checked = false;' . 
				'resizeImage_setImageAreaSelectionFields(\'\', \'\', \'0\', \'0\', \'0\');jsSetStyleDisplay(\'_toolmenucollapsiblearea_resize\', \'none\');resetToolMenuClass();}' .

				'function resizeImage_addThumbPrefix(){var jThmbnlMgsPrfx = \'' . $glbl_thumbnailimages_prefix . '\';' .
				'var jRszMgNm = jsGetElementValue(\'_toolmenucollapsiblearea_resizeimagename\');' .
				'var jThmbMtchFlg = false;if(jRszMgNm.substring(0, jThmbnlMgsPrfx.length) == jThmbnlMgsPrfx){jThmbMtchFlg = true;}' .				
				'var jThmbChckdFlg = jsGetElement(\'_toolmenucollapsiblearea_resizeimagename_thumb_check\').checked;' .				
				'if(jThmbChckdFlg && !jThmbMtchFlg){jsSetValue(\'_toolmenucollapsiblearea_resizeimagename\', (jThmbnlMgsPrfx + jRszMgNm)); return;}' .
				'if(!jThmbChckdFlg && jThmbMtchFlg){jsSetValue(\'_toolmenucollapsiblearea_resizeimagename\', jRszMgNm.replace(jThmbnlMgsPrfx, \'\')); return;}' .
				'return;}' .

				'function resizeImage_startImageAreaSelection(){$(\'img#imageplaceholder\').imgAreaSelect({enable:true,hide:false,keys:true,' .
				'onSelectEnd:function(img, selection){resizeImage_setImageAreaSelectionFields(selection.width, selection.height, selection.x1, selection.y1, \'1\');},' . 
				'onSelectChange:function(img, selection){resizeImage_setImageAreaSelectionFields(selection.width, selection.height, selection.x1, selection.y1, \'1\');}});}' .				

				'function resizeImage_endImageAreaSelection(){$(\'img#imageplaceholder\').imgAreaSelect({disable:true, hide:true});}' .

				'function resizeImage_resetImageAreaSelection(){resizeImage_endImageAreaSelection();jsSetValue(\'_toolmenucollapsiblearea_resizeimagename_copyflag\', \'0\');resizeImage_startImageAreaSelection();}' .

				'function resizeImage_setImageAreaSelectionFields(jsWdth, jsHght, jX1, jY1, jCpyFlg){jsSetValue(\'_toolmenucollapsiblearea_resizeimagename_width\', jsWdth);' .
				'jsSetValue(\'_toolmenucollapsiblearea_resizeimagename_height\', jsHght);' .
				'jsSetValue(\'_toolmenucollapsiblearea_resizeimagename_xcoordinate\', jX1);jsSetValue(\'_toolmenucollapsiblearea_resizeimagename_ycoordinate\', jY1);' .
				'jsSetValue(\'_toolmenucollapsiblearea_resizeimagename_copyflag\', jCpyFlg);}' .

				'function resizeImage(){var jRszMgNm = jsTrim(jsGetElementValue(\'_toolmenucollapsiblearea_resizeimagename\').replace(/ /g, \'\'));' . 
			    'if(jsIsEmpty(jRszMgNm)){alert(\'Image Resize:\\n\\nPlease provide an image file name,\\n(Cannot be blank).\');return false;}' .
			    'if(jRszMgNm.charAt(0) == \'.\'){alert(\'Image Resize:\\n\\nPlease provide an image file name,\\n(File name and extension).\');return false;}' .
				'if(jsGetToken(jRszMgNm, \'last\', \'.\', \'\') != jsGetToken(currimagepath, \'last\', \'.\', \'\')){alert(\'Image Resize:\\n\\nExtension cannot be modified,\\n(Use same extension).\');return false;}' .
				'var jRszMgNmMtchFlg = false;if(jRszMgNm == jsGetToken(currimagepath, \'last\', \'/\', \'\')){jRszMgNmMtchFlg = true;}' .
				'var jRszMgNmVrwrtFlg = jsGetElement(\'_toolmenucollapsiblearea_resizeimagename_overwrite_check\').checked;' .				
				'if(jRszMgNmMtchFlg && !jRszMgNmVrwrtFlg){alert(\'Image Resize:\\n\\nIf you wish to resize the current image,\\nCheck the [overwrite] checkbox.\');return false;}' .
				'if(!jRszMgNmMtchFlg && fileEntryExists(jRszMgNm)){alert(\'Image Resize:\\n\\nThe image [\' + jRszMgNm + \'] already exists,\\nFirst delete it and try the resize operation again or\\nprovide a new file name.\\n(Overwrite is only for the current image)\');return false;}' .
				'var jWdth = jsTrim(jsGetElementValue(\'_toolmenucollapsiblearea_resizeimagename_width\'));if(!jsIsEmpty(jWdth) && !jsIsInteger(jWdth, false)){alert(\'Image Resize:\\n\\nWidth must be an integer number,\\n(In pixels).\');return false;}' .
				'var jHght = jsTrim(jsGetElementValue(\'_toolmenucollapsiblearea_resizeimagename_height\'));if(!jsIsEmpty(jHght) && !jsIsInteger(jHght, false)){alert(\'Image Resize:\\n\\nHeight must be an integer number,\\n(In pixels).\');return false;}' .
				'if(!jsIsEmpty(jWdth) && jWdth < 1){alert(\'Image Resize:\\n\\nWidth cannot be less than 1.\');return false;}' .
				'if(!jsIsEmpty(jHght) && jHght < 1){alert(\'Image Resize:\\n\\nHeight cannot be less than 1.\');return false;}' .
				'if(jsIsEmpty(jWdth) && jsIsEmpty(jHght)){alert(\'Image Resize:\\n\\nWidth or Height are required,\\n(In pixels).\');return false;}' .
				'var jMsgSzStrng = \'\';if(!jsIsEmpty(jWdth)){jMsgSzStrng = \'w:\' + jWdth;}if(!jsIsEmpty(jHght) && jMsgSzStrng != \'\'){jMsgSzStrng += \' x \';}if(!jsIsEmpty(jHght)){jMsgSzStrng += \'h:\' + jHght;}' . 
				'var jCpyFlg = jsGetElementValue(\'_toolmenucollapsiblearea_resizeimagename_copyflag\');var jCpyFlgStrng = \'\';if(jCpyFlg == \'1\'){jCpyFlgStrng = \'\\n(** From selected image area)\';}' .    
				'if(jRszMgNmMtchFlg){var answer = confirm("Proceed to Resize current image?" + jCpyFlgStrng + "\\n\\n[" + jRszMgNm + "]\\n\\nCurrent image will be Overwritten!\\n\\n" + "New size will be: " + jMsgSzStrng + "\\n\\n");}' . 
				'else{var answer = confirm("Proceed to Create New/Resized image?" + jCpyFlgStrng + "\\n\\n[" + jRszMgNm + "]\\n\\n" + "New size will be: " + jMsgSzStrng + "\\n\\n");}' .
				'if(answer){document.forms[3]["imagepath"].value = currimagepath;document.forms[3]["imagenewpath"].value = currimagepath.replace(jsGetToken(currimagepath, \'last\', \'/\', \'\'), jRszMgNm);' .
				'document.forms[3]["imagewidth"].value = jWdth;document.forms[3]["imageheight"].value = jHght;' .
				'document.forms[3]["imagexcoordinate"].value = jsGetElementValue(\'_toolmenucollapsiblearea_resizeimagename_xcoordinate\');document.forms[3]["imageycoordinate"].value = jsGetElementValue(\'_toolmenucollapsiblearea_resizeimagename_ycoordinate\');' .				
				'document.forms[3]["copyflag"].value = jCpyFlg;document.resizefileform.submit();}}' .

				'function tags_showFields(){hideToolsMenuCollapsibleAreas();jsSetClass(\'imageplaceholder_tags\', \'copyred\');' .
				'tags_post(\'1\');jsSetStyleDisplay(\'imageplaceholder_tags_inputarea\', \'inline\');}' .

				'function tags_hideFields(){jsSetValue(\'imageplaceholder_tags_input\', \'\');jsSetStyleDisplay(\'imageplaceholder_tags_inputarea\', \'none\');resetToolMenuClass();}' .

				'function tags_post(jRdNlyFlg){var jTgsNptStrng = jsGetElementValue(\'imageplaceholder_tags_input\').replace(/&/g, \'and\').replace(/\%/g, \' \');' .
				'var jDt = (\'imagepath=\' + currimagepath + \'&tagsstring=\' + jTgsNptStrng + \'&readonlyflag=\' + jRdNlyFlg);' .
				'$.ajax({type:"POST", url:"alexjet.php?event=uploadimagestool_imagetags&uc=' . $uc . '", data:jDt, async:true, dataType:"script", ' .
				'success:function(){if(jRdNlyFlg == \'0\'){tags_hideFields();}else{jsSetValue(\'imageplaceholder_tags_input\', jRsltStrng);}}});}' .

				'function hideToolsMenuCollapsibleAreas(){renameImage_hideFields();embed_hideFields();resizeImage_hideFields();tags_hideFields();}' .

				'function resetToolMenuClass(){jsSetClass(\'imageplaceholder_delete\', \'copygray\');jsSetClass(\'imageplaceholder_rename\', \'copygray\');jsSetClass(\'imageplaceholder_embed\', \'copygray\');jsSetClass(\'imageplaceholder_resize\', \'copygray\');jsSetClass(\'imageplaceholder_tags\', \'copygray\');}' .

				'function fileEntryExists(jFlNm){var jBjct = jsGetElement(\'_filelisting\');var jRfs = jBjct.getElementsByTagName(\'a\');' .
				'for(a=0; a<jRfs.length; a++){if(jRfs[a].innerHTML == jFlNm)return true;}return false;}' .

				'function switchLinkClass(jPrvsD, jnwD){if(!jsIsEmpty(jPrvsD)){jPrvsD = (\'_lnkd_\' + jPrvsD.replace(/\//g, \'_\'));jsSetClass(jPrvsD, \'copy\');}' .
				'if(!jsIsEmpty(jnwD)){jnwD = (\'_lnkd_\' + jnwD.replace(/\//g, \'_\'));jsSetClass(jnwD, \'copyselected\');}}' .

				'function validate(){ var jRrrMssg = ""; ' .
				'if(jsIsEmpty(document.forms[0]["imagefile"].value)) jRrrMssg = jRrrMssg + "Please select an image to upload\n\n";' .		
				'if(jRrrMssg != ""){alert(jRrrMssg);return false;}' . 'return true; }';

	// Make 'citycode'	
	$citycode = makeToken(43200); // 12 hours

	// Upload images form
	$formstring = '<form name="uploadfilesform" id="uploadfilesform" method="post" action="alexjet.php?event=uploadimagestool&uc=' . $uc . '" ' .
				  'enctype="multipart/form-data" onSubmit="javascript:return validate();">' .
				  '<span class="copy">Click the browse button to locate a file on your computer:</span><br />' .
				  '<input type="file" name="imagefile" size="50" />&nbsp;&nbsp;' .
				  '<input type="hidden" name="citycode" value="' . $citycode . '" />' .
				  '<input type="hidden" name="parentpagepath" value="' . $parentpagepath . '" />' . 
				  '<input type="hidden" name="uploadfoldername" value="' . $uploadfoldername . '" />' .
				  '<input type="submit" name="submit" value="Upload File" /></form>';
	// Delete images form
	$form2string = '<form name="deletefileform" id="deletefileform" method="post" action="alexjet.php?event=uploadimagestool_delete&uc=' . $uc . '">' .
				   '<input type="hidden" name="citycode" value="' . $citycode . '" />' .
				   '<input type="hidden" name="parentpagepath" value="' . $parentpagepath . '" />' . 
				   '<input type="hidden" name="uploadfoldername" value="' . $uploadfoldername . '" />' .
				   '<input type="hidden" name="imagepath" value="" /></form>';
	// Rename images form
	$form3string = '<form name="renamefileform" id="renamefileform" method="post" action="alexjet.php?event=uploadimagestool_rename&uc=' . $uc . '">' .
				   '<input type="hidden" name="citycode" value="' . $citycode . '" />' .
				   '<input type="hidden" name="parentpagepath" value="' . $parentpagepath . '" />' . 
				   '<input type="hidden" name="uploadfoldername" value="' . $uploadfoldername . '" />' .
				   '<input type="hidden" name="imagepath" value="" />' .
				   '<input type="hidden" name="imagenewname" value="" /></form>';
	// Resize images form
	$form4string = '<form name="resizefileform" id="resizefileform" method="post" action="alexjet.php?event=uploadimagestool_resize&uc=' . $uc . '">' .
				   '<input type="hidden" name="citycode" value="' . $citycode . '" />' .
				   '<input type="hidden" name="parentpagepath" value="' . $parentpagepath . '" />' . 
				   '<input type="hidden" name="uploadfoldername" value="' . $uploadfoldername . '" />' .
				   '<input type="hidden" name="imagepath" value="" />' .
				   '<input type="hidden" name="imagenewpath" value="" />' .				   
				   '<input type="hidden" name="imagexcoordinate" value="0" />' .
				   '<input type="hidden" name="imageycoordinate" value="0" />' .
				   '<input type="hidden" name="copyflag" value="0" />' .				   
				   '<input type="hidden" name="imagewidth" value="" />' .
				   '<input type="hidden" name="imageheight" value="" /></form>';
				   
	// Link to return to parent (if in a sub-folder)
	$parentfolder = '';
	if ($uploadfoldername != ''){
		$folder_array = explode('/', $uploadfoldername); // Convert string to array
		if (count($folder_array) > 1){
			$parentfolder = $glbl_websiteaddress . $const_libraryfolder . '/alexjet.php?event=uploadimagestool&uploadfoldername=' .	
						    str_replace(('/'. $folder_array[(count($folder_array)-1)]), '', $uploadfoldername) . '&parentpagepath=' . $parentpagepath;
			$parentfolder = '<br /><span class="copyselected"><a href="' . $parentfolder . '" title="Return to Parent Folder">RETURN</a>&nbsp;to Parent Folder:</span><br />' . 
						    '<span class="copy">&nbsp;&nbsp;&nbsp;/' . implode(array_splice($folder_array, 0, (count($folder_array)-1)), '/') . '/</span>';
		}
	}
	// Sub-folders (if any)			   
	$jsstring3 = '';
	if ($uploadfoldername != ''){
		$subfoldersArray = getSubFolders($uploadfoldername);
		foreach ($subfoldersArray as $subfolderentry)
		   $jsstring3 .= '<li><span class="copy"><a href="' .
		  				 $glbl_websiteaddress . $const_libraryfolder . '/alexjet.php?event=uploadimagestool&uploadfoldername=' . $uploadfoldername . '/' . $subfolderentry . '&parentpagepath=' . $parentpagepath .
				         '" title="Sub-folder">' . '/' . $uploadfoldername . '/' . $subfolderentry . '/' . '</a></span></li>'; 
	}
	$jsstring4 = '';
	if ($jsstring3 != '')
		$jsstring4 = '<br /><span class="copyselected">Sub-folders:</span>';
	// Render file upload tool
	$htmlstring = '<table border="0" cellpadding="2" cellspacing="2"><tr>' . 
				  '<td style="width:250px;padding-bottom:25px;border-right-width:1px;border-right-color:#CCCCCC;border-right-style:solid;" align="left" valign="top"><span class="copyselected">Current Folder:<br /></span><span class="copy">&nbsp;&frasl;&nbsp;' . $uploadfoldername .
				  '&nbsp;&frasl;&nbsp;(' . count($filesArray) . '&nbsp;images)</span><br />' .
				  '<ul id="_filelisting" style="list-style-type:circle; padding-left:10px; margin-left:10px;">' . $filesList . $soundFilesList . $PDFFilesList . '</ul>' . $parentfolder .
				  $jsstring4 . '<ul style="list-style-type:circle; padding-left:10px; margin-top:0px; margin-left:10px;">' . $jsstring3 . '</ul>' . '</td>' .
				  '<td align="left" valign="top" style="padding-bottom:25px;">' . '' . ' ' . $formstring . $form2string . $form3string . $form4string . '<span class="copyred">' .  $displaymessage . '</span>' .
				  '<br /><br /><div id="_toolmenu"><span id="imageplaceholder_name" class="copy"></span><br />' .
				  '<span id="imageplaceholder_delete" class="copygray"></span>' . 
				  '&nbsp;<span id="imageplaceholder_rename" class="copygray"></span>' .
				  '&nbsp;<span id="imageplaceholder_embed" class="copygray"></span>' .
				  '&nbsp;<span id="imageplaceholder_resize" class="copygray"></span>' .
				  '&nbsp;<span id="imageplaceholder_tags" class="copygray"></span>' .
				  '&nbsp;<span id="imageplaceholder_tags_inputarea" style="display:none;"><input type="text" id="imageplaceholder_tags_input" name="imageplaceholder_tags_input" value="" style="width:120px;" maxlength="50" />&nbsp;<input type="button" value="Ok" onclick="javascript:return tags_post(\'0\');" />&nbsp&nbsp<a class="copyredsmall" href="javascript:tags_hideFields();" title="Hide Tags">Hide</a></span>' .

				  '<div id="_toolmenucollapsiblearea_rename" style="display:none;padding-left:10px;padding-top:15px;padding-bottom:15px;">' .
				  '<span class="copy">Rename &gt; New file name:</span><br />' .
				  '<input type="text" name="_toolmenucollapsiblearea_renameimagenewname" id="_toolmenucollapsiblearea_renameimagenewname" style="width:250px;" />' .
				  '&nbsp;&nbsp;<input type="button" name="_toolmenucollapsiblearea_renameimagenewname_submit" id="_toolmenucollapsiblearea_renameimagenewname_submit" value="Rename Image" onclick="javascript:return renameImage();" />' .
				  '&nbsp;<input type="button" name="_toolmenucollapsiblearea_renameimagenewname_cancel" id="_toolmenucollapsiblearea_renameimagenewname_cancel" value="Cancel" onclick="javascript:renameImage_hideFields();" /></div>' .

				  '<div id="_toolmenucollapsiblearea_embed" style="display:none;padding-left:10px;padding-top:15px;padding-bottom:15px;">' .
				  '<span class="copy">Embed &gt; Copy for Paste:</span><br />' .
				  '<textarea name="_toolmenucollapsiblearea_emdedstring" id="_toolmenucollapsiblearea_emdedstring" style="width:250px;height:100px;"></textarea>&nbsp;' .
				  '<div id="_toolmenucollapsiblearea_emdedstring_alignarea" style="display:none;"><form name="embedalignradiosform" id="embedalignradiosform">' .
                  '<span class="copy">&nbsp;&nbsp;Image Align:&nbsp;&nbsp;</span>' .
				  '<input type="radio" name="_toolmenucollapsiblearea_emdedstring_align" id="_toolmenucollapsiblearea_emdedstring_align" value="none" onclick="javascript:return makeHtmlForEmbed(\'imgtag\', \'none\');" /><span class="copy">&nbsp;None&nbsp;</span>' .
				  '<input type="radio" name="_toolmenucollapsiblearea_emdedstring_align" id="_toolmenucollapsiblearea_emdedstring_align" value="left" onclick="javascript:return makeHtmlForEmbed(\'imgtag\', \'left\');" /><span class="copy">&nbsp;Left&nbsp;</span>' .
				  '<input type="radio" name="_toolmenucollapsiblearea_emdedstring_align" id="_toolmenucollapsiblearea_emdedstring_align" value="right" onclick="javascript:return makeHtmlForEmbed(\'imgtag\', \'right\');" /><span class="copy">&nbsp;Right</span>' .
				  '</form></div>' .
				  '<input type="button" name="_toolmenucollapsiblearea_emdedstring_cancel" id="_toolmenucollapsiblearea_emdedstring_cancel" value="Cancel" onclick="javascript:embed_hideFields();" /></div>' .

				  '<div id="_toolmenucollapsiblearea_resize" style="display:none;padding-left:10px;padding-top:15px;padding-bottom:15px;">' .
				  '<span class="copy">Resize &gt; File name:</span><br />' .
				  '<input type="text" name="_toolmenucollapsiblearea_resizeimagename" id="_toolmenucollapsiblearea_resizeimagename" style="width:250px;" />' .
				  '<span class="copy"><br />Thumbnail prefix&nbsp;</span><input type="checkbox" name="_toolmenucollapsiblearea_resizeimagename_thumb_check" id="_toolmenucollapsiblearea_resizeimagename_thumb_check" value="1" onclick="javascript:return resizeImage_addThumbPrefix();" />' .
				  '<span class="copy">&nbsp;&nbsp;&nbsp;Overwrite&nbsp;</span><input type="checkbox" name="_toolmenucollapsiblearea_resizeimagename_overwrite_check" id="_toolmenucollapsiblearea_resizeimagename_overwrite_check" value="1" />' .
				  '<span class="copy"><br /><br />Width:&nbsp;</span><input type="text" name="_toolmenucollapsiblearea_resizeimagename_width" id="_toolmenucollapsiblearea_resizeimagename_width" value="" style="width:80px;" maxlength="4" onchange="javascript:resizeImage_resetImageAreaSelection();" />' .
				  '<span class="copy">&nbsp;&nbsp;&nbsp;&nbsp;Height:&nbsp;</span><input type="text" name="_toolmenucollapsiblearea_resizeimagename_height" id="_toolmenucollapsiblearea_resizeimagename_height" value="" style="width:80px;" maxlength="4" onchange="javascript:resizeImage_resetImageAreaSelection();" />' .
				  '<input type="hidden" name="_toolmenucollapsiblearea_resizeimagename_xcoordinate" id="_toolmenucollapsiblearea_resizeimagename_xcoordinate" value="0" />' . 	
				  '<input type="hidden" name="_toolmenucollapsiblearea_resizeimagename_ycoordinate" id="_toolmenucollapsiblearea_resizeimagename_ycoordinate" value="0" />' . 
				  '<input type="hidden" name="_toolmenucollapsiblearea_resizeimagename_copyflag" id="_toolmenucollapsiblearea_resizeimagename_copyflag" value="0" />' .				  	
				  '<span class="copy"><br /><br /><i>Use pixel dimensions for [Width] and/or [Height].</i></span>' .
				  '<span class="copy"><br /><br /><i>Leave [Width] or [Height] blank to preserve<br />the aspect ratio of the image.</i></span>' .
				  '<br /><br /><input type="button" name="_toolmenucollapsiblearea_resizeimagename_submit" id="_toolmenucollapsiblearea_resizeimagename_submit" value="Resize" onclick="javascript:return resizeImage();" />' .
				  '&nbsp;<input type="button" name="_toolmenucollapsiblearea_resizeimagename_cancel" id="_toolmenucollapsiblearea_resizeimagename_cancel" value="Cancel" onclick="javascript:resizeImage_hideFields();" /></div>' .

				  '<br /><br /><img id="imageplaceholder" src="' . $glbl_websiteaddress . $const_libraryfolder . '/clear.gif" alt="View Image">' .
				  '</div></td>' .
				  '</tr></table>';

	return renderPageCanvas('UPLOAD IMAGES TOOL', $htmlstring, $jsstring, $jsrunonload, 'jquery,imgareaselect,alexjet',
	                        (parse_url($glbl_websiteaddress, PHP_URL_SCHEME) . '://' . parse_url($glbl_websiteaddress, PHP_URL_HOST) . $parentpagepath));
}

function event_uploadimagestool_imagetags($imagepath, $tagsstring, $readonlyflag) {
	if($readonlyflag){	
		$imageinfoarray = fetchImageInformation($imagepath);
	    extract($imageinfoarray);
		return 'var jRsltStrng = \'' . str_replace('"', '', $keywords) . '\';';
	}	
	else{
		event_commitkeywords($imagepath, trim($tagsstring), false);	
		return '// Updated tags';
	}
}

?>