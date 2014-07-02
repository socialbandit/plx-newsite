<?php

// Property of: Parallax Digital Studios, Inc.
// order/events.php
// Updated: 08.17.2008_rg, 09.14.2008_rg, 04.07.2009_rg, 09.06.2011_rg, 02.18.2011_rg

include('order_config.php');
include('../admin/config.php');

function fn_openviacurl($local_url){
	$local_result = '';
	// Open connection
	$ch = curl_init();
	// Set the parameters
	curl_setopt($ch, CURLOPT_URL, $local_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_RANGE, '0-20000');
	// Execute request
	if (!$local_result = curl_exec($ch))
		$local_result = '';
	// Close connection
	curl_close($ch);
	return $local_result;		
}

function event_fn_getdata()
{
	global $minorderprice;
	$jsstring = 'minOrderPrice = ' . $minorderprice . ';' . chr(10);
	// Big Banners Plus - materials
	$jsstring .= fn_openviacurl(('http://tools.parallaxdigital.com/labworks/index.php?event=readlabworks&datakey=big_mat&uc=' . makeOrderID()));
	// Big Banners Plus - grommets
	$jsstring .= fn_openviacurl(('http://tools.parallaxdigital.com/labworks/index.php?event=readlabworks&datakey=big_grm&uc=' . makeOrderID()));
	// Big Banners Plus - pole pockets
	$jsstring .= fn_openviacurl(('http://tools.parallaxdigital.com/labworks/index.php?event=readlabworks&datakey=big_pol&uc=' . makeOrderID()));
	// Big Banners Plus - double sided	
	$jsstring .= "var labworks2jsDSObjct = new Object();";
	$jsstring .= "var __rs = new Object();";
	$jsstring .= "__rs.pm = '1.6';";
	$jsstring .= "__rs.lt = '999999';";
	$jsstring .= "labworks2jsDSObjct.rs1 = __rs;";
	// Service types/multipliers
	$jsstring .= fn_openviacurl(('http://tools.parallaxdigital.com/labworks/index.php?event=readlabworks&datakey=service&uc=' . makeOrderID()));

	return $jsstring;
}

function event_fn_getOrderTemplate()
{
	global $glbl_websiteaddress;
	//order_template.html
	$handle = fopen("templates/order_template.html", "r");
	$jsstring = stream_get_contents($handle);
	fclose($handle);

	return $jsstring;
}

function event_fn_getItemTemplate()
{
	global $glbl_websiteaddress;
	//item_template.html
	$handle = fopen("templates/item_template.html", "r");
	$jsstring = stream_get_contents($handle);
	fclose($handle);

	return $jsstring;
}

function event_fn_getShipTemplate()
{
	global $glbl_websiteaddress;
	//ship_template.html
	$handle = fopen("templates/ship_template.html", "r");
	$jsstring = stream_get_contents($handle);
	fclose($handle);

	return $jsstring;
}

function event_formprocessing($formvariables) 
{
	global $glbl_websiteaddress;
	//itemdata,itemtotal,Contact,StoreNum,Phone,Email,BillingAddress1,BillingAddress2,BillingCity,BillingState,BillingZip
	//UseShipAddress
	//ShippingAddress1,ShippingAddress2,ShippingCity,ShippingState,ShippingZip,Notes
	//$_ship_template_$$_item_template_$
	$ot = event_fn_getOrderTemplate();
	$it = event_fn_getItemTemplate();
	$st = event_fn_getShipTemplate();
	$cit = '';
	$itemrecords = explode('|', $formvariables['itemdata']);
	for ( $counter = 0; $counter < count($itemrecords); ++$counter) 
	{
		$tmp = $it;
		//$_item_$$_material_$$_size_$$_unitprice_$$_quantity_$$_price_$
		$itemfields = explode('~', $itemrecords[$counter]);
		$tmp = str_replace('$_itemcount_$', $counter+1, $tmp);
		$tmp = str_replace('$_material_$', $itemfields[0], $tmp);
		$tmp = str_replace('$_width_$', $itemfields[1], $tmp);
		$tmp = str_replace('$_height_$', $itemfields[2], $tmp);
		$tmp = str_replace('$_doublesided_$', $itemfields[3], $tmp);
		$tmp = str_replace('$_quantity_$', $itemfields[4], $tmp);
		$tmp = str_replace('$_grommets_$', 'Grommets: '.$itemfields[5], $tmp);
		$tmpPockets = '';
		if($itemfields[6]=='true'||$itemfields[7]=='true')
		{
			$tmpPockets .= 'Pockets: ';
			if($itemfields[6]=='true')
			{
				$tmpPockets .= 'Top: '.$itemfields[6].' ';
			}
			if($itemfields[7]=='true')
			{
				$tmpPockets .= 'Bottom: '.$itemfields[7].' ';
			}
			$tmpPockets .= ' - Pole Diameter: '.$itemfields[8];
		}
		$tmp = str_replace('$_pockets_$', $tmpPockets, $tmp);
		$tmp = str_replace('$_service_$', $itemfields[9], $tmp);
		$tmp = str_replace('$_itemtotal_$', $itemfields[10], $tmp);
		$cit .= $tmp;
	}
	
	$ot = str_replace('$_item_template_$', $cit, $ot);
	
	$sit = '';
	if(isset($formvariables['UseShipAddress']))
	{
		if($formvariables['UseShipAddress']=='on')
		{
			$tmp = $st;
			//$_item_$$_material_$$_size_$$_quantity_$$_price_$
			$tmp = str_replace('$_ShippingAddress1_$', $formvariables['ShippingAddress1'], $tmp);
			if(isset($formvariables['ShippingAddress2']))
			{
				$tmp = str_replace('$_ShippingAddress2_$', $formvariables['ShippingAddress2'], $tmp);
			}
			else
			{
				$tmp = str_replace('$_ShippingAddress2_$', '', $tmp);
			}
			$tmp = str_replace('$_ShippingCity_$', $formvariables['ShippingCity'], $tmp);
			$tmp = str_replace('$_ShippingState_$', $formvariables['ShippingState'], $tmp);
			$tmp = str_replace('$_ShippingZip_$', $formvariables['ShippingZip'], $tmp);
			$sit .= $tmp;
		}
		else
		{
			$sit = '';
		}
	}
	else
	{
		$sit = '';
	}
	$ot = str_replace('$_ship_template_$', $sit, $ot);
	
	//Contact,StoreNum,Phone,Email,Notes
	$ot = str_replace('$_Contact_$', $formvariables['Contact'], $ot);
	$ot = str_replace('$_StoreNum_$', $formvariables['StoreNum'], $ot);
	$ot = str_replace('$_Phone_$', $formvariables['Phone'], $ot);
	$ot = str_replace('$_Email_$', $formvariables['Email'], $ot);
	if(isset($formvariables['Notes']))	
		$ot = str_replace('$_Notes_$', $formvariables['Notes'], $ot);
	else	
		$ot = str_replace('$_Notes_$', '', $ot);

	//BillingAddress1,BillingAddress2,BillingCity,BillingState,BillingZip
	$ot = str_replace('$_BillingAddress1_$', $formvariables['BillingAddress1'], $ot);
	if(isset($formvariables['BillingAddress2']))
	{
		$ot = str_replace('$_BillingAddress2_$', $formvariables['BillingAddress2'], $ot);
	}
	else
	{
		$ot = str_replace('$_BillingAddress2_$', '', $ot);
	}
	$ot = str_replace('$_BillingCity_$', $formvariables['BillingCity'], $ot);
	$ot = str_replace('$_BillingState_$', $formvariables['BillingState'], $ot);
	$ot = str_replace('$_BillingZip_$', $formvariables['BillingZip'], $ot);
	
	//odertotal
	$ot = str_replace('$_ordertotal_$', $formvariables['odertotal'], $ot);
	
	//confirmationnumber
	$confirmNum = makeOrderID();
	$ot = str_replace('$_confirmationnumber_$', $confirmNum, $ot);
	
	//date
	$ot = str_replace('$_date_$', date("m/d/Y") . ' - ' . date("h:i:s A"), $ot);
	
	$ot = str_replace('$_glbl_websiteaddress_$', $glbl_websiteaddress, $ot);
	$ot = str_replace('$_receiptformgenerator_$', 'Big Banners Plus', $ot);
	
	// Send via email
	global $glbl_sendformsfromemailaddress, $parallax_order_toemailaddress;
	require_once("../admin/htmlMimeMail5/htmlMimeMail5.php"); // htmlMimeMail5 class
	    $mail = new htmlMimeMail5(); // Instantiate a new HTML Mime Mail object
	    $mail->setFrom($glbl_sendformsfromemailaddress);
	    $mail->setReturnPath($glbl_sendformsfromemailaddress);  
	    $mail->setSubject('Big Banners Plus Order Receipt | '.$confirmNum);
	    $mail->setText(str_replace('<br>', '\r\n', $ot));
	    $mail->setHTML($ot);
	    $mail->send(array($formvariables['Email'] . ',' . $parallax_order_toemailaddress), 'smtp'); // Send the email!
	echo($ot);
	
}

function makeOrderID() {
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


//  Event handlers 
if (!isset($_GET['event']))
	// return nothing for Javascript
	echo ('return;');
else{
	$event = $_GET['event'];
	switch ($event) {
		case 'getdata':
			echo(event_fn_getdata());
			break;
		case 'sendorder':
			if (!isset($_POST['ctcode'])){ 
				echo ('');
				exit(0);
			}	
			else{
				if (trim($_POST['ctcode']) != 'McVcKdOQHOI0bZYgqaL4TnZnWez6CbGpcfKG7dFhvY62TOXJju0NNDWEXw1r'){				
					echo ('');
					exit(0);
				}	
			}		
			if (!isset($_POST['fieldlist'])) 
				echo ('return;');
			else
				echo (event_formprocessing($_POST));
			break;
		default:
			echo('return;');
	} // switch
} // if

?>