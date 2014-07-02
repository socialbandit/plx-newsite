// 09.06.2011_rg

$(window).load(function(){
	var	parentNode = document.body;
	jlocal_linkemails(parentNode);	
	jlocal_linkurls(parentNode);
});

// Library functions

var re = /[\w\.\-]+\@([\w\-]+\.)+[\w]{2,4}(?![^<]*>)/ig;
function jlocal_linkemails(parentNode){
	var nodes = parentNode.childNodes;
	for (var i=0; i < nodes.length; i++){
		if (nodes[i].nodeType == 1 && nodes[i].tagName != "A") {
			jlocal_linkemails(nodes[i]);
		} else if (nodes[i].nodeType == 3 && re.test(nodes[i].nodeValue)){
			parentNode.innerHTML = parentNode.innerHTML.replace(re,"<a href='mailto:$&'>$&</a>");
		}
	}
}

var rl = /https?:\/\/([-\w\.]+)+(:\d+)?(\/([\w\_\.]*(\?\S+)?)?)?/ig;
function jlocal_linkurls(parentNode){
	var nodes = parentNode.childNodes;
	for (var i=0; i < nodes.length; i++){
		if (nodes[i].nodeType == 1 && nodes[i].tagName != "A") {
			jlocal_linkurls(nodes[i]);
		} else if (nodes[i].nodeType == 3 && rl.test(nodes[i].nodeValue)){
			parentNode.innerHTML = parentNode.innerHTML.replace(rl,"<a href='$&'>$&</a>");
		}
	}
}

local_uc = Date.parse(new Date()) + '_' + Math.round(100*Math.random()) + '_';
var local_ucIncrement = 0;
function jlocal_UC(){
	++local_ucIncrement;
	return (local_uc + (local_ucIncrement+''));
}

function jlocal_trim(jVl){
	return jVl.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, "");
}

function jlocal_set(jLmntID, jVl){
	// Set the 'innerHTML' of an element
	if (document.all)
		document.all[jLmntID].innerHTML = jVl;
	else
		document.getElementById(jLmntID).innerHTML = jVl;
}

function jlocal_setValue(jLmntID, jVl){
	// Set the 'value' of an element
	if (document.all)
		document.all[jLmntID].value = jVl;
	else
		document.getElementById(jLmntID).value = jVl;
}

function jlocal_getElement(jLmntID){
	if (document.all)
		return document.all[jLmntID];
	else 
		return document.getElementById(jLmntID);
}

function jlocal_getElementValue(jLmntID){
	if (document.all)
		return document.all[jLmntID].value;
	else
		return document.getElementById(jLmntID).value;
}

function jlocal_isEmpty(jStrng){
	var jStrng2 = jlocal_trim(jStrng);
	return ((jStrng2 == null) || (jStrng2.length == 0));
}

var local_reEmail = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i;
function jlocal_isEmail(jMls){
	return local_reEmail.test(jMls);
}

var reFloat = /^-?((\d+(\.\d*)?)|((\d*\.)?\d+))$/;
function jlocal_isNumber(jNmbr) {
	return reFloat.test(jNmbr);
}	

function jlocal_isInteger(jNmbr, jSgndNtgr) {
	if (jSgndNtgr)
		return (jNmbr.toString().search(/^-?[0-9]+$/) == 0); // allows negative sign
	else
		return (jNmbr.toString().search(/^[0-9]+$/) == 0); // does not allow negative sign
}

function jlocal_getToken(jVl, jTknPs, jDlmtr, jDfltVl){
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
}

function jlocal_SetStyleDisplay(jLmntID, jVl){
	if (document.all)
		document.all[jLmntID].style.display = jVl;
	else
	   document.getElementById(jLmntID).style.display = jVl;
}

// Custom functions 

function changeBackgroundColor(bkcolor){
	$.ajax({type:'POST',
		    url:'/php/changebackgroundcolorinformation.php?uc=' + jlocal_UC(), data:('bkgclr=' + bkcolor), async:true, dataType:'script',
		    success:function(){changeBackgroundColor_doit(bkcolor);}
	});
	return;
}

function changeBackgroundColor_doit(bkcolor){
	var jImageName = ('url(/images/bkg-all-' + bkcolor + '-rept.jpg' + ')');
	$('body').css('background-image', jImageName);  
	return;	
}

function validateSendFiles(){
	var jRrrMssg = '';
	if (jlocal_isEmpty(document.forms[0]['Name'].value))
		jRrrMssg = jRrrMssg + 'Name is required\n\n';
	if (jlocal_isEmpty(document.forms[0]['Email'].value))
		jRrrMssg = jRrrMssg + 'Email is required\n\n';
	if (!jlocal_isEmpty(document.forms[0]['Email'].value) &&
		!jlocal_isEmail(document.forms[0]['Email'].value))
		jRrrMssg = jRrrMssg + 'Email must be valid\n\n';
	if (jlocal_isEmpty(document.forms[0]['imagefile1'].value) && jlocal_isEmpty(document.forms[0]['imagefile2'].value) && jlocal_isEmpty(document.forms[0]['imagefile3'].value))
		jRrrMssg = jRrrMssg + 'Select at least one file to upload\n\n';
	if (jRrrMssg != ''){
		alert(jRrrMssg);
		return false;
	}
	jlocal_SetStyleDisplay('_spinner', 'block');
	return true;
}