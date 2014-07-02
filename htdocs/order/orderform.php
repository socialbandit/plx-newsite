<?php
// Property of: Parallax Digital Studios, Inc.
// order/orderform.php
// Updated: 09.06.2011_rg, 09.07.2011_rg, 02.18.2012_rg

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { exit(0); } // This template must be included in a calling routine

?>
<form id="OrderForm" name="OrderForm" action="events.php?event=sendorder" method="post" onSubmit="javascript:return validateRequest();">
													<table border="0" cellspacing="0" cellpadding="1" width="100%">
														<tr width="100%">
															<td  align="left" valign="top" width="100%">
																<div id="orderCanvas" name="orderCanvas"><script type="text/javascript">add2Order();</script><div>
															</td>
														</tr>
														<tr>
															<td>
																<table class="outline" border="0" cellspacing="1" cellpadding="4" width="100%">
																	<tr>
																		<td>
																			<table width="100%">
																				<tr>
																					<td style="text-align: left;"><span class="p2_copy"><a id="addtoorder" name="addtoorder"  onClick="add2Order();">Add Item</a>&ensp;&ensp;<a onClick="handle_clearAllItems();">Clear All Items</a></span></td>
																					<td style="text-align: right;"><span class="p2_copy"><strong>Total</strong></span>&ensp;<span class="p2_copy"><span id="total" name="total" size="10" maxlength="10">$0.00</span></span></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td align="left" valign="top" width="581"><img src="/images/clear.gif" alt="Big Banners Plus : Atlanta" width="32" height="12" border="0" /></td>
														</tr>
														<tr>
															<td align="left" valign="top" width="581">
																<table border="0" cellspacing="1" cellpadding="1">
																	<tr>
																		<td align="left" valign="top" width="182"><span class="p2_copy"><strong>Company Information</strong></span></td>
																		<td align="left" valign="top" width="108"></td>
																		<td align="left" valign="top" width="84"></td>
																	</tr>
																	<tr>
																		<td align="left" valign="top" width="182"><span class="p2_copy">Contact</span><br />
																			<input class="fieldyellow" type="text" name="Contact" id="Contact" size="32" /></td>
																		<td align="left" valign="top" width="108"><span class="p2_copy">Company Name</span><br />
																			<input class="fieldyellow" type="text" name="StoreNum" id="StoreNum" size="16" /></td>
																		<td align="left" valign="top" width="84"><span class="p2_copy">Phone</span><br />
																			<input class="fieldyellow" type="text" name="Phone" id="Phone" size="14" /></td>
																		<td align="left" valign="top" width="185"><span class="p2_copy">Email</span><br />
																			<input class="fieldyellow" type="text" name="Email" id="Email" size="32" maxlength="125" /></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td align="left" valign="top" width="581">
																<hr noshade="noshade" size="1" style="color: #8D8D8D;"/>
															</td>
														</tr>
														<tr>
															<td align="left" valign="top" width="581">
																<table id="billAddress" name="billAddress" border="0" cellspacing="1" cellpadding="1">
																	<tr>
																		<td colspan="4" align="left" valign="top"><span class="p2_copy"><strong>Billing Address</strong></span></td>
																	</tr>
																	<tr>
																		<td align="left" valign="top"><span class="p2_copy">Address Line 1</span><br /><input class="fieldyellow" type="text" name="BillingAddress1" id="BillingAddress1" size="38" /></td>
																		<td align="left" valign="top" width="12"><img src="/images/clear.gif" alt="Big Banners Plus : Atlanta" width="12" height="10" border="0" /></td>
																		<td colspan="2" align="left" valign="top"><span class="p2_copy">Address Line 2</span><br /><input class="fieldgray" type="text" name="BillingAddress2" id="BillingAddress2" size="38" /></td>
																	</tr>
																	<tr>
																		<td align="left" valign="top"><span class="p2_copy">City</span><br /><input class="fieldyellow" type="text" name="BillingCity" id="BillingCity" size="38" /></td>
																		<td align="left" valign="top" width="12"><img src="/images/clear.gif" alt="Big Banners Plus : Atlanta" width="12" height="10" border="0" /></td>
																		<td align="left" valign="top"><span class="p2_copy">State</span><br /><select class="fieldyellow" size="1" name="BillingState" id="BillingState"><option value="">Choose One</option></select></td>
																		<td align="left" valign="top"><span class="p2_copy">Zip</span><br /><input class="fieldyellow" type="text" name="BillingZip" id="BillingZip" size="12" /></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr>
															<td align="left" valign="top"><input type="checkbox" id="UseShipAddress" name="UseShipAddress" type="text" name="zip" size="12" onclick="toggleUseShipAddress();"/><span class="p2_copy">Shipping Address Different From Billing</span></td>
														</tr>
														<tr>
															<td align="left" valign="top" width="581">
																<table id="shipAddress" name="shipAddress" border="0" cellspacing="1" cellpadding="1" style="display:none;">
																	<tr>
																		<td colspan="4" align="left" valign="top"><span class="p2_copy"><strong>Shipping Address</strong></span></td>
																	</tr>
																	<tr>
																		<td align="left" valign="top"><span class="p2_copy">Address Line 1</span><br /><input class="fieldyellow" type="text" name="ShippingAddress1" id="ShippingAddress1" size="38" /></td>
																		<td align="left" valign="top" width="12"><img src="/images/clear.gif" alt="Big Banners Plus : Atlanta" width="12" height="10" border="0" /></td>
																		<td colspan="2" align="left" valign="top"><span class="p2_copy">Address Line 2</span><br /><input class="fieldgray" type="text" name="ShippingAddress2" id="ShippingAddress2" size="38" /></td>
																	</tr>
																	<tr>
																		<td align="left" valign="top"><span class="p2_copy">City</span><br /><input class="fieldyellow" type="text" name="ShippingCity" id="ShippingCity" size="38" /></td>
																		<td align="left" valign="top" width="12"><img src="/images/clear.gif" alt="Big Banners Plus : Atlanta" width="12" height="10" border="0" /></td>
																		<td align="left" valign="top"><span class="p2_copy">State</span><br/><select class="fieldyellow" size="1" name="ShippingState" id="ShippingState"><option value="">Choose One</option></select></td>
																		<td align="left" valign="top"><span class="p2_copy">Zip</span><br /><input class="fieldyellow" type="text" name="ShippingZip" id="ShippingZip" size="12" /></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr height="16">
															<td align="left" valign="top" width="581" height="16">
																<hr noshade="noshade" size="1" style="color: #8D8D8D;"/>
															</td>
														</tr>
														<tr>
															<td align="left" valign="middle" width="581">
																<table border="0" cellspacing="1" cellpadding="1">
																	<tr>
																		<td align="left" valign="middle"><span class="p2_copy">Notes:&nbsp;</span><textarea name="Notes" id="Notes" style="width:250px;" rows="3"></textarea></td>
																	</tr>	
																	<tr>
																		<td align="left" valign="middle"><br /><span class="send">Place Order&nbsp;</span>
																		<a href="javascript:validateRequest();"><img id="bnt_arrow3" src="/images/bnt_arrow.jpg" alt="" name="bnt_arrow3" width="25" height="18" border="0" /></a></td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
													<input type="hidden" name="BillingCompany" id="BillingCompany" value="" />
													<input type="hidden" name="ShippingCompany" id="ShippingCompany" value="" />
													<input type="hidden" name="itemdata" id="itemdata" value="" />
													<input type="hidden" name="odertotal" id="odertotal" value="" />
													<input type="hidden" name="fieldlist" id="fieldlist" value="itemdata,odertotal,Contact,StoreNum,Phone,Email,BillingAddress1,BillingAddress2,BillingCity,BillingState,BillingZip,BillingCompany,UseShipAddress,ShippingAddress1,ShippingAddress2,ShippingCity,ShippingState,ShippingZip,ShippingCompany,Notes" />
													<input type="hidden" name="ctcode" id="ctcode" value="" />
												</form>
												<script type="text/javascript">
													<!--
													populateAbbStates('BillingState');
													jSetCtCode();
													// -->
												</script>
