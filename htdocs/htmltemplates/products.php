<?php
// Property of: Parallax Digital Studios, Inc.
// htmltemplates/products.php
// Updated: 09.04.2011_rg, 09.07.2011_rg

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { exit(0); } // This template must be included in a calling routine

// Get static html for landing pages (if available) & determine if the page is a landing page or else it is a detail page
$local_staticfilename = str_replace('root/', '', $sectionid);
$local_staticfilename = str_replace('/', '_', $local_staticfilename) . $pageid;
$local_statichtmlstring = local_fetchStaticHTMLObjects($local_staticfilename);
// Flag to determine when to show the landing page editbale areas
$local_landingeditableflag = true;
if ($pageid == 'products')
	$local_landingeditableflag = false;

// Make CSS stylesheet(s) calls
$local_cssstylesheets = '';

// Make CSS variables
$local_cssvars = '';

if (getAuthenticationFlag()){
	if ($local_landingeditableflag)
		$local_cssvars .= str_repeat(chr(9), 3) . '#_editableareafld_0 { width:700px; height:20px; }' . chr(10) . 
						  str_repeat(chr(9), 3) . '#_editableareafld_1 { width:360px; height:500px; }' . chr(10) . 
						  str_repeat(chr(9), 3) . '#_editableareafld_2 { width:520px; height:500px; }' . chr(10) .
						  str_repeat(chr(9), 3) . '#_editableareafld_3 { width:280px; height:20px; }' . chr(10) .  	
					  	  str_repeat(chr(9), 3) . '#_editableareafld_4 { width:280px; height:150px; }' . chr(10);
	else
		$local_cssvars .= str_repeat(chr(9), 3) . '#_editableareafld_0 { width:700px; height:20px; }' . chr(10) . 
						  str_repeat(chr(9), 3) . '#_editableareafld_1 { width:360px; height:150px; }' . chr(10) . 
						  str_repeat(chr(9), 3) . '#_editableareafld_2 { width:520px; height:150px; }' . chr(10);
}				  	  

if (trim($local_cssvars) != '')
	$local_cssvars = '<style type="text/css"><!--' . chr(10) . $local_cssvars . str_repeat(chr(9), 2) . '//--></style>' . chr(10); 

if (trim($local_cssstylesheets) != '')
	$local_cssvars = $local_cssstylesheets . $local_cssvars; 

// Make JS variables
$local_jsvars = '';

$local_jsvars .= str_repeat(chr(9), 3) . 'var pagename = \'' . str_replace(" ", "&nbsp;", ucwords(fetchEditableContentInformation($pagetitlecontentid))) . '\';' . chr(10);
				 
if (getAuthenticationFlag())	
	$local_jsvars .= str_repeat(chr(9), 3) . 'var uploadfoldername = \'' . $uploadsfolder . '\';' . chr(10);			 

if (getAuthenticationFlag()){
	if ($local_landingeditableflag){
		$local_jsvars .= str_repeat(chr(9), 3) . 'var editableareas_idlist = \'' . fn_makeid('editablepagetitle') . ',' . fn_makeid('eacontent1') . ',' . fn_makeid('eacontent2') . ',' . fn_makeid('landingtitle') . ',' . fn_makeid('landingcontent') . '\';' . chr(10);
		$local_jsvars .= str_repeat(chr(9), 3) . 'var selectableimages_idlist = \'' . fn_makeid('pic1') . '\';' . chr(10);
	}
	else		
		$local_jsvars .= str_repeat(chr(9), 3) . 'var editableareas_idlist = \'' . fn_makeid('editablepagetitle') . ',' . fn_makeid('eacontent1') . ',' . fn_makeid('eacontent2') . '\';' . chr(10);	
}

if (getAuthenticationFlag())
	$local_jsvars .= str_repeat(chr(9), 3) . 'var toolboxidattributevalue = \'toolbox\';' . chr(10);

if (trim($local_jsvars) != '')
	$local_jsvars = '<script type="text/javascript"><!--' . chr(10) . $local_jsvars . str_repeat(chr(9), 2) . '//--></script>' .  chr(10);

// Make JS libraries call(s)
$local_jslibcalls = '';
if (getAuthenticationFlag())
	$local_jslibcalls = '<script type="text/javascript" src="/admin/dojo.js"></script>' . chr(10) .
						str_repeat(chr(9), 2) . '<script type="text/javascript" src="/admin/alexjet.js"></script>' . chr(10);
if ($local_jslibcalls != '')	
	$local_jslibcalls .= str_repeat(chr(9), 2);
$local_jslibcalls .= '<script type="text/javascript" src="/admin/jquery.js"></script>' . chr(10) .
					 str_repeat(chr(9), 2) . '<script type="text/javascript" src="/js/bigbannersplus.js"></script>' . chr(10);
if ($sectionid == 'root/')
	$local_jslibcalls .= str_repeat(chr(9), 2) . '<script type="text/javascript">' . '$(document).ready(function() { $(\'#_nav_' . $pageid . '\').removeClass().addClass(\'nav_top_selected\'); });' . '</script>'. chr(10);
if (getAuthenticationFlag() && $local_landingeditableflag)
	$local_jslibcalls .= str_repeat(chr(9), 2) . '<script type="text/javascript">' . 'function eventhook_onSelectEditableImage(){ $(\'#_selectimagefld_0\').change(function(){editablearea_edit(\'4\');editablearea_edit_doit(\'4\');eventhook_onSelectEditableImage();}); }' . '</script>'. chr(10);

// Consolidate CSS and JS variables - JS Lib call(s)
$local_consolidatedvars = $local_cssvars;
if (trim($local_consolidatedvars) != '')
	$local_consolidatedvars .= str_repeat(chr(9), 2);
$local_consolidatedvars .= $local_jsvars;	
if (trim($local_jslibcalls) != '')
	$local_consolidatedvars .= str_repeat(chr(9), 2) . $local_jslibcalls;

// When a favicon is available
$local_consolidatedvars = '<link rel="shortcut icon" href="/favicon.ico">' . chr(10) . str_repeat(chr(9), 2) . $local_consolidatedvars;

// Background color
$local_backgroundimage = '/images/bkg-all-red-rept.jpg';
if (isset($_COOKIE['bb_bkgclr'])) // Cookie exists with background color
	if (trim($_COOKIE['bb_bkgclr']) != '')
		$local_backgroundimage = '/images/bkg-all-' .  $_COOKIE['bb_bkgclr'] . '-rept.jpg';	

// On load event for body tag

$local_onload = '';
if (getAuthenticationFlag()){
	if ($local_landingeditableflag)
		$local_onload = 'onload="startup();eventhook_onSelectEditableImage();"';
	else	
		$local_onload = 'onload="startup();" ';	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php echo(lclCUSTOM_makeHtmlHeadTags()); ?>
		<link rel="stylesheet" href="/css/styles.css" type="text/css">
		<?php echo($local_consolidatedvars); ?>
		<?php include('googleanalytics.php'); ?>
	</head>
	
	<body <?php echo($local_onload); ?>leftmargin="0" marginheight="0" marginwidth="0" topmargin="0" style="background-color:#979a9f;">
		<div id="innerwrapper" align="center">
			<div id="innerwrappercenter">
				<div style="position:relative;width:960px;height:63px;background-image:url(/images/bkg-orange.jpg);">
					<div style="position:absolute;top:0px;left:0px;width:215px;height:61px;">
						<div class="top_left" style="position:relative;width:220px;height:71px;">
							<div style="position:absolute;top:15px;left:15px;width:184px;height:85px;"><a href="/" title="Home Page"><img src="/images/logo-bigbanners.jpg" alt="" width="184" height="85" border="0" /></a></div>
						</div>
					</div>
					<div style="position:absolute;top:0px;left:217px;width:744px;height:61px;">
						<div style="position:relative;width:744px;height:71px;background-image:url(/images/bkg-stripe.jpg);">
							<div style="position:absolute;top:90px;left:0px;width:735px;height:20px;"><?php include('topmenunav.php'); ?></div>
						</div>
					</div>
				</div>
				<div id="innerheadline">
					<div id="innertitle" align="left">
						<h1 id="<?php echo(fn_makeid('editablepagetitle')); ?>" class="home_banner_type" style="position:absolute;width:780px;height:30px;margin:0;<?php if (!getAuthenticationFlag()) { ?>overflow:hidden;<?php } else { ?>overflow:visible;z-index:105;<?php } ?>"><?php echo(lclCUSTOM_fetchC('editablepagetitle')); ?></h1>
					</div>
				</div>
				<div id="innerphone"><?php echo($local_tollfreephoneno); ?></div>
				<div id="facebookicon" align="right"><?php $local_fblikeurl=$pageid;include('fblike.php'); ?></div>
				<div id="expandablelayer" align="left">
					<?php if($local_statichtmlstring != '') { echo($local_statichtmlstring); } ?>
					<table width="900" border="0" cellspacing="0" cellpadding="0"<?php if(getAuthenticationFlag() && $local_statichtmlstring != '') { echo(' style="border: dashed 1px #B44B6F;"'); } ?>>
						<?php if(getAuthenticationFlag() && $local_statichtmlstring != '') { ?>
						<tr>
							<td colspan="2" align="left" valign="top" class="misclabel" style="padding-bottom:20px;"><b>Optional text for product landing pages:</b></td>
						</tr>	
						<?php } ?>						
						<tr>
							<td align="left" valign="top" class="supercopy" id="<?php echo(fn_makeid('eacontent1')); ?>" style="width:370px;<?php if($local_statichtmlstring != '') { echo('height:50px;'); } else { echo('height:500px;'); } ?>padding-bottom:50px;"><?php echo(lclCUSTOM_fetchC('eacontent1')); ?></td>
							<td align="left" valign="top" class="supercopy" id="<?php echo(fn_makeid('eacontent2')); ?>" style="width:530px;<?php if($local_statichtmlstring != '') { echo('height:50px;'); } else { echo('height:500px;'); } ?>padding-bottom:50px;"><?php echo(lclCUSTOM_fetchC('eacontent2')); ?></td>
						</tr>
					</table>
					<?php if (getAuthenticationFlag() && $local_landingeditableflag) { ?>
					<table width="900" border="0" cellspacing="1" cellpadding="1" style="padding-top:50px;padding-bottom:50px;">
						<tr>
							<td align="left" valign="top" width="300" class="cellborder_toprowleftmostcell all_cell_border"><span class="misclabel"><b>Title for <?php if ($sectionid == 'products/') { echo('Home Carousel and<br />Main Products Landing Page.'); } else { echo('Sub-Category Landing Page'); } ?></b></span></td>
							<td align="left" valign="top" width="300" class="cellborder_toprowothercells"><span class="misclabel"><b>Blurb for <?php if ($sectionid == 'products/') { echo('Main Products Landing Page.'); } else { echo('Sub-Category Landing Page'); } ?></b></span></td>
							<td align="left" valign="top" width="300" class="cellborder_toprowothercells"><span class="misclabel"><b>Image for <?php if ($sectionid == 'products/') { echo('Home Carousel and<br />Main Products Landing Page.'); } else { echo('Sub-Category Landing Page'); } ?><br />Width: 210px. - Height: 122px.</b></span></td>
						</tr>					
						<tr>
							<td align="left" valign="top" width="300" height="200" class="cellborder_rowsleftmostcell supercopy" id="<?php echo(fn_makeid('landingtitle')); ?>"><?php echo(lclCUSTOM_fetchC('landingtitle')); ?></td>
							<td align="left" valign="top" width="300" class="cellborder_rowsothercells supercopy" id="<?php echo(fn_makeid('landingcontent')); ?>"><?php echo(lclCUSTOM_fetchC('landingcontent')); ?></td>							
							<td align="left" valign="top" width="300" class="cellborder_rowsothercells supercopy" id="<?php echo(fn_makeid('pic1')); ?>"></td>
						</tr>					
					</table>
					<?php } ?>
					<div id="innerfooter">
						<div id="footerlogo" align="left"><a href="/" title="Home Page"><img src="/images/logo-bigbanners.jpg" alt="" width="184" height="85" border="0" /></a></div>
						<div id="innerfooternavigation" align="right"><?php include('footer3.php'); ?></div>
					</div>
				</div>
			</div>
		</div>
	</body>

</html>