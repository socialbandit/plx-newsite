<?php
// alexjet_pgmgt.php | alexjet PHP page management function library | VERSION 3.6
// Copyright 2006-2010 Alexander Media, Inc. - All rights reserved.
// By: 11.06.2009_rg, 12.01.2009_rg, 02.15.2010_rg, 09.02.2010_rg, 09.18.2010_rg, 11.23.2010_rg

function event_pagemanagementtool($parentpagepath) {
	// Page management tool
	global $glbl_websiteaddress, $glbl_loginpagepath, $glbl_allowsectionmanagement, $glbl_dbtableprefix;
    // Must be logged in
	if (!getAuthenticationFlag())
		return '// Invalid.';
	// Queries
	$sectionsquery = execQuery('SELECT * FROM ' . $glbl_dbtableprefix . 'pagesections WHERE ShowInPageManager = 1 ORDER BY SortOrder');
	$templatesquery = execQuery('SELECT * FROM ' . $glbl_dbtableprefix . 'pagetemplates WHERE ShowInPageManager = 1 ORDER BY SortOrder');
	// Render java script functions and data variables
	$jsstringdata = 'var sectionsDtObjct = new Object();';
	$jscnt = 0;
	$jssctnsrtrdr = '-1'; // Store the last/highest section sort order value
	while ($row = mysql_fetch_assoc($sectionsquery)){
		$jscnt++;
		$jsstringdata .= 'var __rs' . $jscnt . ' = new Object();' . 
					     '__rs' . $jscnt . '.sectionid = \'' . $row['SectionID'] . '\';' . 
						 '__rs' . $jscnt . '.description = \'' . $row['Description'] . '\';' .
						 '__rs' . $jscnt . '.defaulttemplatepath = \'' . $row['DefaultTemplatePath'] . '\';' .
						 '__rs' . $jscnt . '.sortorder = \'' . $row['SortOrder'] . '\';' .
						 '__rs' . $jscnt . '.indent = \'' . str_repeat('../', (substr_count($row['SectionID'], '/')-1)) . '\';' .
						 'sectionsDtObjct.rs' . $jscnt . ' = __rs' . $jscnt . ';';
		$jssctnsrtrdr = $row['SortOrder'];						 
	} // while
	if (is_int((int)$jssctnsrtrdr))
		$jssctnsrtrdr++;
	if (mysql_num_rows($sectionsquery) > 0)
		mysql_data_seek($sectionsquery, 0); // reset pointer
	$disablemembersonlychkbx = '';
	if (trim($glbl_loginpagepath) == '')
		$disablemembersonlychkbx = 'disabled';
	$jsstring =
	        'var jglbl_sectionID = \'\';' .
	        'var jglbl_membersList = \'\';' .
			$jsstringdata .
			'function fetchSectionDefaultTemplatePath(jSctn){' .
			'	if(jsObjectRecordCount(sectionsDtObjct) > 0){' .
			'		for (var i in sectionsDtObjct)' .
			'           if(sectionsDtObjct[i].sectionid == jSctn) ' .
			'              return sectionsDtObjct[i].defaulttemplatepath; ' .
			'	}' .
			'   return \'\'; ' .
			'}' .
			'function doAddPage(){' .
			'	var jTmpltPth = jsGetElementValue(\'TemplatePath\');' .
			'	if (jsTrim(jTmpltPth) == \'\'){' .
			'		alert(\'Please select a template.\');' .
			'		return false;' .
			'	}' .
			'	var jSctn = jsGetElementValue(\'SectionID\');' .
			'	if (jsTrim(jSctn) == \'\'){' .
			'		alert(\'Please select a section.\');' .
			'		return false;' .
			'	}' .
			'	var jPgKywrd = jsGetElementValue(\'PageKeyword\');' .
			'   jPgKywrd = jPgKywrd.toLowerCase();' .
			'	jPgKywrd = jPgKywrd.replace(/ /g, \'\');' .
			'	jPgKywrd = jPgKywrd.replace(/[^a-zA-Z 0-9 _-]+/g, \'\');' .
			'	if (jPgKywrd.length > 35){' .
			'		alert(\'Keyword is too long. 35 characters max.\');' .
			'		return false;' .
			'	}' .
			'	if (jsTrim(jPgKywrd) != \'\'){' .
			'		var jNswr = confirm(\'Please confirm you want to create a new page under: \' +  jSctn  + ' .
			'							\'\n\nThe page Keyword will be: \' + jPgKywrd);' .
			'		if(jNswr){' .
			'		    jsAjaxURL(\'alexjet.php?event=addpage&pagekeyword=\' + jPgKywrd + \'&templatepath=\' + jTmpltPth + \'&sectionid=\' + jSctn + \'&uc=\' + jsUC(), false);' .
			'			return true;' .
			'		}' .
			'	}' .
			'	else' .
			'		alert(\'Page Keyword is required. (35 characters max.)!\');' .
			'	return false;' .
			'}' .
			'function doTogglePageStatus(jChkbxObj, jPgKywrd, jSctn){' .
			'   if(jChkbxObj.checked) jPgStts = \'active\'; else jPgStts = \'inactive\'; ' .
			'	jsAjaxURL(\'alexjet.php?event=togglepagestatus&pagekeyword=\' + jPgKywrd + \'&sectionid=\' + jSctn + \'&pagestatus=\' + jPgStts + \'&uc=\' + jsUC(), false);' .
			'	return; ' .				
			'}' .
			'function doTogglePageMembersOnly(jChkbxObj, jPgKywrd, jSctn){' .
			'   if(jChkbxObj.checked) jPgMmbrsNly = \'1\'; else jPgMmbrsNly = \'0\'; ' .
			'	jsAjaxURL(\'alexjet.php?event=togglepagemembersonly&pagekeyword=\' + jPgKywrd + \'&sectionid=\' + jSctn + \'&pagemembersonly=\' + jPgMmbrsNly + \'&uc=\' + jsUC(), false);' .
			'	return; ' .				
			'}' .
			'function doMovePageSortOrder(jPgKywrd, jSctn, jDrctn){' .
			'	jsAjaxURL(\'alexjet.php?event=movepagesortorder&pagekeyword=\' + jPgKywrd + \'&sectionid=\' + jSctn + \'&direction=\' + jDrctn + \'&uc=\' + jsUC(), false);' .
			'	return; ' .				
			'}' .
			'function doRemovePage(jPgKywrd, jSctn){' .
			'	var jNswr = confirm(\'Please confirm you want to REMOVE the page:\n\n [\' + jPgKywrd + \'] under [\' + jSctn + \'] \n\nNote: All data will be lost!\'); ' .
			'   if(jNswr){ ' .
			'		jsAjaxURL(\'alexjet.php?event=removepage&pagekeyword=\' + jPgKywrd + \'&sectionid=\' + jSctn + \'&uc=\' + jsUC(), false);' .
		    '   	return; ' .
			'	}' .
			'	return false; ' .	
			'}' .
			'function membersList(){' .
			'	jsDataRequestURL(\'editablearea\', \'membersList_doit\', \'contentid=ADMIN_memberslist\');' .
			'	return;' .
			'}' .
			'function membersList_doit(jRsltBjct){' .
			'	if(jsObjectRecordCount(jRsltBjct) > 0){' .
			'	  document.forms[0][\'content\'].value = jRsltBjct.rs1.content.replace(/<br>/gi, \'\\n\');' .
			'     jglbl_membersList = document.forms[0][\'content\'].value;' .
			'   }' .
			'	return;' .
			'}' .
			'function doSaveMemberList(){' .
			'   document.forms[0][\'content\'].value = document.forms[0][\'content\'].value.replace(/ /gi, \'\');' .
			'	jsAjaxFORM(\'alexjet.php?event=commiteditablearea\' + \'&uc=\' + jsUC(), \'_memberlistingform\', true);' .
			'   membersList();' .
			'	return;' .
			'}' .
			'function membersList_cancel(){' .
		    ' 	if(document.forms[0][\'content\'].value != jglbl_membersList){' .
			'		var jNswr = confirm(\'Any changes will be lost. Please confirm you want to reload the member list.\n\n\');' .
			' 		if(jNswr)' .
			'			membersList();' .
			'   }' .
			'	return;' .
			'}';
	if ($glbl_allowsectionmanagement)
		$jsstring .=
			'function sectionsMgt_toggleLink(){' .
			'	sectionsMgt_resetFormFields();' .
			'   jsSetClass(\'_sectionMgtEditLink\', \'copygray\');' .
			'   jsSetClass(\'_sectionMgtAddLink\', \'copygray\');' .
			'   jsCollapseToggle(\'_sectionMgtCanvas\');' .
			'   if(jsGetElementClass(\'_sectionMgtLink\') == \'copy\') jsSetClass(\'_sectionMgtLink\', \'copyselected\'); else jsSetClass(\'_sectionMgtLink\', \'copy\');' .
			'   if(jsGetElementInnerHTML(\'_sectionMgtLinkSymbol\') == \'(+)\') jsSet(\'_sectionMgtLinkSymbol\', \'(-)\'); else jsSet(\'_sectionMgtLinkSymbol\', \'(+)\');' .
			'	return;' .
			'}' .
			'function sectionsMgt_resetFormFields(){' .
			'   jsAdd2Select(\'sectionid2\', \'\', \'\', true);' .
			'   jsSetValue(\'sectiondescription\', \'\');' .
			'	if (document.all) ' .
			'   	jsSetSelectOption(document.all[\'sectiondefaulttemplate\'], \'\'); ' .
			' 	else ' .
			'   	jsSetSelectOption(document.getElementById(\'sectiondefaulttemplate\'), \'\'); ' .
			'   jsSetValue(\'sectionsortorder\', \'\');' .
			'   jsSetValue(\'sectionsendbutton\', \'\');' .
			'   jsSetValue(\'sectionid3\', \'\');' .
			'   jsSetValue(\'action\', \'\');' .
			'	jsSetStyleDisplay(\'_sectionMgtRemoveLink\', \'none\');' .
			'	jsSetStyleDisplay(\'_sectionMgtSendButton\', \'none\');' .
			'   jsSetValue(\'_WorkArea\', \'\');' .
			'   jsSetDisabled(\'sectionid2\', true);' .
			'   jsSetDisabled(\'sectiondescription\', true);' .
			'   jsSetDisabled(\'sectiondefaulttemplate\', true);' .
			'   jsSetDisabled(\'sectionsortorder\', true);' .
			'   jsSetDisabled(\'sectionsendbutton\', true);' .
			'}' .
			'function sectionsMgt_populateSectionIDFieldOnAdd(jRsltBjct){' .
			'   jsAdd2Select(\'sectionid2\', \'Choose a Page to Create a Section\', \'\', true);' .
			'   var jTmpStrng = \'\';' .
			'	if(jsObjectRecordCount(jRsltBjct) > 0){' .
			'		for (var i in jRsltBjct){' .
			'          jsAdd2Select(\'sectionid2\', (jRsltBjct[i].sectionid + \'  -  (\' + jRsltBjct[i].description + \')\'), jRsltBjct[i].sectionid, false);' .
			'          jTmpStrng += (jRsltBjct[i].sectionid + \'~\' + jRsltBjct[i].description + \'~n/a~\' + \'-1|\');' .
			'       }' .
			'   }' .
			'	jsSetStyleDisplay(\'_sectionMgtSendButton\', \'block\');' .
			'   jsSetValue(\'_WorkArea\', jTmpStrng);' .
			'}' .
			'function sectionsMgt_populateSectionIDFieldOnEdit(sectionsDtObjct){' .
			'   jsAdd2Select(\'sectionid2\', \'Choose Section to Edit\', \'\', true);' .
			'   var jTmpStrng = \'\';' .
			'	if(jsObjectRecordCount(sectionsDtObjct) > 0){' .
			'		for (var i in sectionsDtObjct){' .
			'         if(sectionsDtObjct[i].sectionid != \'root/\'){' .
			'           jsAdd2Select(\'sectionid2\', (sectionsDtObjct[i].indent + sectionsDtObjct[i].sectionid + \'  -  (\' + sectionsDtObjct[i].description + \')\'), sectionsDtObjct[i].sectionid, false);' .
			'           var jDfltTmpltStrng = \'n/a\';' .
			'           if (jsTrim(sectionsDtObjct[i].defaulttemplatepath) != \'\')' .
			'          		var jDfltTmpltStrng = sectionsDtObjct[i].defaulttemplatepath;' .
			'           var jSrtRdrStrng = \'-1\';' .
			'           if (jsTrim(sectionsDtObjct[i].sortorder) != \'\')' .
			'          		var jSrtRdrStrng = sectionsDtObjct[i].sortorder;' .
			'           jTmpStrng += (sectionsDtObjct[i].sectionid + \'~\' + sectionsDtObjct[i].description + \'~\' + jDfltTmpltStrng + \'~\' + jSrtRdrStrng + \'|\');' .
			'         }' .
			'       }' .
			'   }' .	
			'	jsSetStyleDisplay(\'_sectionMgtSendButton\', \'block\');' .
			'	jsSetStyleDisplay(\'_sectionMgtRemoveLink\', \'block\');' .
			'   jsSetValue(\'_WorkArea\', jTmpStrng);' .
			'}' .
			'function sectionsMgt_populateDerivedFields(){' .
			'	jSlctdSctn = jsGetElementValue(\'sectionid2\');' .
			'   if(jsTrim(jSlctdSctn) != \'\'){' .
			'	    var jWrkRRry = jsGetElementValue(\'_WorkArea\').split(\'|\');' .
			'       for(j=0; j<jWrkRRry.length; j++){' .
			'			jDscrptn = jsGetToken(jWrkRRry[j], \'2\', \'~\', \'\');' . 
			'			jDfltTmpltPth = jsGetToken(jWrkRRry[j], \'3\', \'~\', \'n/a\');' .
			'			jSrtRdr = jsGetToken(jWrkRRry[j], \'4\', \'~\', \'0\');' .
			'           if (jSlctdSctn == jsGetToken(jWrkRRry[j], \'1\', \'~\', \'\')){' .
			'           	jsSetValue(\'sectiondescription\', jDscrptn);' .
			'               if (jDfltTmpltPth != \'n/a\'){' .
			'					if (document.all)' .
			'   					jsSetSelectOption(document.all[\'sectiondefaulttemplate\'], jDfltTmpltPth); ' .
			' 					else' .
			'   					jsSetSelectOption(document.getElementById(\'sectiondefaulttemplate\'), jDfltTmpltPth); ' .
			'				}' .			
			'           	jsSetValue(\'sectionsortorder\', jSrtRdr);' .
			'           }' .
			'       }' .
			'      if (jsGetElementValue(\'sectionsortorder\') == \'-1\')' .
			'      	  jsSetValue(\'sectionsortorder\', \'' . $jssctnsrtrdr . '\');' .
			'      if (jsGetElementValue(\'action\') == \'edit\')' .
			'      	  listPages(jSlctdSctn);' .
			'   }' .
			'   return;' .
			'}' .
			'function sectionsMgt_populateAddFormFields(){' .
			'   jsSetClass(\'_sectionMgtEditLink\', \'copygray\');' .
			'   jsSetClass(\'_sectionMgtAddLink\', \'copyred\');' .
			'   sectionsMgt_resetFormFields();' .
			'   jsSetDisabled(\'sectionid2\', false);' .
			'   jsSetDisabled(\'sectiondescription\', false);' .
			'   jsSetDisabled(\'sectiondefaulttemplate\', false);' .
			'   jsSetDisabled(\'sectionsortorder\', false);' .
			'   jsSetDisabled(\'sectionsendbutton\', false);' .
			'   jsSetValue(\'sectionsendbutton\', \'Create New Section\');' .
			'   jsSetValue(\'action\', \'add\');' .
			'   document.getElementById(\'sectionid2\').focus();' .
			'   jsDataRequestURL(\'pageregistry4sectionsmgt\', \'sectionsMgt_populateSectionIDFieldOnAdd\', \'\');' .  
			'	return;' .
			'}' .
			'function sectionsMgt_populateEditFormFields(){' .
			'   jsSetClass(\'_sectionMgtEditLink\', \'copyred\');' .
			'   jsSetClass(\'_sectionMgtAddLink\', \'copygray\');' .
			'   sectionsMgt_resetFormFields();' .
			'   jsSetDisabled(\'sectionid2\', false);' .
			'   jsSetDisabled(\'sectiondescription\', false);' .
			'   jsSetDisabled(\'sectiondefaulttemplate\', false);' .
			'   jsSetDisabled(\'sectionsortorder\', false);' .
			'   jsSetDisabled(\'sectionsendbutton\', false);' .
			'   jsSetValue(\'sectionsendbutton\', \'Edit Section\');' .
			'   jsSetValue(\'action\', \'edit\');' .
			'   document.getElementById(\'sectionid2\').focus();' .
			'   sectionsMgt_populateSectionIDFieldOnEdit(sectionsDtObjct);' .
			'	return;' .
			'}' .
			'function sectionsMgt_post2db(){' .
			'	var msg = \'\';' .
			'	if (jsIsEmpty(jsGetElementValue(\'sectionid2\')))' .
			'		msg = msg + \'Section is required\n\';' .
			'	if (jsIsEmpty(jsGetElementValue(\'sectiondescription\')))' .
			'		msg = msg + \'Description is required\n\';' .
			'   else if (jsGetElementValue(\'sectiondescription\').indexOf(String.fromCharCode(39)) >= 0)' . 
			'		msg = msg + \'Single Quote is not valid in Description\n\';' .
			'	if (jsIsEmpty(jsGetElementValue(\'sectionsortorder\')))' .
			'		msg = msg + \'Sort order is required\n\';' .
			'   else if (!jsIsInteger(jsGetElementValue(\'sectionsortorder\'), false)) ' .
			'		msg = msg + \'Sort order must be numeric\n\';' .
			'	if (msg != \'\'){' .
			'		alert(msg);' .
			'		return false;' .
			'	}' .
			'	jsAjaxFORM(\'alexjet.php?event=sectionsmgt_post2db\' + \'&uc=\' + jsUC(), \'_sectionmgtform\', true);' .
			'	return true;' .
			'}' .
			'function sectionsMgt_remove(){' .
			'	var msg = \'\';' .
			'   jSctn = jsGetElementValue(\'sectionid2\');' .
			'	if (jsIsEmpty(jSctn))' .
			'		msg = msg + \'A section must be selected!\n\';' .
			'	if (msg != \'\'){' .
			'		alert(msg);' .
			'		return false;' .
			'	}' .
			'	var jNswr = confirm(\'Please confirm you want to REMOVE the section: \n\n[\' +  jSctn + \']\');' .
			'	if(jNswr){' .
			'   	jsSetValue(\'sectionid3\', jSctn);' .
			'		jsAjaxFORM(\'alexjet.php?event=sectionsmgt_remove\' + \'&uc=\' + jsUC(), \'_sectionmgtremoveform\', true);' .
			'		return true;' .
			'	}' .
			'	return false;' .
			'}';
	$jsstring .=
			'function listPages(jSctn){' .
			'	var jprev_sectionID = jglbl_sectionID;' .
			'   if (jsTrim(jprev_sectionID) != \'\'){' .
			'		var jStLmnt = \'_\' + jprev_sectionID.replace(/\//gi, \'_\');' .
			'       jsSetClass(jStLmnt, \'copygray\');' . 
			'	}' .
			'   jglbl_sectionID = jSctn;' .
			'   document.cookie = (\'alexjet_pgmgt=\' + jglbl_sectionID + \';\');' .
			'	var jStLmnt = \'_\' + jglbl_sectionID.replace(/\//gi, \'_\');' .
			'	if (document.all)' .
			'		document.all[jStLmnt].className = \'copyred\';' .
			'	else ' .
			'		document.getElementById(jStLmnt).className = \'copyred\';' .
			'	if (document.all) ' .
			'   	jsSetSelectOption(document.all[\'SectionID\'], jSctn); ' .
			' 	else ' .
			'   	jsSetSelectOption(document.getElementById(\'SectionID\'), jSctn); ' .
			'	if (document.all) ' .
			'   	jsSetSelectOption(document.all[\'TemplatePath\'], fetchSectionDefaultTemplatePath(jSctn)); ' .
			' 	else ' .
			'   	jsSetSelectOption(document.getElementById(\'TemplatePath\'), fetchSectionDefaultTemplatePath(jSctn)); ' .
			'   jsDataRequestURL(\'pageregistry\', \'listPages_doit\', \'sectionid=\' + jSctn);' .  
			'	return;' .
			'}' .
			'function listPages_doit(jRsltBjct){' .
			'   var jStyl = \'border-bottom-width:1px; border-bottom-color:#000000; border-bottom-style:solid; \' + ' . 
			'               \'padding:5px;\' + ' .
			'	            \'border-right-width:1px; border-right-color:#000000; border-right-style:solid;\'; ' .
			'   var jSty2 = \'border-bottom-width:1px; border-bottom-color:#CCCCCC; border-bottom-style:solid; \' + ' .
			'               \'padding:5px;\' + ' .
			'	            \'border-right-width:1px; border-right-color:#CCCCCC; border-right-style:solid;\'; ' .
			'	var jStr = \'<table border="0" cellpadding="0" cellspacing="0"> ' .                          
		    '			     	<tr> ' .
		    '			     	   <td align="center" valign="top" style="\' + jStyl + \' width:150px; background-color:#CCCCCC;"><span class="copy">KEYWORD</span></td> ' .
		    '			     	   <td align="center" valign="top" style="\' + jStyl + \' width:280px; background-color:#CCCCCC;"><span class="copy">TITLE</span></td> ' .
		    '			     	   <td align="center" valign="top" style="\' + jStyl + \' width:120px; background-color:#CCCCCC;"><span class="copy">TEMPLATE</span></td> ' .
		    '			     	   <td align="center" valign="top" style="\' + jStyl + \' width:70px; background-color:#CCCCCC;"><span class="copy">HIDE</span></td> ' .
		    '			     	   <td align="center" valign="top" style="\' + jStyl + \' width:70px; background-color:#CCCCCC;"><span class="copy">MEMBERS</span></td> ' .
		    '			     	   <td align="center" valign="top" style="\' + jStyl + \' width:40px; background-color:#CCCCCC;"><span class="copy">DELETE</span></td> ' .
		    '			     	   <td align="center" valign="top" style="\' + jStyl + \' width:40px; background-color:#CCCCCC;"><span class="copy">ORDER</span></td> ' .			
	        '					</tr>\'; ' . 			
			'	if(jsObjectRecordCount(jRsltBjct) > 0){' .
			'		for (var i in jRsltBjct){' .
			'           var jChkd = \'\'; ' .
			'           if(jRsltBjct[i].pagestatus == \'active\') ' .
			'              jChkd = \' checked\'; ' .
			'           var jPgStr = \'<a href="' . $glbl_websiteaddress . '\' + jRsltBjct[i].pagepath.replace(\'/index.php\', \'\') + \'" title="View Page">\' + jRsltBjct[i].pageid + \'</a>\'; ' .
			'           var jPgTtl = \'&nbsp;\'; ' .
			'           if(jsTrim(jRsltBjct[i].pagetitle) != \'\') ' .
			'              var jPgTtl = jRsltBjct[i].pagetitle; ' .
			'           var jSttsStr = \'<input type="checkbox" value="active" ' .
			'			                        onClick="javascript:return doTogglePageStatus(this, \' + \'' . chr(92) . chr(39) . '\' + jRsltBjct[i].pageid + \'' . chr(92) . chr(39) . '\' + \', \' + \'' . chr(92) . chr(39) . '\' + jRsltBjct[i].sectionid + \'' . chr(92) . chr(39) . '\' + \');"\' + jChkd + \'>\'; ' .
			'           var jMmbrsNlyChkd = \'\'; ' .
			'           if(jRsltBjct[i].membersonly == \'1\') ' .
			'              jMmbrsNlyChkd = \' checked\'; ' .
			'           var jMmbrsNlyStr = \'<input type="checkbox" value="1" ' . $disablemembersonlychkbx . 
			'			                        onClick="javascript:return doTogglePageMembersOnly(this, \' + \'' . chr(92) . chr(39) . '\' + jRsltBjct[i].pageid + \'' . chr(92) . chr(39) . '\' + \', \' + \'' . chr(92) . chr(39) . '\' + jRsltBjct[i].sectionid + \'' . chr(92) . chr(39) . '\' + \');"\' + jMmbrsNlyChkd + \'>\'; ' .
 			'			var jMvDStr = \'<a href="javascript:void(0);" ' . 
            '                              onClick="javascript:return doMovePageSortOrder(\' + \'' . chr(92) . chr(39) . '\' + jRsltBjct[i].pageid + \'' . chr(92) . chr(39) . '\' + \', \' + \'' . chr(92) . chr(39) . '\' + jRsltBjct[i].sectionid + \'' . chr(92) . chr(39) . '\' + \', \' + \'' . chr(92) . chr(39) . 'movedown' . chr(92) . chr(39) . '\' + \');" ' . 
			'			                   title="Move Page Down"><img src="arrowdown.jpg" width="15" height="15" border="0" alt="Move Page Down"></a> \'; ' .
 			'			var jMvPStr = \'<a href="javascript:void(0);" ' . 
            '                              onClick="javascript:return doMovePageSortOrder(\' + \'' . chr(92) . chr(39) . '\' + jRsltBjct[i].pageid + \'' . chr(92) . chr(39) . '\' + \', \' + \'' . chr(92) . chr(39) . '\' + jRsltBjct[i].sectionid + \'' . chr(92) . chr(39) . '\' + \', \' + \'' . chr(92) . chr(39) . 'moveup' . chr(92) . chr(39) . '\' + \');" ' . 
			'			                   title="Move Page Up"><img src="arrowup.jpg" width="15" height="15" border="0" alt="Move Page Up"></a> \'; ' .
			'           var jDltStr = \'<a href="javascript:void(0);" title="Remove Page"' .
			'                              onClick="javascript:return doRemovePage(\' + \'' . chr(92) . chr(39) . '\' + jRsltBjct[i].pageid + \'' . chr(92) . chr(39) . '\' + \', \' + \'' . chr(92) . chr(39) . '\' + jRsltBjct[i].sectionid + \'' . chr(92) . chr(39) . ');">delete</a>\'; ' .
			'			jStr = jStr + \'<tr>\';' .
			'			jStr = jStr + \'<td align="left" valign="top" style="\' + jSty2 + \' width:150px;"><span class="copy">\' + jPgStr + \'</span></td>\';' .
			'			jStr = jStr + \'<td align="left" valign="top" style="\' + jSty2 + \' width:280px;"><span class="copy">\' + jPgTtl + \'</span></td>\';' .
			'			jStr = jStr + \'<td align="left" valign="top" style="\' + jSty2 + \' width:120px;"><span class="copy">\' + jRsltBjct[i].templatedescription + \'</span></td>\';' .
			'			jStr = jStr + \'<td align="center" valign="top" style="\' + jSty2 + \' width:70px;"><span class="copy">\' + \'Active&nbsp;\' + \'</span>\' + jSttsStr + \'</td>\';' .
			'			jStr = jStr + \'<td align="center" valign="top" style="\' + jSty2 + \' width:70px;"><span class="copy">\' + \'Only&nbsp;\' + \'</span>\' + jMmbrsNlyStr + \'</td>\';' .
			'			jStr = jStr + \'<td align="center" valign="top" style="\' + jSty2 + \' width:40px;"><span class="copy">\' + jDltStr + \'</span></td>\';' .
			'			jStr = jStr + \'<td align="center" valign="top" style="\' + jSty2 + \' width:40px;"><span class="copy">\' + jMvDStr + \'&nbsp;\' + jMvPStr + \'</span></td>\';' .
			'			jStr = jStr + \'</tr>\';' .			
			'		}' .
			'	}' .
			'	jStr = jStr + \'<tr><td colspan="7"><br /><br /><span class="copy">\' + jsObjectRecordCount(jRsltBjct) + \'&nbsp;page(s)</span></td></tr></table>\';' .
			'	jsSet(\'_pagesListingCanvas\', jStr);' .
			'	return;' .
			'}';
	// Render html - sections title cell
	$htmlstring1 = '<span class="copy">SECTIONS</span>';	
	// Render html - add page form cell
	$htmlstring2a = '<select name="SectionID" id="SectionID" style="width:150px; color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;"><option value="">Choose Section</option>';
	while ($row = mysql_fetch_assoc($sectionsquery))
		$htmlstring2a .= '<option value="' . $row['SectionID'] . '">' . $row['Description'] . '</option>';
	if (mysql_num_rows($sectionsquery) > 0)
		mysql_data_seek($sectionsquery, 0); // reset pointer
	$htmlstring2a .= '</select>';
	$htmlstring2b = '<select name="TemplatePath" id="TemplatePath" style="width:150px; color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;"><option value="">Choose Template</option>';
	while ($row = mysql_fetch_assoc($templatesquery))
		$htmlstring2b .= '<option value="' . $row['TemplatePath'] . '">' . $row['Description'] . '</option>';
	if (mysql_num_rows($templatesquery) > 0)
		mysql_data_seek($templatesquery, 0); // reset pointer	
	$htmlstring2b .= '</select>';
	$htmlstring2c = '<input type="text" name="PageKeyword" id="PageKeyword" maxlength="35" style="width:150px; color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;">';
	$htmlstring2d = '<input type="button" name="AddPage" value="Add Page" title="Add Page" onClick="javascript:return doAddPage();" style="color:#000000; font-family:Arial; font-size:10.5px; font-weight:bold;">';	
	$htmlstring2 = '<span class="copy">ADD NEW PAGE</span><br />' . 	
				   '<table border="0">' .
				   '    <tr><td valign="top" align="left" style="height:20px;">' . $htmlstring2a . '</td><td valign="top" align="left">' . $htmlstring2b . '</td><td valign="top" align="left">' . $htmlstring2c . '</td><td valign="top" align="left">' . $htmlstring2d . '</td><tr>' .
				   '    <tr><td valign="top" align="left" style="height:20px;">' . '<span class="copyredsmall">section</span>' . '</td><td valign="top" align="left">' . '<span class="copyredsmall">template</span>' . '</td><td valign="top" align="left">' . '<span class="copyredsmall">page keyword</span>' . '</td><td valign="top" align="left">' . '&nbsp;' . '</td><tr>' .	
				   '</table>' ;
	// Sections listing
	$htmlstring3 = '';
	$htmlstring4 = '';
	while ($row = mysql_fetch_assoc($sectionsquery)){
		$htmlstring3 .= '<br /><span class="copygray" id="_' . str_replace('/', '_', $row['SectionID']) . '">' .
					    '&nbsp;&nbsp;<a href="javascript:void(0);" title="List Pages" onclick="javascript:listPages(\'' . $row['SectionID'] . '\');">' .
		                $row['Description'] . '</a>&nbsp;&gt;&nbsp;</span>' . str_repeat('&nbsp;&nbsp;&nbsp;', (substr_count($row['SectionID'], '/')-1)) . '<br />';
		if ($htmlstring4 == '') // Section to show when tool loads
			$htmlstring4 = '<script type="text/javascript">listPages(\'' . $row['SectionID'] . '\');</script>';
	} // while
	if (isset($_COOKIE['alexjet_pgmgt']))
		if(trim($_COOKIE['alexjet_pgmgt']) != '')
			$htmlstring4 = '<script type="text/javascript">listPages(\'' . $_COOKIE['alexjet_pgmgt'] . '\');</script>';
	// Members listing
	$htmlstring5 = '';
	$htmlstring5b = '';
	$htmlstring6 = '';
	$htmlstring7 = '';
	if (trim($glbl_loginpagepath) != ''){
		$htmlstring5 = '<span class="copy">MEMBERS LISTING</span><br /><span class="copyredsmall">Format: username,password<br />One per line<br />Separate with comma<br />Spaces will be removed<br />Username (max. 15 characters)<br />Password (max. 15 characters)</span>';
		$htmlstring5b = '<img src="clear.gif" border="0" width="10" height="1">';
		$htmlstring6 = '<form name="_memberlistingform" id="_memberlistingform"><textarea name="content" id="content" rows="6" cols="50" style="color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;"></textarea><input type="hidden" name="contentid" value="ADMIN_memberslist"></form>' .
					   '<input type="button" value="Save" style="color:#000000; font-family:Arial; font-size:10.5px; font-weight:bold;" onClick="javascript:return doSaveMemberList();">' .
					   '&nbsp;&nbsp;<input type="button" value="Cancel" style="color:#000000; font-family:Arial; font-size:10.5px; font-weight:bold;" onClick="javascript:return membersList_cancel();">';
		$htmlstring7 = '<script type="text/javascript">membersList();</script>';
	}
	// Sections management
	if ($glbl_allowsectionmanagement){
		$htmlstring8a = '<span class="copy" id="_sectionMgtLink"><a href="javascript:void(0);" title="Add/Edit Sections" onClick="javascript:return sectionsMgt_toggleLink();">SECTION MANAGEMENT</a>&nbsp;</span><span class="copy" id="_sectionMgtLinkSymbol">(+)</span>';
		$htmlstring8b = '<img src="clear.gif" border="0" width="10" height="1">';
		$htmlstring8c = '<option value="">Choose One</option>';
		while ($row = mysql_fetch_assoc($templatesquery))
			$htmlstring8c .= '<option value="' . $row['TemplatePath'] . '">' . $row['Description'] . '</option>';
		$htmlstring8c = '&nbsp;<div id="_sectionMgtCanvas" style="height:200px; display:none;">' .
				        '<table border="0"><tr>' .
				        '<td valign="top" align="right"><span class="copygray" id="_sectionMgtEditLink">&nbsp;&nbsp;<a href="javascript:void(0);" title="Edit Sections" onclick="javascript:sectionsMgt_populateEditFormFields();">Edit</a>&nbsp;&gt;&nbsp;</span><br /><br />' .
				        '<span class="copygray" id="_sectionMgtAddLink">&nbsp;&nbsp;<a href="javascript:void(0);" title="Add Sections" onclick="javascript:sectionsMgt_populateAddFormFields();">Add</a>&nbsp;&gt;&nbsp;</span></td>' .
						'<td valign="top" align="left"><form name="_sectionmgtform" id="_sectionmgtform" method="post"><table border="0" style="padding-left:20px;">' .
						'<tr><td><span class="copy">* Section ID:&nbsp;</span></td><td><select id="sectionid2" name="sectionid2" disabled onChange="javascript:return sectionsMgt_populateDerivedFields();" style="width:200px; color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;"></select></td></tr>' .
						'<tr><td><span class="copy">* Description:&nbsp;</span></td><td><input type="text" id="sectiondescription" name="sectiondescription" value="" size="30" maxlength="50" disabled style="width:200px; color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;" /></td></tr>' .
						'<tr><td><span class="copy">&nbsp; Default Template:&nbsp;</span></td><td><select id="sectiondefaulttemplate" name="sectiondefaulttemplate" disabled style="width:200px; color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;">' . $htmlstring8c . '</select></td></tr>' .
						'<tr><td><span class="copy">* Sort Order:&nbsp;</span></td><td><input type="text" id="sectionsortorder" name="sectionsortorder" value="" size="10" maxlength="5" disabled style="width:50px; color:#000000; font-family:Arial; font-size:10.5px; font-weight:normal;" /></td></tr>' .
						'<tr><td>&nbsp;</td><td><br /><div id="_sectionMgtSendButton" style="display:none;"><input type="button" id="sectionsendbutton" name="sectionsendbutton" value="" disabled style="color:#000000; font-family:Arial; font-size:10.5px; font-weight:bold;" onClick="javascript:return sectionsMgt_post2db();" /><span class="copyredsmall">&nbsp;&nbsp;* required fields</span</div></td></tr>' .
						'</table><input type="hidden" id="action" name="action" value="" /></form>' .
						'<form name="_sectionmgtremoveform" id="_sectionmgtremoveform" method="post">' .
						'<div id="_sectionMgtRemoveLink" style="display:none; padding-left:25px;"><span class="copygray"><a href="javascript:void(0);" title="Remove Section" onClick="javascript:return sectionsMgt_remove();">Remove Section</a></span></div>' .
						'<input type="hidden" id="sectionid3" name="sectionid3" value="" /></form>' .
						'<input type="hidden" id="_WorkArea" name="_WorkArea" value="" /></td>' .
				        '</tr></table>' .
				        '</div>';
	}
	// Render html - put all together
	$htmlstring = '<table border="0">' . 
				  '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="1"></td>' .
				  '       <td style="width:200px; padding-top:50px; border-bottom-width:1px; border-bottom-color:#990000; border-bottom-style:dashed;" align="right" valign="top">' . $htmlstring1 . '</td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed; border-bottom-width:1px; border-bottom-color:#990000; border-bottom-style:dashed;"><img src="clear.gif" border="0" width="10" height="1"></td>' .
				  '		  <td style="width:770px; height:50px; padding-left:10px; padding-top:10px; border-bottom-width:1px; border-bottom-color:#990000; border-bottom-style:dashed;" align="left" valign="top">' . $htmlstring2 . '</td>' . 
				  '   </tr>' . 
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="1"></td>' .
				  '       <td style="width:200px;" align="right" valign="top">' . $htmlstring3 . '</td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed;"><img src="clear.gif" border="0" width="10" height="1"></td>' .
				  '       <td style="width:770px; height:400px; padding-left:10px;" align="left" valign="top" id="_pagesListingCanvas"></td>' .
				  '   </tr>';


	if (trim($glbl_loginpagepath) != '')
		$htmlstring .=
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="1"></td>' .
				  '       <td style="width:200px; padding-top:25px; border-top-width:1px; border-top-color:#990000; border-top-style:dashed;" align="right" valign="top">' . $htmlstring5 . '</td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed; border-top-width:1px; border-top-color:#990000; border-top-style:dashed;">' . $htmlstring5b . '</td>' .
				  '       <td style="width:770px; height:160px; padding-top:10px; padding-left:10px; border-top-width:1px; border-top-color:#990000; border-top-style:dashed;" align="left" valign="top">' . $htmlstring6 . '</td>' .
				  '   </tr>';
	if ($glbl_allowsectionmanagement)
		$htmlstring .=
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="1"></td>' .
				  '       <td style="width:200px; padding-top:10px; border-top-width:1px; border-top-color:#990000; border-top-style:dashed;" align="right" valign="top">' . $htmlstring8a . '</td>' .
				  '       <td style="width:10px; border-right-width:1px; border-right-color:#990000; border-right-style:dashed; border-top-width:1px; border-top-color:#990000; border-top-style:dashed;">' . $htmlstring8b . '</td>' .
				  '       <td style="width:770px; padding-top:10px; padding-left:10px; border-top-width:1px; border-top-color:#990000; border-top-style:dashed;" align="left" valign="top">' . $htmlstring8c . '</td>' .
				  '   </tr>';
	$htmlstring .=
			      '   <tr>' .
				  '       <td style="width:20px;"><img src="clear.gif" border="0" width="18" height="20"></td>' .
				  '       <td style="width:200px;"></td>' .
				  '       <td style="width:10px;"></td>' .
				  '       <td style="width:770px;"></td>' .
				  '   </tr>' .
			      '</table>' . $htmlstring4 . $htmlstring7;
	
	return renderPageCanvas('PAGE MANAGEMENT TOOL', $htmlstring, $jsstring, '', 'dojo,alexjet',
	                        (parse_url($glbl_websiteaddress, PHP_URL_SCHEME) . '://' . parse_url($glbl_websiteaddress, PHP_URL_HOST) . $parentpagepath));
}

function event_fn_addpage($pagekeyword, $templatepath, $sectionid) {
	// Add a page
	global $glbl_physicalwebrootlocation, $glbl_uploadsrootfolder, $glbl_companyname, $glbl_dbtableprefix,
	       $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $const_libraryfolder;
    // Must be logged in
	if (!getAuthenticationFlag())
		return '// Invalid.';
	// Validation
	if ($pagekeyword == '' || strlen($pagekeyword) > 35 || $templatepath == '' || $sectionid == '')   
		return 'alert(\'Invalid. (azul)\');';
	if (substr($sectionid, -1) != '/')
		return 'alert(\'Invalid. (rosado)\');';	
	// Page must not already exist as locked = 1
	$pagequery = execQuery('SELECT PageID FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\' AND LockPage != 0 LIMIT 1');
	while ($row = mysql_fetch_assoc($pagequery))
		return 'alert(\'Invalid: The page [' . $pagekeyword . '] is locked!\');';
	// Paths
	$sectionid2 = $sectionid;
	if ($sectionid2 == 'root/')
		$sectionid2 = '';
	$pagepath = $glbl_physicalwebrootlocation . $sectionid2 . $pagekeyword . '/index.php';
	if (file_exists($pagepath))
		return 'alert(\'Invalid: The page [' . $pagekeyword . '] already exists!\');';
	if (!file_exists($glbl_physicalwebrootlocation . $sectionid2))
		return 'alert(\'Invalid: A section folder must exist!\');';
	// Create index.php page content
	$templatecontent =
		'<?php' . chr(10) .
		'' . chr(10) .
		'// Property of: {$companyname$}' . chr(10) .
		'// {$sectionid2$}{$pagekeyword$}/index.php' . chr(10) .
		'// Updated: {$date$}' . chr(10) .
		'' . chr(10) .
		'// Variables' . chr(10) .
		'$path2root = \'{$path2root$}\'; // end with / or blank if already in the root' . chr(10) .
		'$pageid = \'{$pagekeyword$}\';' . chr(10) .
		'$sectionid = \'{$sectionid$}\';' . chr(10) .
		'$templatepath = \'{$templatepath$}\';' . chr(10) .
		'$_GET[\'event\'] = \'renderdynamicpage\';' . chr(10) .
		'include($path2root . \'' . $const_libraryfolder . '/config.php\');' . chr(10) .
		'include($path2root . \'' . $const_libraryfolder . '/alexjet.php\');' . chr(10) .
		'' . chr(10) .
		'?>';
	$path2root = str_repeat('../', count(explode('/', str_replace('root/', '', $sectionid))));
	$templatecontent = str_replace('{$pagekeyword$}', $pagekeyword, $templatecontent);
	$templatecontent = str_replace('{$templatepath$}', $templatepath, $templatecontent);
	$templatecontent = str_replace('{$sectionid2$}', $sectionid2, $templatecontent);
	$templatecontent = str_replace('{$path2root$}', $path2root, $templatecontent);
	$templatecontent = str_replace('{$sectionid$}', $sectionid, $templatecontent);
	$templatecontent = str_replace('{$companyname$}', $glbl_companyname, $templatecontent);
	$templatecontent = str_replace('{$date$}', date("m/d/Y"), $templatecontent);
	// Create folder under section if needed
	if (!file_exists(($glbl_physicalwebrootlocation . $sectionid2 . $pagekeyword)))
		mkdir(($glbl_physicalwebrootlocation . $sectionid2 . $pagekeyword));  
	// Create folder under uploads if needed
	$uploadsfolderpath = str_replace('/', '_', $sectionid);
	$uploadsfolderpath = ($glbl_physicalwebrootlocation . $glbl_uploadsrootfolder . $uploadsfolderpath . $pagekeyword);
	if (!file_exists($uploadsfolderpath))
		mkdir($uploadsfolderpath);  
	// Create index page
	$fh = fopen(($pagepath), 'w') or die("can't open file");
	fwrite($fh, $templatecontent);
	fclose($fh);
	// Add editable area title for new page
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link)
	  die(mysql_error());
	// Select database on MySQL server
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
	   die(mysql_error());
	// Insert into editable content table 'short title'
	$editableareanamestring = ($sectionid . $pagekeyword . '_editablepagetitle');
	$result = mysql_query('SELECT ContentID FROM ' . $glbl_dbtableprefix . 'editablecontent WHERE ContentID = \'' . $editableareanamestring . '\'');
	if (mysql_affected_rows()==0){ // not found
		$query = sprintf('INSERT INTO ' . $glbl_dbtableprefix . 'editablecontent (ContentID, Content) ' .
						 'VALUES (\'' . $editableareanamestring . '\', \'' . $pagekeyword . '\')');
		$result = mysql_query($query); // Perform Query
	}
	// Get highest sort order and assign next order
    $result = mysql_query('SELECT SortOrder FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE SectionID = \'' . $sectionid . '\' AND LockPage = 0 ORDER BY SortOrder DESC LIMIT 1');
	$maxpagesortorder = 1;
	while ($row = mysql_fetch_assoc($result)){
		$maxpagesortorder = $row['SortOrder'];
		if (is_numeric($maxpagesortorder))
			$maxpagesortorder++;
		else
			$maxpagesortorder = 1;	
	} // while	
	// Insert into pages register table
	$pagelevel = strlen(str_replace('.', '', $path2root));
	$result = mysql_query('SELECT PageID FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\' LIMIT 1');
	if (mysql_affected_rows()==0){ // not found
		$query = sprintf('INSERT INTO ' . $glbl_dbtableprefix . 'pageregistry (PageID, SectionID, TemplatePath, PagePath, PageLevel, PageStatus, SortOrder, PageTitleContentID, LockPage, MembersOnly, UID) ' .
						 'VALUES (\'' . $pagekeyword . '\', \'' . $sectionid . '\',  \'' . $templatepath . '\', ' .
						 '\'' . ($sectionid2 . $pagekeyword . '/index.php') . '\', \'' . $pagelevel . '\', \'inactive\', \'' . $maxpagesortorder . '\', ' .
						 '\'' . $editableareanamestring . '\', 0, 0, \'' . makeUC() . '\')');
		$result = mysql_query($query); // Perform Query
	}
	// Close connection
	mysql_close($link);
	// Message for Javascript & User defined function hook
	return 'listPages(\'' . $sectionid . '\');' . execUserDefinedFunction('USRDFNDafter_event_addpage');
}

function event_fn_togglepagestatus($pagekeyword, $sectionid, $pagestatus) {
	// Show/Hide page
	global $glbl_dbtableprefix;
	if (!getAuthenticationFlag()) // Must be logged in
		return '// Invalid.';
	// Validation
	if ($pagekeyword == '' || $sectionid == '' || ($pagestatus != 'active' && $pagestatus != 'inactive'))
		return 'alert(\'Invalid. (blanco)\');';
	// Page must exist on registry
	$pagequery = execQuery('SELECT PageID FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\' AND LockPage = 0 LIMIT 1');
	while ($row = mysql_fetch_assoc($pagequery)){
		execQuery('UPDATE ' . $glbl_dbtableprefix . 'pageregistry SET PageStatus = \'' . $pagestatus . '\'  WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\'');
		return 'listPages(\'' . $sectionid . '\');' . execUserDefinedFunction('USRDFNDafter_event_togglepagestatus'); // Re-list pages & User defined function hook
	} // while	

	// Page not found
	return '// Invalid.';
}

function event_fn_togglepagemembersonly($pagekeyword, $sectionid, $pagemembersonly) {
	// Show/Hide page
	global $glbl_dbtableprefix;
	if (!getAuthenticationFlag()) // Must be logged in
		return '// Invalid.';
	// Validation
	if ($pagekeyword == '' || $sectionid == '' || ($pagemembersonly != '1' && $pagemembersonly != '0'))
		return 'alert(\'Invalid. (piel)\');';
	// Page must exist on registry
	$pagequery = execQuery('SELECT PageID FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\' AND LockPage = 0 LIMIT 1');
	while ($row = mysql_fetch_assoc($pagequery)){
		execQuery('UPDATE ' . $glbl_dbtableprefix . 'pageregistry SET MembersOnly = ' . $pagemembersonly . ' WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\'');
		return 'listPages(\'' . $sectionid . '\');' . execUserDefinedFunction('USRDFNDafter_event_togglepagemembersonly'); // Re-list pages & User defined function hook
	} // while	

	// Page not found
	return '// Invalid.';
}

function event_fn_movepagesortorder($pagekeyword, $sectionid, $direction) {
	// Move page up/down in sort order
	global $glbl_dbtableprefix;
	if (!getAuthenticationFlag()) // Must be logged in
		return '// Invalid.';
	// Validation
	if ($pagekeyword == '' || $sectionid == '' || ($direction != 'movedown' && $direction != 'moveup'))
		return 'alert(\'Invalid. (manzana)\');';
	// Move Up
	if ($direction == 'moveup'){
		// Get current page sort order
		$currentpagesortorder = 0;
		$uppagesortorder = 0;
		$uppageid = '';
		$pagequery = execQuery('SELECT SortOrder FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\' AND LockPage = 0 LIMIT 1');
		while ($row = mysql_fetch_assoc($pagequery))
			$currentpagesortorder = $row['SortOrder'];
		// Get 'up (previous)' page sort order
		$pagequery = execQuery('SELECT PageID, SortOrder FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE SortOrder < ' . $currentpagesortorder . ' AND PageID != \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\' AND LockPage = 0 ORDER BY SortOrder DESC LIMIT 1');
		while ($row = mysql_fetch_assoc($pagequery)){
			$uppageid = $row['PageID'];
			$uppagesortorder = $row['SortOrder'];
		} // while
		// Update table rows
		if ($uppagesortorder != 0){
			execQuery('UPDATE ' . $glbl_dbtableprefix . 'pageregistry SET SortOrder = \'' . $uppagesortorder . '\'  WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\'');
			execQuery('UPDATE ' . $glbl_dbtableprefix . 'pageregistry SET SortOrder = \'' . $currentpagesortorder . '\'  WHERE PageID = \'' . $uppageid . '\' AND SectionID = \'' . $sectionid . '\'');
		} // if
	} // if
	// Move Down
	if ($direction == 'movedown'){
		// Get current page sort order
		$currentpagesortorder = 9999999;
		$downpagesortorder = 0;
		$downpageid = '';
		$pagequery = execQuery('SELECT SortOrder FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\' AND LockPage = 0 LIMIT 1');
		while ($row = mysql_fetch_assoc($pagequery))
			$currentpagesortorder = $row['SortOrder'];
		// Get 'down (next)' page sort order
		$pagequery = execQuery('SELECT PageID, SortOrder FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE SortOrder > ' . $currentpagesortorder . ' AND PageID != \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\' AND LockPage = 0 ORDER BY SortOrder LIMIT 1');
		while ($row = mysql_fetch_assoc($pagequery)){
			$downpageid = $row['PageID'];
			$downpagesortorder = $row['SortOrder'];
		} // while
		// Update table rows
		if ($downpagesortorder != 0){
			execQuery('UPDATE ' . $glbl_dbtableprefix . 'pageregistry SET SortOrder = \'' . $downpagesortorder . '\'  WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\'');
			execQuery('UPDATE ' . $glbl_dbtableprefix . 'pageregistry SET SortOrder = \'' . $currentpagesortorder . '\'  WHERE PageID = \'' . $downpageid . '\' AND SectionID = \'' . $sectionid . '\'');
		} // if
	} // if

	// Message for Javascript & User defined function hook
	return 'listPages(\'' . $sectionid . '\');' . execUserDefinedFunction('USRDFNDafter_event_movepagesortorder');
}

function event_fn_removepage($pagekeyword, $sectionid) {
	global $glbl_physicalwebrootlocation, $glbl_dbtableprefix;
	// Remove page
	if (!getAuthenticationFlag()) // Must be logged in
		return '// Invalid.';
	// Validation
	if ($pagekeyword == '' || $sectionid == '')
		return 'alert(\'Invalid. (naranja)\');';
	// Page cannot be locked = 1
	$pagequery = execQuery('SELECT PageID FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\' AND LockPage != 0 LIMIT 1');
	while ($row = mysql_fetch_assoc($pagequery))
		return 'alert(\'Invalid: The page [' . $pagekeyword . '] is locked (mandarina)!\');';
	// Page cannot be a section
	$pagequery = execQuery('SELECT SectionID FROM ' . $glbl_dbtableprefix . 'pagesections WHERE SectionID = \'' . str_replace('root/', '', $sectionid) . $pagekeyword . '/\' LIMIT 1');
	while ($row = mysql_fetch_assoc($pagequery))
		return 'alert(\'Page [' . $pagekeyword . '] cannot be removed because it is a Section.\n\nRemove the Section first.\');';
	// Section path	
	$sectionid2 = $sectionid;
	if ($sectionid2 == 'root/')
		$sectionid2 = '';
	// Remove file
	$pagepath = $glbl_physicalwebrootlocation . $sectionid2 . $pagekeyword . '/index.php';
	unlink($pagepath); 
	// Remove folder
	rmdir(($glbl_physicalwebrootlocation . $sectionid2 . $pagekeyword)); 
	// delete from editable areas
	$editableareanamestring = ($sectionid . $pagekeyword . '_');
	execQuery('DELETE FROM ' . $glbl_dbtableprefix . 'editablecontent WHERE ContentID LIKE \'' . $editableareanamestring . '%\'');
	// delete from pages registry
	execQuery('DELETE FROM ' . $glbl_dbtableprefix . 'pageregistry WHERE PageID = \'' . $pagekeyword . '\' AND SectionID = \'' . $sectionid . '\' AND LockPage = 0');

	// Message for Javascript & reload page & user defined function hook
	return 'alert(\'Page: [' . $pagekeyword . '] has been Removed.\');' .
	       'listPages(\'' . $sectionid . '\');' .
		   execUserDefinedFunction('USRDFNDafter_event_removepage');
}

function event_fn_sectionsmgt_post2db($action, $_POST) {
	// Initialize data object
	global $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
	// Post to db for section management
	if (!getAuthenticationFlag()) // Must be logged in
		return '// Invalid.';
	if ($action != 'add' && $action != 'edit' && $action != 'remove')
		return 'alert(\'Invalid. (mango)\');';
	$sectionid = '';
	$description = '';
	$defaulttemplatepath = '';
	$sortorder = '';
	if (isset($_POST['sectionid2']))
		$sectionid = trim($_POST['sectionid2']);
	if (isset($_POST['sectiondescription']))
		$description = trim($_POST['sectiondescription']);
	if (isset($_POST['sectiondefaulttemplate']))
		$defaulttemplatepath = trim($_POST['sectiondefaulttemplate']);
	if (isset($_POST['sectionsortorder']))
		$sortorder = trim($_POST['sectionsortorder']);
	if ($sectionid == '' || $description == '' || $sortorder == '' || !is_int((int)$sortorder))
		return 'alert(\'Invalid. (fresa)\');';
	// Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link) 
		return '// Invalid.';
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
		return '// Invalid.';
	// "Inject" prevention   
    $sectionid = mysql_real_escape_string($sectionid);
    $description = mysql_real_escape_string($description);
    $defaulttemplatepath = mysql_real_escape_string($defaulttemplatepath);
    $sortorder = mysql_real_escape_string($sortorder);
	// Make query
	if ($action == 'add'){
		$tblnm = $glbl_dbtableprefix . 'pageregistry';
		$querystring = 

<<<HDSTRING
	SELECT SectionID FROM $tblnm WHERE SectionID = '$sectionid'
HDSTRING;
		
		// Perform query
		$result = mysql_query($querystring);
		// Check if section does not already exist
	 	if (mysql_num_rows($result) < 1){
			$tblnm = $glbl_dbtableprefix . 'pagesections';
			$querystring = 

<<<HDSTRING
	INSERT 
	INTO   $tblnm
		   (SectionID, Description, DefaultTemplatePath, SortOrder, ShowInPageManager)
	VALUES ('$sectionid', '$description', '$defaulttemplatepath', '$sortorder', 1)
HDSTRING;
		
	 	}
		else		
			$querystring = ''; // Do nothing, INVALID
	}
	else if ($action == 'edit'){
		$tblnm = $glbl_dbtableprefix . 'pagesections';
		$querystring = 

<<<HDSTRING
	UPDATE $tblnm
	SET    Description = '$description',
	       DefaultTemplatePath = '$defaulttemplatepath',
	       SortOrder = '$sortorder'
	WHERE  SectionID = '$sectionid' AND SectionID != 'root/' AND ShowInPageManager = 1
HDSTRING;
		
	}	
	// Perform query
	$result = mysql_query($querystring);
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result)
   		return '// Invalid.';
	// Close connection
	mysql_close($link);	   

	return 'window.location.reload(true);'; // Reload page
}

function event_fn_sectionsmgt_remove($sectionid) {
	// Initialize data object
	global $mysql_hostname, $mysql_username, $mysql_password, $mysql_databasename, $glbl_dbtableprefix;
	// Post to db for section management
	if (!getAuthenticationFlag()) // Must be logged in
		return '// Invalid.';
	if ($sectionid == '')
		return 'alert(\'Invalid. (lima)\');';
	// Connect to MySQL
	$link = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	if (!$link) 
		return '// Invalid.';
    // Select database on MySQL server 
	$db_selected = mysql_select_db($mysql_databasename, $link);
	if (!$db_selected)
		return '// Invalid.';
	// "Inject" prevention   
    $sectionid = mysql_real_escape_string($sectionid);
	$jsstrng = 'window.location.reload(true);'; // On success, reload page
	// Make query
	$tblnm = $glbl_dbtableprefix . 'pageregistry';
	$querystring = 

<<<HDSTRING
	SELECT SectionID FROM $tblnm WHERE SectionID = '$sectionid'
HDSTRING;

	// Perform query
	$result = mysql_query($querystring);
	// Check if there are any pages under the section
	if (mysql_num_rows($result) >= 1)
		$jsstrng = 'alert(\'Before removing the section:\n\n[' . $sectionid . ']\n\nAll pages under it must be removed first!\')';
	else{ // Remove section
		// Make query
		$tblnm = $glbl_dbtableprefix . 'pagesections';
		$querystring = 
	
<<<HDSTRING
	DELETE FROM $tblnm
	WHERE  SectionID = '$sectionid' AND SectionID != 'root/' AND ShowInPageManager = 1
HDSTRING;
		
		// Perform query
		$result = mysql_query($querystring);

	} // if
	// Check result | This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result)
   		return '// Invalid.';
	// Close connection
	mysql_close($link);	   

	return $jsstrng;
}

?>