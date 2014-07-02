<?php
// Property of: Parallax Digital Studios, Inc.
// htmltemplates/sendfilesform.php
// Updated: 09.06.2011_rg

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { exit(0); } // This template must be included in a calling routine

$localtmp_oid = '';
if (isset($_GET['oid'])) // From order receipt
	$localtmp_oid = trim($_GET['oid']);

?>

<form id="sendfilesform" name="sendfilesform" action="http://tools.parallaxdigital.com/sendfiles/index3.php" method="post" enctype="multipart/form-data" onsubmit="javascript:return validateSendFiles();">
							<table class="sendforms" border="0" cellspacing="1" cellpadding="1">
								<tr>
									<td align="left" valign="top" width="803" height="12"><span class="formfieldslabel"><strong>Enter name, email, select files and leave any comments if necessary.</strong></span></td>
								</tr>
								<tr>
									<td align="left" valign="top" width="803" height="8"><img src="/images/clear.gif" alt="Parallax : Atlanta" width="29" height="8" border="0" /></td>
								</tr>
								<tr>
									<td align="left" valign="top" width="803" height="12"><span class="formfieldslabel_required">Name:</span>&nbsp;<input type="text" name="Name" size="24" maxlength="60" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="formfieldslabel_required">Email:</span>&nbsp;<input type="text" name="Email" size="24" maxlength="100" /></td>
								</tr>
								<tr>
									<td align="left" valign="top" width="803" height="8"><img src="/images/clear.gif" alt="Parallax : Atlanta" width="29" height="8" border="0" /></td>
								</tr>
								<tr>
									<td align="left" valign="top" width="803" height="12"><span class="formfieldslabel">File 1:</span>&nbsp;&nbsp;<input type="file" name="imagefile1" value="" size="24" /><br /><br />
										<span class="formfieldslabel">File 2:</span>&nbsp;&nbsp;<input type="file" name="imagefile2" value="" size="24" /><br /><br />
										<span class="formfieldslabel">File 3:</span>&nbsp;&nbsp;<input type="file" name="imagefile3" value="" size="24" /><br /><br />
									</td>
								</tr>
								<tr>
									<td align="left" valign="top" width="803" height="8"><img src="/images/clear.gif" alt="Parallax : Atlanta" width="29" height="8" border="0" /></td>
								</tr>
								<tr>
									<td align="left" valign="top" width="803" height="67"><span class="formfieldslabel">Notes:</span><br />
										<textarea name="Notes" rows="4" cols="54"></textarea></td>
								</tr>
								<tr>
									<td align="left" valign="top" width="803" height="35">
										<br />
										<table border="0" cellspacing="1" cellpadding="1" width="100">
											<tr>
												<td align="left" valign="middle"><span class="formfieldssend">Send&nbsp;File(s)</span></td>
												<td align="left" valign="middle" width="34"><input type="image" alt="Search" name="send" src="/images/arrow.png" border="0" width="34" height="32" /></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						<input type="hidden" name="OrderID" value="<?php echo($localtmp_oid) ?>" />
						<input type="hidden" name="Reference" value="Please check FTP site for uploaded files (see list below)" />
						<input type="hidden" name="fieldlist" value="Name,Email,Notes,OrderID,Reference" />
						<input type="hidden" name="afterpage" value="<?php echo($glbl_websiteaddress) ?>sendfiles-thankyou" />
						<input type="hidden" name="jk" value="DNTHVSXTHGVRMNTFCKSMVRYDY:)" />
						</form>
						<div id="_spinner" style="display:none;"><img id="img-spinner" src="/images/in-progress.gif" alt="Sending Files"/></div>
