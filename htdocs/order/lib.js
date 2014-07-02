// 04.23.2009_rg, 06.23.2009_rg | servicemultiplier, 09.06.2011_rg, 02.18.2012_rg | added jSetCtCode(), 05.09.2012_rg price calculation corrections in totalItems()

function validateRequest()
{
	/*
	* Contact
	* StoreNum
	* Phone
	* Email
	* BillingAddress1
	BillingAddress2
	* BillingCity
	* BillingState
	* BillingZip
	
	* UseShipAddress
	
	* ShippingAddress1
	* ShippingAddress2
	* ShippingCity
	* ShippingState
	* ShippingZip
	*/
	
	var jRrrMssg = '';
	if (jsIsEmpty(document.forms[0]['Contact'].value))
	jRrrMssg = jRrrMssg + 'Contact is required\n\n';
	if (jsIsEmpty(document.forms[0]['StoreNum'].value))
	jRrrMssg = jRrrMssg + 'Store # is required\n\n';
	if (jsIsEmpty(document.forms[0]['Phone'].value))
	jRrrMssg = jRrrMssg + 'Phone is required\n\n';
	if (jsIsEmpty(document.forms[0]['Email'].value))
	jRrrMssg = jRrrMssg + 'Email is required\n\n';
	if (!jsIsEmail(document.forms[0]['Email'].value))
	jRrrMssg = jRrrMssg + 'Invalid Email\n\n';
	if (jsIsEmpty(document.forms[0]['BillingAddress1'].value))
	jRrrMssg = jRrrMssg + 'Billing Address 1 is required\n\n';
	if (jsIsEmpty(document.forms[0]['BillingCity'].value))
	jRrrMssg = jRrrMssg + 'Billing City is required\n\n';
	if (jsIsEmpty(document.forms[0]['BillingState'].value))
	jRrrMssg = jRrrMssg + 'Billing State is required\n\n';
	if (jsIsEmpty(document.forms[0]['BillingZip'].value))
	jRrrMssg = jRrrMssg + 'Billing Zip is required\n\n';
	
	if (document.all)
	{
		var objRef = document.all['UseShipAddress'].checked;
	}
	else
	{
		var objRef = document.getElementById('UseShipAddress').checked;
	}
	if(objRef != "0")
	{
		if (jsIsEmpty(document.forms[0]['ShippingAddress1'].value))
		jRrrMssg = jRrrMssg + 'Shipping Address 1 is required\n\n';
		if (jsIsEmpty(document.forms[0]['ShippingCity'].value))
		jRrrMssg = jRrrMssg + 'Shipping City is required\n\n';
		if (jsIsEmpty(document.forms[0]['ShippingState'].value))
		jRrrMssg = jRrrMssg + 'Shipping State is required\n\n';
		if (jsIsEmpty(document.forms[0]['ShippingZip'].value))
		jRrrMssg = jRrrMssg + 'Shipping Zip is required\n\n';
	}
	
	var returnitemdata = '';
	var sizeindex = 0;
	var materialindex = 0;
	var qntyval = 0;
	var itemval = '';
	document.forms[0]['itemdata'].value='';
	var orderItemElements = document.getElementById("orderCanvas");
	var orderTagElements = orderItemElements.getElementsByTagName('table');
	var itemCount = orderTagElements.length/3;
	var counter = 0;
	
	for (counter=0;counter<itemCount;counter++)
	{
		if(orderTagElements[counter])
		{	
			var parentrowname = orderTagElements[counter*3].id;
			var itemData = new Object();
			itemData.material = document.getElementById('material'+parentrowname);
			itemData.width = document.getElementById('width'+parentrowname);
			itemData.height = document.getElementById('height'+parentrowname);
			itemData.twoSided = document.getElementById('twoSided'+parentrowname);
			itemData.quantity = document.getElementById('quantity'+parentrowname);
			//itemData.sewing = document.getElementById('sewing'+parentrowname);
			itemData.grommetsCount = document.getElementById('grommets'+parentrowname);
			itemData.pocketsTop = document.getElementById('pocketsTop'+parentrowname);
			itemData.pocketsBottom = document.getElementById('pocketsBottom'+parentrowname);
			itemData.poleDiameter = document.getElementById('poleDiameter'+parentrowname);
			itemData.service = document.getElementById('service'+parentrowname);
			itemData.itemtotal = document.getElementById('itemtotal'+parentrowname);
	
			sizeindex=0;
			materialindex=0;
			if(counter!=0){returnitemdata += '|';}
			var ordercolobj = orderTagElements[counter];
			//material
			returnitemdata += itemData.material.value + '~';
			//width
			returnitemdata += itemData.width.value + '~';
			//height
			returnitemdata += itemData.height.value + '~';
			//twoSided
			returnitemdata += itemData.twoSided.checked + '~';
			//quantity
			returnitemdata += itemData.quantity.value + '~';
			//grommet
			returnitemdata += itemData.grommetsCount.value + '~';
			//pocketsTop
			returnitemdata += itemData.pocketsTop.checked + '~';
			//pocketsBottom
			returnitemdata += itemData.pocketsBottom.checked + '~';
			//poleDiameter
			returnitemdata += itemData.poleDiameter.value + '~';
			//service
			returnitemdata += itemData.service.value + '~';
			//itemTotal
			returnitemdata += itemData.itemtotal.innerHTML;
			
			if(itemData.material.selectedIndex==0||
				itemData.width.value==''||
				itemData.height.value==''||
				itemData.quantity.value=='')
			{
				if (itemData.material.selectedIndex==0)
				jRrrMssg = jRrrMssg + 'Material for [Item '+(counter+1)+'] is not selected\n\n';
				if (jsIsNumber(itemData.width.value)||itemData.width.value==0)
				jRrrMssg = jRrrMssg + 'Width for [Item '+(counter+1)+'] must be greater than zero\n\n';
				if (jsIsNumber(itemData.height.value)||itemData.height.value==0)
				jRrrMssg = jRrrMssg + 'Height for [Item '+(counter+1)+'] must be greater than zero\n\n';
				if (jsIsNumber(itemData.quantity.value)||itemData.quantity.value==0)
				jRrrMssg = jRrrMssg + 'Quantity for [Item '+(counter+1)+'] must be greater than zero\n\n';
			}
			
			if(itemData.pocketsTop.checked===true||itemData.pocketsBottom.checked===true)
			{
				if (itemData.poleDiameter.selectedIndex==0)
				jRrrMssg = jRrrMssg + 'Pole Diameter for [Item '+counter+'] is not selected\n\n';
			}
		}
	}
	document.forms[0]['itemdata'].value=returnitemdata;
	document.forms[0]['odertotal'].value=document.getElementById('total').innerHTML;
	
	var orderItemElements = document.getElementById("orderCanvas");
	var orderTagElements = orderItemElements.getElementsByTagName('table');
	var itemCount = orderTagElements.length/3;
	if (itemCount == 0)
	{
		jRrrMssg = jRrrMssg + 'You must add at least one item to place an order\n\n';
	}
	
	if (jRrrMssg != '')
	{
		alert(jRrrMssg);
	}
	else
	{
		var jNswr = confirm('Please click [OK] to confirm and send order.');
		if(jNswr){
			document.OrderForm.submit();
			return true;
		}
	}
}

var materials = '';
var materialsArray = new Array();
function getMaterials()
{
	materials = '';
	var found = false;
	for (var i in labworks2jsBgDtObjct)
	{
		for (var k in materialsArray)
		{
			if (materialsArray[k]==labworks2jsBgDtObjct[i].mt)
			{
				found = true;
			}
			else
			{
				found = false;
			}
		}
		materialsArray[i]=labworks2jsBgDtObjct[i].mt;
		if(found==false)
		{
			materials += '<option value="' + labworks2jsBgDtObjct[i].mt + '">' + labworks2jsBgDtObjct[i].mt + '</option>';
		}
	} // for
}

function add2Order()
{
	var rowname =  'orderTable_'+jsUC();
	if(jsObjectRecordCount(labworks2jsBgDtObjct) > 0)
	{
		var html = '';
		html += '			<table width="100%" border="0px">'+
			'				<tr>'+
			'					<td align="left" valign="middle"  colspan="3"><span class="p2_copy"><strong>Item</strong>&ensp;<em>Material</em></span>&ensp;<select class="fieldyellow" name="material'+rowname+'" id="material'+rowname+'" onChange="totalItems(\''+rowname+'\')"><option value="">Choose Material</option>'+materials+'</select>&ensp;&ensp;<span class="p2_copy"><em>Width</em></span> <input class="fieldyellow" type="text" id="width'+rowname+'" name="width'+rowname+'" size="4" maxlength="4" onKeyup="totalItems(\''+rowname+'\')"/> <span class="p2_copy"><em>in.</em></span>&ensp;<span class="p2_copy"><em>Height</em></span> <input class="fieldyellow" type="text" id="height'+rowname+'" name="height'+rowname+'" size="4" maxlength="4" onKeyup="totalItems(\''+rowname+'\')"/> <span class="p2_copy"><em>in.</em>&ensp;&ensp;<input type="hidden" name="twoSided'+rowname+'" id="twoSided'+rowname+'" value="" /><span class="p2_copy"><em>Quantity</em></span>&ensp;<input class="fieldyellow" type="text" id="quantity'+rowname+'" name="quantity'+rowname+'" size="4" maxlength="4" onKeyup="totalItems(\''+rowname+'\')"/></td>'+
			'				</tr>'+
			'				<tr>'+
			'					<td align="left" valign="middle" colspan="3"><span class="p2_copy"><strong>Finishing Options</strong></span></td>'+
			'				</tr>'+
			'				<tr>'+
			'					<td colspan="3">'+
			'					<table width="100%" cellspacing="1" cellpadding="1" border="0px">'+
			'						<tbody>'+
			//'							<tr>'+
			//'								<td align="left" valign="middle" width="160px"><img src="/images/clear.gif" alt="Big Banners Plus : Atlanta" width="20px" height="4px" border="0" /><span class="p2_copy"><em>Sewing?</em> (<span id="sewingprice_display" name="sewingprice_display">$0.50</span>/linear ft.)</span></td>'+
			//'								<td align="left" valign="middle"><span class="p2_copy"><input type="radio" value="yes" name="sewing'+rowname+'" id="sewing'+rowname+'" onChange="totalItems(\''+rowname+'\')"/><label for="Sewing_0">Yes</label><input type="radio" checked="checked" value="no" name="sewing'+rowname+'" id="sewing'+rowname+'" onChange="totalItems(\''+rowname+'\')"/><label for="Sewing_1">No</label></span></td>'+
			//'							</tr>'+
			'							<tr>'+
			'								<td align="left" valign="middle" width="160px"><img src="/images/clear.gif" alt="Parallax Digital : Atlanta" width="20px" height="4px" border="0" /><span class="p2_copy"><em>Grommets?</em> (<span id="grommetsprice_display'+rowname+'" name="grommetsprice_display'+rowname+'">$0.00</span>/each)</span></td>'+
			'								<td align="left" valign="middle"><input class="fieldyellow" type="text" id="grommets'+rowname+'" name="grommets'+rowname+'" size="5" maxlength="5" onKeyup="totalItems(\''+rowname+'\')"/></td>'+
			'							</tr>'+
			'							<tr>'+
			'								<td align="left" valign="middle" width="160px"><img src="/images/clear.gif" alt="Parallax Digital : Atlanta" width="20px" height="4px" border="0" /><span class="p2_copy"><em>Pole Pockets?</em> (<span id="polepocketsprice_display'+rowname+'" name="polepocketsprice_display'+rowname+'">$0.00</span>/each)</span></td>'+
			'								<td align="left" valign="middle" ><input type="checkbox" name="pocketsTop'+rowname+'" id="pocketsTop'+rowname+'" value="yes" onClick="totalItems(\''+rowname+'\')"/><span class="p2_copy">Top</span>&ensp;<input type="checkbox" name="pocketsBottom'+rowname+'" id="pocketsBottom'+rowname+'" value="yes" onClick="totalItems(\''+rowname+'\')"/><span class="p2_copy">Bottom</span>&ensp;<select class="fieldyellow" name="poleDiameter'+rowname+'" id="poleDiameter'+rowname+'" onChange="totalItems(\''+rowname+'\')"><option value="">Pole Diameter</option><option value="0.5 Inch">0.5 Inch</option><option value="1 Inch">1 Inch</option><option value="1.5 Inch">1.5 Inch</option><option value="2 Inch">2 Inch</option></select></td>'+
			'							</tr>'+
			'						</tbody>'+
			'					</table>'+
			'					</td>'+
			'				</tr>'+
			'				<tr>'+
			'					<td valign="middle" width="250px" style="text-align: left;" colspan="3"><span class="p2_copy"><strong>Service</strong>&ensp;<select class="fieldyellow" name="service'+rowname+'" id="service'+rowname+'" onChange="totalItems(\''+rowname+'\')"><option value="5 Days">5 Days (Normal Service)</option><option value="4 Days">4 Days</option><option value="3 Days">3 Days</option><option value="2 Days">2 Days</option><option value="1 Day">1 Day</option></select></span></td>'+
			'				</tr>'+
			'				<tr>'+
			'					<td valign="middle" width="250px" style="text-align: left;"></td>'+
			'					<td valign="middle" width="100px" style="text-align: right;"><span class="p2_copy"><strong><a onClick="handle_optionremove(\''+rowname+'\')">remove item</a></strong></span></td>'+
			'					<td valign="middle" style="text-align: right;"><span class="p2_copy"><strong>Item Total</strong></span>&ensp;<span class="p2_copy"><span id="itemtotal'+rowname+'" name="itemtotal'+rowname+'">$0.00</span></span></td>'+
			'				</tr>'+
			'			</table>';
		
		var _row = document.createElement('table');
		_row.setAttribute('id', rowname);
		_row.setAttribute('name', rowname);
		_row.setAttribute('border', "0");
		_row.setAttribute('cellspacing', "1");
		_row.setAttribute('cellpadding', "4");
		_row.setAttribute('width', "100%");
		_row.setAttribute('class', "outline");
		_row.setAttribute('className', "outline");
		var _row_Obj = document.getElementById('orderCanvas').appendChild(_row);
		
		var _row_tbody = document.createElement('tbody');
		var _row_tbody_Obj = _row_Obj.appendChild(_row_tbody);
		
		var _row_tr = document.createElement('tr');
		var _row_tr_Obj = _row_tbody_Obj.appendChild(_row_tr);
		
		var _row_tr_td = document.createElement('td');
		var _row_tr_td_Obj = _row_tr_Obj.appendChild(_row_tr_td);
		_row_tr_td_Obj.innerHTML = html;
		
		if(document.getElementById('addtoorder'))
		{
		document.getElementById('addtoorder').focus();
		}
		document.getElementById('material'+rowname).focus();
	}
	totalprices();
}

function handle_clearAllItems()
{
	bgSet('orderCanvas','');
	add2Order();
	totalprices();
}

function handle_optionremove(rowname)
{
	var tblOwner = document.getElementById('orderCanvas'); //object
	var tblRemove = document.getElementById(rowname); //object
	try
	{
		tblOwner.removeChild(tblRemove);
		//increment count
		var cntObj = document.getElementById('orderCanvas');
		var cntattonj = cntObj.attributes['cnt'];
		var newcnt = cntattonj.nodeValue.valueOf();
		newcnt--;
		cntattonj.nodeValue = newcnt;
	}
	catch(x)
	{
		//
	}
	totalprices();
}

function totalItems(parentrowname)
{	
	var itemData = new Object();
	itemData.material = document.getElementById('material'+parentrowname);
	itemData.width = document.getElementById('width'+parentrowname);
	itemData.height = document.getElementById('height'+parentrowname);
	itemData.twoSided = document.getElementById('twoSided'+parentrowname);
	itemData.quantity = document.getElementById('quantity'+parentrowname);
	//itemData.sewing = document.getElementById('sewing'+parentrowname);
	itemData.grommetsCount = document.getElementById('grommets'+parentrowname);
	itemData.grommetsprice_display = document.getElementById('grommetsprice_display'+parentrowname);
	itemData.polepocketsprice_display = document.getElementById('polepocketsprice_display'+parentrowname);
	itemData.pocketsTop = document.getElementById('pocketsTop'+parentrowname);
	itemData.pocketsBottom = document.getElementById('pocketsBottom'+parentrowname);
	itemData.poleDiameter = document.getElementById('poleDiameter'+parentrowname);
	itemData.service = document.getElementById('service'+parentrowname);
	itemData.itemtotal = document.getElementById('itemtotal'+parentrowname);
	
	if (!bgIsInteger(itemData.width.value,false))
	{
		itemData.width.value='';
	}
	
	if (!bgIsInteger(itemData.height.value,false))
	{
		itemData.height.value='';
	}
	
	if (!bgIsInteger(itemData.quantity.value,false))
	{
		itemData.quantity.value='';
	}
	
	if (!bgIsInteger(itemData.grommetsCount.value,false))
	{
		itemData.grommetsCount.value='';
	}
		
	var workingpricepersqfeet = 0;
	var squaredFt = 0;
	var twosidedmultiplier = 0;
	var grommetprice = 0;
	var pocketprice = 0;
	var servicemultiplier = 0;
	
	//look up price based on material and size
	itemData.itemtotal.innerHTML = '$0.00';
	
	//#1 get squared_feet ((width/12)*(heigh/12))
	//squaredFt = ((parseFloat(itemData.width.value)/12) * (parseFloat(itemData.height.value)/12));
	if (!isNaN(parseFloat(itemData.quantity.value)))
		squaredFt = ((parseFloat(itemData.width.value)/12) * (parseFloat(itemData.height.value)/12)) * parseFloat(itemData.quantity.value);	
	else
		squaredFt = ((parseFloat(itemData.width.value)/12) * (parseFloat(itemData.height.value)/12));
	
	//#2 get matrial_price = (on limit lookup table for squared_feet comapred to squared_feet_limit)
	for (var k in labworks2jsBgDtObjct)
	{
		if (labworks2jsBgDtObjct[k].mt==itemData.material.value && 
			squaredFt <= parseFloat(labworks2jsBgDtObjct[k].sfl) &&
			squaredFt >0)
		{
			workingpricepersqfeet = parseFloat(labworks2jsBgDtObjct[k].pr);
			break;
		}
	}
	
	//#4 (if double_sided then) get double_sided_modifier = (on limit lookup table for quantity comapred to double_sided_modifier_limit)
	if(itemData.twoSided.checked===true)
	{
		for (var k in labworks2jsDSObjct)
		{
			if (parseFloat(itemData.quantity.value) <= parseFloat(labworks2jsDSObjct[k].lt) &&
				parseFloat(itemData.quantity.value) >0)
			{
				twosidedmultiplier = parseFloat(labworks2jsDSObjct[k].pm,true);
				break;
			}
		}
	}

	//#5 (if gromment_count) gromment_price = (on limit lookup table for gromment_count comapred to grommet_limit)
	itemData.grommetsprice_display.innerHTML='$0.00';
	if(parseFloat(itemData.grommetsCount.value) > 0)
	{
		for (var k in labworks2jsGrmObjct)
		{
			if (parseFloat(itemData.grommetsCount.value) <= parseFloat(labworks2jsGrmObjct[k].lt) &&
				parseFloat(itemData.grommetsCount.value) >0)
			{
				grommetprice = parseFloat(labworks2jsGrmObjct[k].pr);
				itemData.grommetsprice_display.innerHTML=addCurrency(grommetprice);
				break;
			}
		}
	}                                            
	
	//#7 (if pole_pocket_top or pole_pocket_bottom) pole_pocket_price = (on limit lookup table for quantity comapred to pole_pocket)
	itemData.polepocketsprice_display.innerHTML='$0.00';
	if(itemData.pocketsTop.checked===true||itemData.pocketsBottom.checked===true)
	{
		var inpcktcnt = 0;
		if(itemData.pocketsTop.checked===true)
		{
			inpcktcnt++;
		}
		//(if pole_pocket_bottom) item_price = item_price + (pole_pocket_price * 1)
		if(itemData.pocketsBottom.checked===true)
		{
			inpcktcnt++
		}
		for (var k in labworks2jsPktObjct)
		{
			if (((Math.ceil(parseFloat(itemData.width.value)/12))*inpcktcnt) <= parseFloat(labworks2jsPktObjct[k].lt) &&
				parseFloat(itemData.quantity.value) >0)
			{
				pocketprice = parseFloat(labworks2jsPktObjct[k].pr);
				itemData.polepocketsprice_display.innerHTML=addCurrency(pocketprice);
				break;
			}
		}
	}
	
	//#8 get service_modifier then item_price = item_price * service_modifier
	switch(parseFloat(itemData.service.value))
	{
		case 5:
			servicemultiplier = 0;
			break; 
		case 4:
			// servicemultiplier = parseFloat(labworks2jsServObjct.rsh);
			servicemultiplier = 0.25;
			break;    
		case 3:
			//servicemultiplier = parseFloat(labworks2jsServObjct.ond);
			servicemultiplier = 0.5;
			break;
		case 2:
			// servicemultiplier = parseFloat(labworks2jsServObjct.crh);
			servicemultiplier = 1;
			break;
		default:
			//servicemultiplier = parseFloat(labworks2jsServObjct.nor);
			servicemultiplier = 1.5;
	}

	var addprice = 0;
	var pocketcount = 0;
	
	//#3 get item_price = matrial_price x squared_feet
	addprice = (workingpricepersqfeet*squaredFt);
	
	//(if double_sided then) item_price = item_price * double_sided_modifier
	if(itemData.twoSided.checked===true)
	{
		addprice = addprice * twosidedmultiplier;
	}
	
	//(if gromment_count) item_price = item_price + (gromment_price * gromment_count)
	if(parseFloat(itemData.grommetsCount.value) > 0)
	{
		addprice = addprice + (grommetprice * parseFloat(itemData.grommetsCount.value));
	}
	
	if(itemData.pocketsTop.checked===true||itemData.pocketsBottom.checked===true)
	{
		//(if pole_pocket_top) item_price = item_price + (pole_pocket_price * 1)
		if(itemData.pocketsTop.checked===true)
		{
			pocketcount++;
		}
		//(if pole_pocket_bottom) item_price = item_price + (pole_pocket_price * 1)
		if(itemData.pocketsBottom.checked===true)
		{
			pocketcount++
		}
		addprice = addprice + (((Math.ceil(parseFloat(itemData.width.value)/12))*pocketcount)*pocketprice);
	}
	
	// addprice = addprice * servicemultiplier;
	addprice = addprice + (addprice * servicemultiplier);
	
	//#9 get quantity then total_item_price = item_price * quantity
	//addprice = addprice * parseFloat(itemData.quantity.value);
	
	if(isNaN(addprice)===true)
	{
		itemData.itemtotal.innerHTML = '$0.00';
	}
	else
	{
		itemData.itemtotal.innerHTML = addCurrency(addprice);
	}
	
	//#10 add up each total_item_price to get order_price
	totalprices();
}

function totalprices()
{
	if(document.getElementById('total'))
	{
		var objParent = document.getElementById('total');
		objParent.innerHTML='$0.00';
	}
	var orderItemElements = document.getElementById("orderCanvas");
	var orderTagElements = orderItemElements.getElementsByTagName('table');
	var itemCount = orderTagElements.length/3;
	var counter = 0;
	var total = 0;
	for (counter=0;counter<itemCount;counter++)
	{
		if(orderTagElements[counter])
		{	
			var parentrowname = orderTagElements[counter*3].id;
			var cleanprice = document.getElementById('itemtotal'+parentrowname).innerHTML;
			cleanprice = cleanprice.replace(',', "");
			cleanprice = cleanprice.replace('$', "");
			total+=parseFloat(cleanprice);
		}
	}
	if(document.getElementById('total'))
	{
		if(minOrderPrice!=0.00&&total!=0.00)
		{
			if(total<minOrderPrice)
			{
				objParent.innerHTML=addCurrency(minOrderPrice);
			}
			else
			{
				objParent.innerHTML=addCurrency(total);
			}
		}
		else
		{
			objParent.innerHTML=addCurrency(total);
		}
	}
}

function toggleUseShipAddress()
{
	// init some variables to use.
	var objRef = '';
	var trgtRef = '';
	
	// based on the browser get an reference to the element we will be working with.
	if (document.all)
	{
		objRef = document.all['UseShipAddress'].checked;
	}
	else
	{
		objRef = document.getElementById('UseShipAddress').checked;
	}
	
	if(objRef == "0")
	{
		trgtRef.value = "1";
		document.getElementById('shipAddress').style.display = "none";
	}
	else
	{
		trgtRef.value = "0";
		document.getElementById('shipAddress').style.display = "block";
		populateAbbStates("ShippingState")
	}
}

function addCurrency( passValue ) 
{
	strValue = passValue.toString();
	var objRegExp = /(?:(-))?([0-9]+)(((?:[\.])?)([0-9]+))?/;
	var objre = /a/g;
	var objReturnExp = objRegExp.exec(strValue);
	
	var reMinus = '';
	var reNumber = ''; 
	var reDecimaltest = '';
	var reDecimalStr = '';
	var reDecimal = 0;
	var adddollar = false;
	
	//pull matches from pattern
	if(objReturnExp)
	{
		reMinus=objReturnExp[1];
		reNumber=objReturnExp[2];
		if(objReturnExp[4])
		{
			reDecimaltest=objReturnExp[5];
			if(reDecimaltest.length>=1){reDecimalStr=reDecimaltest.substring(0,1)}
			if(reDecimaltest.length>=2){reDecimalStr+=reDecimaltest.substring(1,2)}
			
			reDecimal = parseFloat(reDecimalStr);
			if(reDecimaltest.substring(2,3).valueOf()>=5)//9
			{
				reDecimal++;
				if (reDecimal == 100)
				{
					adddollar = true;
					reDecimalStr='00';
					reDecimal = 0;
				}
			}
		}
		else
		{
			reDecimalStr='00';
			reDecimal=0;
		}
		if(adddollar===true)
		{
			reNumber++;
		}
	}
	objre.lastindex = 0;
	var retDecimal = '';
	if(reNumber!='')
	{
		if(parseFloat(reDecimalStr)==0)
		{
			reDecimalStr = '00';
		}
		else
		{
			if(reDecimalStr.length == 1){reDecimalStr+='0';}
		}
		
		strReturn = addCommas(reNumber+'.'+reDecimalStr);
		if(reMinus=='-')
		{
			strReturn = '(' + strReturn + ')';
		}
		
		return '$' + strReturn;
	}
	return passValue;
}

function addCommas( passValue ) 
{
	// make sure it's a string
	var strValue = passValue.toString();
	var reThousands = '';
	var reDecimals = '';
	var reRemainder = '';
	
	if(strValue.indexOf('.')>0)
	{
		// get groups of three{thousands} before the decimal
		var objRegExp = /((([0-9]{3})*)[\.])?([0-9]+)?$/;
		var objre = /a/g;
		var objReturnExp = objRegExp.exec(strValue);
		//pull matches from pattern
		if(objReturnExp)
		{
			reThousands=objReturnExp[2];
			reDecimals=objReturnExp[4];
		}
		objre.lastindex = 0;
		reRemainder = strValue.replace(objRegExp, '');
	}
	else
	{
		// get groups of three{thousands} before the decimal
		var objRegExp = /(([0-9]{3})*)$/;
		var objre = /a/g;
		var objReturnExp = objRegExp.exec(strValue);
		//pull matches from pattern
		if(objReturnExp)
		{
			reThousands=objReturnExp[1];
		}
		objre.lastindex = 0;
		reRemainder = strValue.replace(objRegExp, '');
	}
	//alert('reRemainder '+reRemainder+' reThousands '+reThousands+' reDecimals '+reDecimals);
	var result="";
	if(reRemainder!=''&&reThousands=='')
	{
		result += reRemainder+'';
	}
	if(reRemainder!=''&&reThousands!='')
	{
		result += reRemainder+',';
	}
	if(reThousands!='')
	{
		var objRegExp  = new RegExp('(-?[0-9]+)([0-9]{3})');
		
		//check for match to search criteria
		while(objRegExp.test(reThousands)) 
		{
			//replace original string with first group match,
			//a comma, then second group match
			reThousands = reThousands.replace(objRegExp, '$1,$2');
		}
		result += reThousands;
	}
	if(reDecimals!='')
	{
		result += '.'+reDecimals;
	}
	
	return result;
}

var abbStates = new Array("AK","AL","AR","AZ","CA","CO","CT","DC","DE","FL","GA","HI","IA","ID","IL","IN","KS","KY","LA","MA","MD","ME","MI","MN","MO","MS","MT","NC","ND","NE","NH","NJ","NM","NV","NY","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VA","VT","WA","WI","WV","WY");

function populateAbbStates(target)
{
		var targetObjct = document.getElementById(target);
		for (var i in abbStates)
		{
			 var optn = document.createElement("OPTION");
			optn.text = abbStates[i];
			optn.value = abbStates[i];
			targetObjct.options.add(optn);
		} 	
}

// start functions bgSetInsertAfter // Set the 'innerHTML' of an element
function bgSetInsertAfter(jLmntID, jVl)
{// jLmntID = {element id}, jVl = {value to set with}
	var refObjct = bgGet(jLmntID);
	bgSet(jLmntID, refObjct+jVl);
}
// end bgSetInsertAfter

// start functions bgSetInsertBefore // Set the 'innerHTML' of an element
function bgSetInsertBefore(jLmntID, jVl)
{// jLmntID = {element id}, jVl = {value to set with}
	var refObjct = bgGet(jLmntID);
	bgSet(jLmntID, jVl+refObjct);
}
// end bgSetInsertBefore

// start functions bgSet // Set the 'innerHTML' of an element
function bgSet(jLmntID, jVl)
{// jLmntID = {element id}, jVl = {value to set with}
	
        var objRef = '';
	if (document.all)// ie
	{
		objRef = document.all[jLmntID];
		if (objRef != null)
		{
		objRef.innerHTML = jVl;
		}
	}
	else// other
	{
		objRef = document.getElementById(jLmntID);
		if (objRef != null)
		{
		objRef.innerHTML = jVl;
		}
	}
} // bgSet()

// start functions bgSet // Set the 'innerHTML' of an element
function bgGet(jLmntID)
{// jLmntID = {element id}, jVl = {value to set with}
	
        var objRef = '';
	if (document.all)// ie
	{
		objRef = document.all[jLmntID];
		if (objRef != null)
		{
			return objRef.innerHTML;
		}
	}
	else// other
	{
		objRef = document.getElementById(jLmntID);
		if (objRef != null)
		{
			return objRef.innerHTML;
		}
	}
} // bgSet()

var reFloat = /^-?((\d+(\.\d*)?)|((\d*\.)?\d+))$/;
function bgIsNumber(jNmbr) { // jNmbr = number to check
	return reFloat.test(jNmbr);
} // jsIsNumber()

function bgIsInteger(jNmbr, jSgndNtgr) { // jNmbr = number to check | jSgndNtgr = allow signed integer (true/false)
	if (jSgndNtgr)
		return (jNmbr.toString().search(/^-?[0-9]+$/) == 0); // allows negative sign
	else
		return (jNmbr.toString().search(/^[0-9]+$/) == 0); // does not allow negative sign
} // jsIsInteger()

function jSetCtCode(){
	jsSetValue('ctcode', 'McVcKdOQHOI0bZYgqaL4TnZnWez6CbGpcfKG7dFhvY62TOXJju0NNDWEXw1r');	
}