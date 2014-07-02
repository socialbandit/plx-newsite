// alexjet.js | alexjet JavaScript function library | VERSION 3.6
// Copyright 2006-2010 Alexander Media, Inc. - All rights reserved.
// By: 08.14.2006_rg, 02.08.2007_rg, 02.24.2007_rg, 04.06.2007_rg, 07.26.2007_rg, 11.09.2007_rg,
//     01.20.2008_rg, 02.22.2008_rg, 03.27.2008_rg, 05.09.2008_rg, 05.23.2008_rg, 06.26.2008_rg,
//     08.03.2008_rg, 09.26.2008_rg, 10.01.2008_rg, 01.11.2009_rg, 04.22.2009_rg, 11.25.2009_rg,
//     08.15.2010_rg, 09.05.2010_rg, 09.15.2010_rg, 11.06.2010_rg

// Ajax functions
function jsAjaxErrorHandler(type, error) {
	var msg = "Communication error!\n" +
	error.message;
	alert(msg);
} // jsAjaxErrorHandler()

function jsAjaxDoReturnValue(type, evaldObj) {
	return evaldObj;
} // jsOnSelectTopic_doReturnValue()

function jsAjaxURL(jRl, jSync){ // jRl = server script url | jSync = false (asynchronous) / true (synchronous), false is the usual setting
	// connect to the server
	dojo.io.bind({
		url: jRl,
		load: jsAjaxDoReturnValue,
		error: jsAjaxErrorHandler,
		mimetype: "text/javascript",
		sync: jSync
	}); // dojo.io.bind
} // jsAjaxURL()

function jsAjaxFORM(jRl, jFrmNm, jSync){ // jRl = form processor url | jFrmNm = form name | jSync = false (asynchronous) / true (synchronous), false is the usual setting
	// get form object
	jFrmObj = document.getElementById(jFrmNm);
	// connect to the server	
	dojo.io.bind({
	url: jRl,
	formNode: jFrmObj,
	method: "POST",
	load: jsAjaxDoReturnValue,
	error: jsAjaxErrorHandler,
	mimetype: "text/javascript",
	sync: jSync
	});
} // jsAjaxFORM()

uc = Date.parse(new Date()) + '_' + Math.round(100*Math.random()) + '_';
var ucIncrement = 0; // a value to be incremented
function jsUC(){  // return the unique value
	++ucIncrement;
	return (uc + (ucIncrement+''));
} // jsUC()

// Data request functions
function jsDataRequestURL(jRqstKy, jCllbckFnctn, jRlPrmtrs){ // jRqstKy = request key | jCllbckFnctn = callback function | jRlPrmtrs = url parameters
	if (jRlPrmtrs.length)
		jRlPrmtrs = '&' + jRlPrmtrs;
	jsAjaxURL(scriptwebaddress + '?event=datarequest&requestkey=' + jRqstKy + '&callbackfunction=' + jCllbckFnctn + jRlPrmtrs + '&uc=' + jsUC(), true); // post data
} // jsDataRequestURL()

function jsObjectRecordCount(jBjct){ // jBjct = object
	var jRcrdCnt = 0;
	if (jBjct.empty) // When the elememt 'empty' exists, it indicates that the query did not return anything
		return jRcrdCnt;
	for (var i in jBjct) // Loop over the object
		++jRcrdCnt;
	return jRcrdCnt;
} // jsObjectRecordCount()

// Generic functions
function jsInitIfUndefined(variablename, defaultvalue){
	// Initialize a variable to 'defaultvalue' if not already defined
	if (typeof(window[variablename]) == "undefined") // variable is undefined
		return defaultvalue;
	return eval(variablename); // already defined, keep value
} // jsInitIfUndefined()

function jsTrim(jVl){ // jVl = value to 'trim'
	return jVl.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, "");
} // jsTrim()

function jsSet(jLmntID, jVl){  // jLmntID = element id | jVl = value
	// Set the 'innerHTML' of an element
	if (document.all)
		document.all[jLmntID].innerHTML = jVl;
	else
		document.getElementById(jLmntID).innerHTML = jVl;
} // jsSet()

function jsSetValue(jLmntID, jVl){  // jLmntID = element id | jVl = value
	// Set the 'value' of an element
	if (document.all)
		document.all[jLmntID].value = jVl;
	else
		document.getElementById(jLmntID).value = jVl;
} // jsSetValue()

function jsSetSelectOption(jFrmFldBjct, jVl){ // jFrmFldBjct = form field object | jVl = value to set with
	// Set the 'selected' option of a select input list
	for (i=0; i < jFrmFldBjct.options.length; i++){
		if (jFrmFldBjct.options[i].value == jVl)
			jFrmFldBjct.options[i].selected = true;
	}
} // jsSetSelectOption()

function jsSetClass(jLmntID, jClssNm){ // jLmntID = element id | jClssNm = class name
	// Set the 'class' of an element
	if (document.all)
		document.all[jLmntID].className = jClssNm;
	else
		document.getElementById(jLmntID).className = jClssNm;
} // jsSetClass()

function jsSetDisabled(jLmntID, jBlnFlg){ // jLmntID = element id | jBlnFlg = boolean flag
	// Set the 'disabled' attribute of an element
	if (document.all)
		document.all[jLmntID].disabled = jBlnFlg;
	else
		document.getElementById(jLmntID).disabled = jBlnFlg;
} // jsSetDisabled()

function jsGetElement(jLmntID){ // jLmntID = element id
	if (document.all)
		return document.all[jLmntID];
	else 
		return document.getElementById(jLmntID);
} // jsGetElement()

function jsGetElementValue(jLmntID){ // jLmntID = element id
	if (document.all)
		return document.all[jLmntID].value;
	else
		return document.getElementById(jLmntID).value;
} // jsGetElementValue()

function jsGetElementInnerHTML(jLmntID){ // jLmntID = element id
	// Get the 'innerHTML' of an element
	if (document.all)
		return document.all[jLmntID].innerHTML;
	else
		return document.getElementById(jLmntID).innerHTML;
} // jsGetElementInnerHTML()

function jsGetElementClass(jLmntID){ // jLmntID = element id
	// Get the 'class' of an element
	if (document.all)
		return document.all[jLmntID].className;
	else
		return document.getElementById(jLmntID).className;
} // jsGetElementClass()

function jsAdd2Select(jLmntID, jTxt, jVl, MptyFlg){ // jLmntID = element id | jTxt = option text | jVl = option value | MptyFlg = empty flag
	if (MptyFlg){ // Remove all options from select list
		if (document.all)
			for(var k = document.all[jLmntID].options.length - 1; k >= 0; k--)
				document.all[jLmntID].options[k] = null;
		else
			for(var k = document.getElementById(jLmntID).options.length - 1; k >= 0; k--)
				document.getElementById(jLmntID).options[k] = null;	
	} // if
	// Add options to select list
	if (jsTrim(jTxt) == '' && jsTrim(jVl) == '')
		return;
	jNwPtn = new Option(jTxt, jVl);	
	if (document.all)
		document.all[jLmntID].options[document.all[jLmntID].options.length++] = jNwPtn;
	else
        document.getElementById(jLmntID).options[document.getElementById(jLmntID).options.length++] = jNwPtn;
	return;
} // jsAdd2Select()

function jsIsEmpty(jStrng){ // jStrng = string to check if empty
	var jStrng2 = jsTrim(jStrng);
	return ((jStrng2 == null) || (jStrng2.length == 0));
} // jsIsEmpty()

function jsIsEmptyRadio(jFrmFldNm, jFrmNmbr){ // jFrmFldNm = radio form field name | jFrmNmbr = form number
	for (i=document.forms[jFrmNmbr][jFrmFldNm].length-1; i > -1; i--)
		if(document.forms[jFrmNmbr][jFrmFldNm][i].checked) 
			return false;
	return true;
} // jsIsEmptyRadio()

var reEmail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i;
function jsIsEmail(jMls){ // jMls = email to validate (empty is Ok)
	return reEmail.test(jMls);
} // jsIsEmail()

var reFloat = /^-?((\d+(\.\d*)?)|((\d*\.)?\d+))$/;
function jsIsNumber(jNmbr) { // jNmbr = number to check
	return reFloat.test(jNmbr);
} // jsIsNumber()

function jsIsInteger(jNmbr, jSgndNtgr) { // jNmbr = number to check | jSgndNtgr = allow signed integer (true/false)
	if (jSgndNtgr)
		return (jNmbr.toString().search(/^-?[0-9]+$/) == 0); // allows negative sign
	else
		return (jNmbr.toString().search(/^[0-9]+$/) == 0); // does not allow negative sign
} // jsIsInteger()

function jsGetToken(jVl, jTknPs, jDlmtr, jDfltVl){ // jVl = string value to extract from | jTknPs = token position to extract, optional {last} | jDlmtr = delimiter | jDfltVl = default value when token not found
	var jTknsRry = jVl.split(jDlmtr); // make an array
	if (jTknPs == 'last'){ //  last token
		if (jTknsRry.length > 0)
			return jTknsRry[(jTknsRry.length-1)];
		else	
			return jDfltVl;	
	}
	if (jTknPs < 1) // token is not numeric
		return jDfltVl;
	if (jTknsRry.length < jTknPs)
		return jDfltVl;
	return jTknsRry[(jTknPs-1)];
} // jsGetToken()

function jsAppendToken(jVl, jPpndVl, jDlmtr){ // jVl = string value to append to | jPpndVl = value to be appended | jDlmtr = delimiter
	if (jVl != '')
		jVl = jVl + jDlmtr + jPpndVl;
	else
		jVl = jPpndVl;
	return jVl;
} // jsAppendToken()

function ctcd(jCtn, jVl){ // jCtn = action | jVl = value
	if (jCtn == 'search'){
		for (var f=0; f<document.forms.length; f++)
			if (typeof(document.forms[f]['citycode']) != "undefined")
				return true;
	} // if
	if (jCtn == 'write'){
		for (var f=0; f<document.forms.length; f++)
			if (typeof(document.forms[f]['citycode']) != "undefined")
				document.forms[f]['citycode'].value = jVl;
	} // if
	return false;
}

// Block & Table functions
function jsCollapseToggle(jLmntID){ // jLmntID = element id
	if (document.all)
		var jTrgt = document.all[jLmntID];
	else
		var jTrgt = document.getElementById(jLmntID);
    if (jTrgt.style.display == 'block')
        jTrgt.style.display = 'none';
    else 
        jTrgt.style.display = 'block';
} // jsCollapseToggle()

function jsSetStyleDisplay(jLmntID, jVl){ // jLmntID = element id | jVl = value {block or none}
	// Set the 'style.display' attribute of an element
	if (document.all)
		document.all[jLmntID].style.display = jVl;
   else
       document.getElementById(jLmntID).style.display = jVl;
} // jsSetStyleDisplay()

// Library functions
function dataobject(companyname, currentyear, createdyear, imagesnamelist,
					imagenameattributevalue, imageclassname, imagesortorder, imagecellidattributevalue, imagenotesidattributevalue,
					copyrightidattributevalue, toolboxidattributevalue,
					uploadfoldername, currentimage, navigationcellid,
					navigationelementclassnameDESELECTED, navigationelementclassnameSELECTED,
					numberofnavigationlinksperrow,
					editableareas_idlist, editableareas_array, editableareas_idlist_transform2UL,
					selectableimages_idlist, selectableimages_array){
	// function to initialize data object	
	this.companyname = companyname;
	this.currentyear = currentyear;
	this.createdyear = createdyear;
	this.imagesnamelist = imagesnamelist;
	this.imagenameattributevalue = imagenameattributevalue;
	this.imageclassname = imageclassname;
	this.imagesortorder = imagesortorder;
	this.imagecellidattributevalue = imagecellidattributevalue;
	this.imagenotesidattributevalue = imagenotesidattributevalue;
	this.copyrightidattributevalue = copyrightidattributevalue;
	this.toolboxidattributevalue = toolboxidattributevalue;
	this.uploadfoldername = uploadfoldername;
	this.currentimage = currentimage;
	this.navigationcellid = navigationcellid;	
	this.navigationelementclassnameDESELECTED = navigationelementclassnameDESELECTED;
	this.navigationelementclassnameSELECTED = navigationelementclassnameSELECTED;
	this.numberofnavigationlinksperrow = numberofnavigationlinksperrow;
	this.editableareas_idlist = editableareas_idlist;
	this.editableareas_array = editableareas_array;
	this.editableareas_idlist_transform2UL = editableareas_idlist_transform2UL;
	this.selectableimages_idlist = selectableimages_idlist;
	this.selectableimages_array = selectableimages_array;
}

function imageobject(imagePath, bgcolor, notes, keywords, thumbnailimagePath){
	// function to initialize image object	
	this.imagePath = imagePath;
	this.bgcolor = bgcolor;
	this.notes = notes;
	this.keywords = keywords;
	this.thumbnailimagePath = thumbnailimagePath;
}

function editableareaobject(idattribute, content){
	// function to initialize editable area object
	this.idattribute = idattribute;
	this.content = content;
}

function selectableimageobject(idattribute, imagepath){
	// function to initialize selectable image object
	this.idattribute = idattribute;
	this.imagepath = imagepath;
}

function copyright(){
	if (dt.copyrightidattributevalue.length > 0){ // copyright id attribute available
		// create the copyright text with alexmedia link after line break
		var yrRng = dt.createdyear;
		if(yrRng != dt.currentyear && dt.currentyear != '') // year range string
			var yrRng = dt.createdyear + '-' + dt.currentyear;
		var strng = 'Copyright &copy; ' + yrRng + ' ' + dt.companyname + ' - All rights reserved.';
		// display copyright
		document.getElementById(dt.copyrightidattributevalue).innerHTML = strng;
	}
}

function memberlogout(){
	jsAjaxURL((scriptwebaddress + '?event=memberlogout' + '&uc=' + jsUC()), true);
	return;
}

function toolbox(){
	if (dt.toolboxidattributevalue.length > 0 && loginflag){ // toolbox id attribute available and logged in
		// create tool box to edit content
		var strng = '&nbsp;';
		// page information button
		var strng = strng + '<input type="button" value="Browser Fields" title="Browser Fields" onclick="javascript:toolbox_pageinformation();">';
		// editnotes button
		if (dt.imagenotesidattributevalue.length > 0) // image notes id attribute available
			var strng = strng + '<input type="button" value="Notes" title="Edit Notes" onclick="javascript:toolbox_editnotes();">';
		// background colors button
		if (dt.imagecellidattributevalue.length > 0) // image cell id attribute available
			var strng = strng + '<input type="button" value="Background" title="Edit Background" onclick="javascript:toolbox_editbackgroundcolor();">';
		if (dt.imagenameattributevalue.length > 0) // image cell name attribute available
			var strng = strng + '<input type="button" value="Keywords" title="Edit Keywords" onclick="javascript:toolbox_editkeywords();">';
		if (dt.uploadfoldername.length > 0) // upload folder name available
			var strng = strng + '<input type="button" value="Upload Images" title="Upload Images" onclick="javascript:toolbox_uploadimages();">';
		// page management button
		if (pagemanagementflag)
			var strng = strng + '<input type="button" value="Page Management" title="Page Management" onclick="javascript:toolbox_pagemanagement();">';
		// logout button
		var strng = strng + '<input type="button" value="Logout" title="Logout" onclick="javascript:jsAjaxURL(\'' + scriptwebaddress + '?event=logout' + '&uc=' + jsUC()+ '\');">';
		// display toolbox
		document.getElementById(dt.toolboxidattributevalue).innerHTML = strng;
	}
}

function toolbox_editnotes(){
	// display the 'editnotes' tool
	var strng = '<table border="0" cellspacing="0" cellpadding="0"><tr><td>' +
			    '<span style="font-family:Courier; font-size:9px; font-weight:bold;">Note:&nbsp;</span><input type="text" name="_editnotesfld" id="_editnotesfld" value="' +
			    '" size="20" maxlength="50" style="font:Courier;"><br>' +
			    '<input type="button" name="save" value=" Save" onclick="javascript:toolbox_editnotes_doit();">&nbsp;' + 
			    '<input type="button" name="cancel" value="Cancel" onclick="javascript:toolbox();">' +
			    '</td></tr></table>';
	document.getElementById(dt.toolboxidattributevalue).innerHTML = strng;
	if (dt.imagesnamelist.length >= dt.currentimage) // image information must exist	
		document.getElementById('_editnotesfld').value = dt.imagesnamelist[dt.currentimage].notes;
	document.getElementById('_editnotesfld').focus();
}

function toolbox_editnotes_doit(){
	// update notes
	dt.imagesnamelist[dt.currentimage].notes = document.getElementById('_editnotesfld').value;
	shownotes((dt.currentimage+1), dt.imagenotesidattributevalue);
	// commit changes to the database
	jsAjaxURL(scriptwebaddress + '?event=commitnotes&imageid=' + document.imagesarray[dt.currentimage].src + '&content=' + (dt.imagesnamelist[dt.currentimage].notes.replace('#', '%23')).replace('&', '%26') + '&uc=' + jsUC(), true); // post data
}

function toolbox_editbackgroundcolor(){
	// display the 'editbackgroundcolor' tool
	var strng = '';
	if (dt.imagesnamelist.length >= dt.currentimage) // image information must exist
		strng = dt.imagesnamelist[dt.currentimage].bgcolor;
	strng = '<table width="225" border="0" cellspacing="1" cellpadding="1"><tr height="15">' +
			'<td bgcolor="#892f1d" width="15" height="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#892f1d\');">&nbsp;</td>' +
			'<td bgcolor="#c7533b" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#c7533b\');">&nbsp;</td>' +
			'<td bgcolor="#efa599" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#efa599\');">&nbsp;</td>' +
			'<td bgcolor="#d0b7ad" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#d0b7ad\');">&nbsp;</td>' +
			'<td bgcolor="#a689a7" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#a689a7\');">&nbsp;</td>' +
			'<td bgcolor="#8aa067" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#8aa067\');">&nbsp;</td>' +
			'<td bgcolor="#5b9ebf" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#5b9ebf\');">&nbsp;</td>' +
			'<td bgcolor="#997869" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#997869\');">&nbsp;</td>' +
			'<td bgcolor="#a52a2a" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#a52a2a\');">&nbsp;</td>' +
			'<td bgcolor="#cde985" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#cde985\');">&nbsp;</td>' +
			'<td bgcolor="#613318" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#613318\');">&nbsp;</td>' +
			'<td bgcolor="#98baac" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#98baac\');">&nbsp;</td></tr><tr height="15">' +
			'<td bgcolor="#4e2029" width="15" height="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#4e2029\');">&nbsp;</td>' +
			'<td bgcolor="#b9d0dc" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#b9d0dc\');">&nbsp;</td>' +
			'<td bgcolor="#00365b" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#00365b\');">&nbsp;</td>' + 
			'<td bgcolor="#88adc3" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#88adc3\');">&nbsp;</td>' +
			'<td bgcolor="#887811" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#887811\');">&nbsp;</td>' +
			'<td bgcolor="#3d4242" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#3d4242\');">&nbsp;</td>' +
			'<td bgcolor="black" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'black\');">&nbsp;</td>' +
			'<td bgcolor="white" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'white\');">&nbsp;</td>' +
			'<td bgcolor="purple" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'purple\');">&nbsp;</td>' +
			'<td bgcolor="#e9db1b" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#e9db1b\');">&nbsp;</td>' +
			'<td bgcolor="#006600" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#006600\');">&nbsp;</td>' +
			'<td bgcolor="#ebab00" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#ebab00\');">&nbsp;</td></tr><tr height="15">' +
			'<td bgcolor="#243842" width="15" height="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#243842\');">&nbsp;</td>' +
			'<td bgcolor="#8b713c" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#8b713c\');">&nbsp;</td>' +
			'<td bgcolor="#382225" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#382225\');">&nbsp;</td>' + 
			'<td bgcolor="#891a1c" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#891a1c\');">&nbsp;</td>' +
			'<td bgcolor="#818646" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#818646\');">&nbsp;</td>' +
			'<td bgcolor="#838f97" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#838f97\');">&nbsp;</td>' +
			'<td bgcolor="#462e26" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#462e26\');">&nbsp;</td>' +
			'<td bgcolor="#fabfb7" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#fabfb7\');">&nbsp;</td>' +
			'<td bgcolor="#92785b" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#92785b\');">&nbsp;</td>' +
			'<td bgcolor="#ebd3d5" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#ebd3d5\');">&nbsp;</td>' +
			'<td bgcolor="#bcaf82" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#bcaf82\');">&nbsp;</td>' +
			'<td bgcolor="#d0c0c0" width="15" onclick="javascript:toolbox_editbackgroundcolor_doit(\'#d0c0c0\');">&nbsp;</td></tr><tr>' +
			'<td colspan="15"><input type="button" name="close" value="Close" onclick="javascript:toolbox();">&nbsp;</td>' + 
			'</tr></table>';
	document.getElementById(dt.toolboxidattributevalue).innerHTML = strng;
}

function toolbox_editbackgroundcolor_doit(c){ // c = color
	// update background color
	dt.imagesnamelist[dt.currentimage].bgcolor = c;	
	changeimagebgcolor((dt.currentimage+1), dt.imagecellidattributevalue);
	// commit changes to the database
	jsAjaxURL(scriptwebaddress + '?event=commitbackgroundcolor&imageid=' + document.imagesarray[dt.currentimage].src + '&content=' + dt.imagesnamelist[dt.currentimage].bgcolor.replace('#', '%23') + '&uc=' + jsUC(), true); // post data
}

function toolbox_editkeywords(){
	// display the 'editkeywords' tool
	var strng = '<table border="0" cellspacing="0" cellpadding="0"><tr><td>' +
			    '<span style="font-family:Courier; font-size:9px; font-weight:bold;">Keywords:&nbsp;</span><input type="text" name="_editkeywordsfld" id="_editkeywordsfld" value="' +
			    '" size="20" maxlength="200" style="font:Courier;"><br>' +
			    '<input type="button" name="save" value=" Save" onclick="javascript:toolbox_editkeywords_doit();">&nbsp;' + 
			    '<input type="button" name="cancel" value="Cancel" onclick="javascript:toolbox();">' +
			    '</td></tr></table>';
	document.getElementById(dt.toolboxidattributevalue).innerHTML = strng;
	if (dt.imagesnamelist.length >= dt.currentimage) // image information must exist	
		document.getElementById('_editkeywordsfld').value = dt.imagesnamelist[dt.currentimage].keywords;
	document.getElementById('_editkeywordsfld').focus();
}

function toolbox_editkeywords_doit(){
	// update keywords
	dt.imagesnamelist[dt.currentimage].keywords = document.getElementById('_editkeywordsfld').value;
	// commit changes to the database
	jsAjaxURL(scriptwebaddress + '?event=commitkeywords&imageid=' + document.imagesarray[dt.currentimage].src + '&content=' + (dt.imagesnamelist[dt.currentimage].keywords.replace('#', '%23')).replace('&', '%26') + '&uc=' + jsUC(), true); // post data
}

function toolbox_uploadimages(){
	// launch upload images tool
	window.location = scriptwebaddress + '?event=uploadimagestool' + dt.uploadfoldername + '&parentpagepath=' + window.location.pathname + '&uc=' + jsUC();
}

function toolbox_pagemanagement(){
	// launch page management tool
	window.location = scriptwebaddress + '?event=pagemanagement' + '&parentpagepath=' + window.location.pathname + '&uc=' + jsUC();
}

function toolbox_pageinformation(){
	// launch page information tool
	window.location = scriptwebaddress + '?event=pageinformationtool' + '&parentpagepath=' + window.location.pathname + '&uc=' + jsUC();
}

function showimage(n, m){ // n = image number, m = image name attribute value
	// show image in the document
	if (document.imagesarray != null && m.length > 0){
		if (document.imagesarray.length >= n){ // image must exist
			dt.currentimage = n-1;
			if (m.toLowerCase().indexOf('background:') == -1){ // image is shown via <img> tag
				if (navigator.userAgent.indexOf('Safari') != -1) // Safari workaround for proper resizing
					document[m].src = webprotocol + '//' + webaddress + librarylocation + 'clear.gif';
				document[m].src = document.imagesarray[dt.currentimage].src;
			}
			else // image is shown via background attribute
				document.getElementById(m.replace(/background:/i, '')).style.backgroundImage = 'url(' + document.imagesarray[dt.currentimage].src + ')';
		}
	}
	return;
}

function changeimagebgcolor(n, c){ // n = image number, c = image cell id attribute value
	// change the background color for the cell where image appears
	if (c.length > 0) // image cell id attribute available	
		if (dt.imagesnamelist.length >= n) // image information must exist
			document.getElementById(c).bgColor = dt.imagesnamelist[n-1].bgcolor;
	return;
}

function shownotes(n, i){ // n = image number, i = image notes id attribute value
    // show image notes
	if (i.length > 0){ // image notes id attribute available	
		if (dt.imagesnamelist.length >= n) // image information must exist
			document.getElementById(i).innerHTML = dt.imagesnamelist[n-1].notes;
		else	
			document.getElementById(i).innerHTML = '';
	} //if
	return;
}

function togglenavigationelementclassname(ln){ // ln = link number
	// set the element 'className' attribute
	if (document.getElementById(dt.navigationcellid + dt.currentimage) != null)
		document.getElementById(dt.navigationcellid + dt.currentimage).className = dt.navigationelementclassnameDESELECTED;
	if (document.getElementById(dt.navigationcellid + dt.currentimage + '_link') != null)
		document.getElementById(dt.navigationcellid + dt.currentimage + '_link').className = dt.navigationelementclassnameDESELECTED;
	if (document.getElementById(dt.navigationcellid + ln) != null)		
		document.getElementById(dt.navigationcellid + ln).className = dt.navigationelementclassnameSELECTED;
	if (document.getElementById(dt.navigationcellid + ln + '_link') != null)		
		document.getElementById(dt.navigationcellid + ln + '_link').className = dt.navigationelementclassnameSELECTED;	
}

function navigation(n, m){ // n = NUMBERS or THUMBNAILS | method = onclick, onmouseover, href, etc.
	// show the navigation bar for images using a hyperlink for each image
	var strng = '';
	// create html string to display navigation links	
	if (dt.imagesnamelist.length >= 1){ // there are images to be shown
		var cnt = 0;
		for(k=0; k<dt.imagesnamelist.length; k++){
			if (dt.numberofnavigationlinksperrow >= 1){ // start new row when needed
				cnt = cnt+1;
				if (cnt == 1 && k != 0)
					var strng = strng + '</tr><tr>';
				if (cnt == dt.numberofnavigationlinksperrow)
					cnt = 0;
			}
			var clss = dt.navigationelementclassnameDESELECTED;
			if (k == 0)
				var clss = dt.navigationelementclassnameSELECTED;
			var prestrng = '<td class="' + clss + '" id="' + dt.navigationcellid + k + '">';
			var posstrng = '</td>';
			var lnkstrng = (k+1);			
			if (n == 'THUMBNAILS')
				var lnkstrng = '<img src="' + 'http://' + webaddress +
							   dt.imagesnamelist[k].thumbnailimagePath + '" border="0">';
			var strng = strng + prestrng + 
						'<a class="' + clss + '" ' +
					    'id="' + dt.navigationcellid + k + '_link" ' +
						m + '="javascript:togglenavigationelementclassname(' + k +
					    ');showimage(' + (k+1) + ',\'' + dt.imagenameattributevalue + '\');' +
						'changeimagebgcolor(' + (k+1) + ',\'' + dt.imagecellidattributevalue + '\');' +
						'shownotes(' + (k+1) + ',\'' + dt.imagenotesidattributevalue + '\');toolbox();">' + lnkstrng + '</a>' + 
						posstrng;
		} // for
	} // if	
	document.write('<table border="0">' + strng + '</tr></table>');
}

function navigationbynumbers(){
	navigation('NUMBERS', 'href');
}

function navigationbythumbnailsONHOVER(){
	if (loginflag)
		navigationbythumbnailsONCLICK();
	else	
		navigation('THUMBNAILS', 'onmouseover');
}

function navigationbythumbnailsONCLICK(){
	navigation('THUMBNAILS', 'href');
}

function rotateimages(){
	if (dt.imagesnamelist.length >= 1){ // there are images to be shown
		if ((dt.currentimage+1) < dt.imagesnamelist.length){
			var tmpcurrentimage = dt.currentimage+2;
			showimage(tmpcurrentimage, dt.imagenameattributevalue);
			shownotes(tmpcurrentimage, dt.imagenotesidattributevalue);
		}
		else{ // cycle around
			showimage(1, dt.imagenameattributevalue);
			shownotes(1, dt.imagenotesidattributevalue);
		}
	}
}

function preloadimages(){
  // preload images onto an array under the document object
  mgsrry = new Array();
  if (dt.imagesnamelist.length >= 1){ // there are images to be preloaded
  	for(k=0; k<dt.imagesnamelist.length; k++){
		mgsrry[k] = new Image;
		mgsrry[k].src = 'http://' + webaddress + dt.imagesnamelist[k].imagePath;
	} // for
  } // if
  if (document.images && mgsrry.length > 0){
  	document.imagesarray = mgsrry;
	return true;
  }	
  return false;
}

function editablearea_show(i){
	var cntntstrng = editablearea_transform(i);
	if (loginflag)
		cntntstrng = cntntstrng + '<div><input type="button" value="Edit" title="Edit Content" onclick="javascript:editablearea_edit(\'' + i + '\');"></div>';
	if (document.getElementById(dt.editableareas_array[i].idattribute).innerHTML != cntntstrng)
		document.getElementById(dt.editableareas_array[i].idattribute).innerHTML = cntntstrng;
	return;
}

function editablearea_transform(i){
	var lstfrtrnsfrmrry = editableareas_idlist_transform2UL.split(',');
	var cntntstrngtrnsfrm = '';
	var cntntstrngtrnsfrmrry = dt.editableareas_array[i].content.split('<br>');
	for(j=0; j<lstfrtrnsfrmrry.length; j++)
		if (lstfrtrnsfrmrry[j] == dt.editableareas_array[i].idattribute) // show content as UL list. ONLY when needed
			for (m=0; m<cntntstrngtrnsfrmrry.length; m++)
				if (jsTrim(cntntstrngtrnsfrmrry[m]) != '')
					cntntstrngtrnsfrm = cntntstrngtrnsfrm + '<li>' + cntntstrngtrnsfrmrry[m] + '</li>';
	if (jsTrim(cntntstrngtrnsfrm) != '')
		cntntstrngtrnsfrm = '<ul>' + cntntstrngtrnsfrm + '</ul>';
	else
		var cntntstrngtrnsfrm = dt.editableareas_array[i].content;	
	return cntntstrngtrnsfrm;
}

function editablearea_edit(i){
	// display the 'editablearea' tool
	var strng = '<table border="0" cellspacing="0" cellpadding="0" width="96%"><tr><td height="100%">' +
				'<textarea name="_editableareafld_' + i + '" id="_editableareafld_' + i + '">' + 
				dt.editableareas_array[i].content.replace(/<br>/gi, '\n') + '</textarea><br>' +
				'<input type="button" name="save" value=" Save" onclick="javascript:editablearea_edit_doit(\'' + i + '\');">&nbsp;' + 
				'<input type="button" name="cancel" value="Cancel" onclick="javascript:editablearea_show(\'' + i + '\');linktransformations(document.body);">' +
				'</td></tr></table>'
	document.getElementById(dt.editableareas_array[i].idattribute).innerHTML = strng;
}

function editablearea_edit_doit(i){
	// update the editable area
	dt.editableareas_array[i].content = document.getElementById('_editableareafld_' + i).value.replace(/\n/gi, '<br>');
	// update _editableareaform form
	document.forms['_editableareaform'].elements[0].value = dt.editableareas_array[i].idattribute; 
	document.forms['_editableareaform'].elements[1].value = document.getElementById('_editableareafld_' + i).value; 
	editablearea_show(i);
	linktransformations(document.body);
	jsAjaxFORM(scriptwebaddress + '?event=commiteditablearea' + '&uc=' + jsUC(), '_editableareaform', true) // post data
}

function showalleditableareas(){
  if (dt.editableareas_array.length >= 1){ // there are editable areas
  	for(k=0; k<dt.editableareas_array.length; k++)
		editablearea_show(k);
	// create a form to process editable area form submissions		
	if (loginflag){
		var dtblrfrm = document.createElement('form');
		dtblrfrm.id = '_editableareaform';
		dtblrfrm.name = '_editableareaform';
		dtblrfrm.method = 'POST';
		document.body.appendChild(dtblrfrm);
		var dtblrfrmcntntfld = document.createElement('input');
		dtblrfrmcntntfld.type = 'hidden';
		dtblrfrmcntntfld.name = 'contentid';
		dtblrfrmcntntfld.value = '';
		dtblrfrm.appendChild(dtblrfrmcntntfld);
		var dtblrfrmcntntfld = document.createElement('input');
		dtblrfrmcntntfld.type = 'hidden';
		dtblrfrmcntntfld.name = 'content';
		dtblrfrmcntntfld.value = '';
		dtblrfrm.appendChild(dtblrfrmcntntfld);
	} // if
  } // if
  return;
}

function selectableimage_show(i){
	var cntntstrng = '';
	if (jsTrim(dt.selectableimages_array[i].imagepath) != '')
		cntntstrng = '<img src="' + 'http://' + webaddress + dt.selectableimages_array[i].imagepath + '" border="0">';
	if (loginflag){
		cntntstrng = cntntstrng + '<div><select name="_selectimagefld_' + i + '" id="_selectimagefld_' + i +
		             '" style="width:100%" onchange="javascript:selectableimage_edit_doit(\'' + i + '\');">' +
				     '<option value="">no image</option>';
		if (dt.imagesnamelist.length >= 1) // there are images
			for(n=0; n<dt.imagesnamelist.length; n++){
				cntntstrng = cntntstrng + '<option value="' + dt.imagesnamelist[n].imagePath + '"';
				if (dt.imagesnamelist[n].imagePath == dt.selectableimages_array[i].imagepath) // selected image
					cntntstrng = cntntstrng + ' selected';
				cntntstrng = cntntstrng + '>' + jsGetToken(dt.imagesnamelist[n].imagePath, 'last', '/', '') + '</option>';
			} // for
		cntntstrng = cntntstrng + '</select></div>';
	} // if
	if (document.getElementById(dt.selectableimages_array[i].idattribute).innerHTML != cntntstrng)
		document.getElementById(dt.selectableimages_array[i].idattribute).innerHTML = cntntstrng;
	return;
}

function selectableimage_edit_doit(i){
	// update the selectable image
	var cntntstrng = document.getElementById('_selectimagefld_' + i).options[document.getElementById('_selectimagefld_' + i).selectedIndex].value;
	dt.selectableimages_array[i].imagepath = cntntstrng;
	// update _selectableimageform form
	document.forms['_selectableimageform'].elements[0].value = dt.selectableimages_array[i].idattribute; 
	document.forms['_selectableimageform'].elements[1].value = cntntstrng;
	selectableimage_show(i);
	jsAjaxFORM(scriptwebaddress + '?event=commitselectableimage' + '&uc=' + jsUC(), '_selectableimageform', true) // post data
}

function showallselectableimages(){
  if (dt.selectableimages_array.length >= 1){ // there are selectable images
  	for(k=0; k<dt.selectableimages_array.length; k++)
		selectableimage_show(k);
	// create a form to process selectable images form submissions
	if (loginflag){
		var dtblrfrm = document.createElement('form');
		dtblrfrm.id = '_selectableimageform';
		dtblrfrm.name = '_selectableimageform';
		dtblrfrm.method = 'POST';
		document.body.appendChild(dtblrfrm);
		var dtblrfrmcntntfld = document.createElement('input');
		dtblrfrmcntntfld.type = 'hidden';
		dtblrfrmcntntfld.name = 'imageid';
		dtblrfrmcntntfld.value = '';
		dtblrfrm.appendChild(dtblrfrmcntntfld);
		var dtblrfrmcntntfld = document.createElement('input');
		dtblrfrmcntntfld.type = 'hidden';
		dtblrfrmcntntfld.name = 'imagepath';
		dtblrfrmcntntfld.value = '';
		dtblrfrm.appendChild(dtblrfrmcntntfld);
		var dtblrfrmcntntfld = document.createElement('input');
		dtblrfrmcntntfld.type = 'hidden';
		dtblrfrmcntntfld.name = 'foldername';
		dtblrfrmcntntfld.value = jsGetToken(dt.uploadfoldername, 'last', '=', '');
		dtblrfrm.appendChild(dtblrfrmcntntfld);
	} // if
  } // if
  return;
}

// match email addresses not within tag attributes
var re = /[\w\.\-]+\@([\w\-]+\.)+[\w]{2,4}(?![^<]*>)/ig;
function linkemails(parentNode){
	var nodes = parentNode.childNodes;
	for (var i=0; i < nodes.length; i++){
		if (nodes[i].nodeType == 1 && nodes[i].tagName != "A") {
			linkemails(nodes[i]);
		} else if (nodes[i].nodeType == 3 && re.test(nodes[i].nodeValue)){
			parentNode.innerHTML = parentNode.innerHTML.replace(re,"<a href='mailto:$&'>$&</a>");
		}
	} // for
}

// match url's not within tag attributes
var rl = /https?:\/\/([-\w\.]+)+(:\d+)?(\/([\w\_\.]*(\?\S+)?)?)?/ig;
function linkurls(parentNode){
	var nodes = parentNode.childNodes;
	for (var i=0; i < nodes.length; i++){
		if (nodes[i].nodeType == 1 && nodes[i].tagName != "A") {
			linkurls(nodes[i]);
		} else if (nodes[i].nodeType == 3 && rl.test(nodes[i].nodeValue)){
			parentNode.innerHTML = parentNode.innerHTML.replace(rl,"<a href='$&'>$&</a>");
		}
	} // for
}

function linktransformations(parentNode){
	if (!parentNode) // object is undefined, default to entire document
		parentNode = document.body;
	linkemails(parentNode);
	linkurls(parentNode);
}

function setimageclassname(){
	if (jsTrim(dt.imagenameattributevalue) != '' && jsTrim(dt.imageclassname) != '') // set image style(s) when available
		document[dt.imagenameattributevalue].className = dt.imageclassname;
}

function startup(){
    // start the system
	if (preloadimages()){
	    showimage(1, dt.imagenameattributevalue);
		changeimagebgcolor(1, dt.imagecellidattributevalue);
		shownotes(1, dt.imagenotesidattributevalue);
	}
	showalleditableareas();
	linktransformations(document.body);
	showallselectableimages();
	if (!loginflag && jsIsNumber(imagerotateinterval))	
		setInterval("rotateimages()", imagerotateinterval);
	copyright();
	toolbox();
	setTimeout('setimageclassname()', 700);
	if (ctcd('search', ''))
		jsAjaxURL(scriptwebaddress + '?event=ctcd' + '&uc=' + jsUC(), true); // Get data
}

// Variable definitions
var loginflag = false;
var pagemanagementflag = false;

// Init	
uploadfoldername = jsInitIfUndefined('uploadfoldername', '');
imagenameattributevalue = jsInitIfUndefined('imagenameattributevalue', '');
imageclassname = jsInitIfUndefined('imageclassname', '');
imagesortorder = jsInitIfUndefined('imagesortorder', '');
imagecellidattributevalue = jsInitIfUndefined('imagecellidattributevalue', '');
imagenotesidattributevalue = jsInitIfUndefined('imagenotesidattributevalue', '');
copyrightidattributevalue = jsInitIfUndefined('copyrightidattributevalue', '');
toolboxidattributevalue = jsInitIfUndefined('toolboxidattributevalue', '');
navigationelementclassnameDESELECTED = jsInitIfUndefined('navigationelementclassnameDESELECTED', '');
navigationelementclassnameSELECTED = jsInitIfUndefined('navigationelementclassnameSELECTED', '');
numberofnavigationlinksperrow = jsInitIfUndefined('numberofnavigationlinksperrow', '');
editableareas_idlist = jsInitIfUndefined('editableareas_idlist', '');
editableareas_idlist_transform2UL = jsInitIfUndefined('editableareas_idlist_transform2UL', '');
selectableimages_idlist = jsInitIfUndefined('selectableimages_idlist', '');
indexrootfolder = jsInitIfUndefined('indexrootfolder', '');
imagerotateinterval = jsInitIfUndefined('imagerotateinterval', '');
dt = new dataobject('', '', '', new Array(),
					imagenameattributevalue, imageclassname, imagesortorder, imagecellidattributevalue, imagenotesidattributevalue,
					copyrightidattributevalue, toolboxidattributevalue, uploadfoldername, -1, 'photocell',
					navigationelementclassnameDESELECTED, navigationelementclassnameSELECTED, numberofnavigationlinksperrow,
					editableareas_idlist, new Array(), editableareas_idlist_transform2UL, selectableimages_idlist, new Array());
if (dt.uploadfoldername.length > 0) // fetch images only if a folder is specified
	dt.uploadfoldername = '&uploadfoldername=' + dt.uploadfoldername;
if (dt.editableareas_idlist.length > 0) // when editable areas are specified
	dt.editableareas_idlist = '&editableareas_idlist=' + dt.editableareas_idlist;	
if (dt.selectableimages_idlist.length > 0) // when selectable images are specified
	dt.selectableimages_idlist = '&selectableimages_idlist=' + dt.selectableimages_idlist;	
if (dt.imagesortorder.length > 0) // when the images sort order is specified
	dt.imagesortorder = '&imagesortorder=' + dt.imagesortorder;
var servertype = 'php';
var librarylocation = 'admin/';
var webprotocol = window.location.protocol;
var webaddress = window.location.host + '/' + indexrootfolder;
var scriptwebaddress = webprotocol + '//' + webaddress + librarylocation + 'alexjet.' + servertype; // webaddress, serverype & librarylocation are required.

if (!jsInitIfUndefined('bypassinitflag', false)){ // bypassinitflag = true to bypass initialization
	// Fetch initialization data from server (currentyear & imagesnamelist (if needed))
	jsAjaxURL(scriptwebaddress + '?event=initializedata' + dt.uploadfoldername + dt.editableareas_idlist + dt.selectableimages_idlist + dt.imagesortorder + '&uc=' + jsUC(), true); // Get data
}