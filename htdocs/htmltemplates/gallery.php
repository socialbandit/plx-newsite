<?php
// Property of: Parallax Digital Studios, Inc.
// htmltemplates/gallery.php
// Updated: 09.06.2011_rg, 09.07.2011_rg

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { exit(0); } // This template must be included in a calling routine

// Make CSS stylesheet(s) calls
$local_cssstylesheets = '';
$local_cssstylesheets .= '<link rel="stylesheet" type="text/css" href="/css/jquery.lightbox-0.5.css" media="screen" />' . chr(10);

// Make CSS variables
$local_cssvars = '';

if (getAuthenticationFlag())
		$local_cssvars .= str_repeat(chr(9), 3) . '#_editableareafld_0 { width:700px; height:20px; }' . chr(10) . 
					  	  str_repeat(chr(9), 3) . '#_editableareafld_1 { width:900px; height:200px; }' . chr(10);

if (trim($local_cssvars) != '')
	$local_cssvars = '<style type="text/css"><!--' . chr(10) . $local_cssvars . str_repeat(chr(9), 2) . '//--></style>' . chr(10); 

if (trim($local_cssstylesheets) != '')
	$local_cssvars = $local_cssstylesheets . $local_cssvars; 

// Make JS variables
$local_jsvars = '';

$local_jsvars .= str_repeat(chr(9), 3) . 'var pagename = \'' . str_replace(" ", "&nbsp;", ucwords(fetchEditableContentInformation($pagetitlecontentid))) . '\';' . chr(10);
				 
if (getAuthenticationFlag())	
	$local_jsvars .= str_repeat(chr(9), 3) . 'var uploadfoldername = \'' . $uploadsfolder . '\';' . chr(10);			 

if (getAuthenticationFlag())
		$local_jsvars .= str_repeat(chr(9), 3) . 'var editableareas_idlist = \'' . fn_makeid('editablepagetitle') . ',' . fn_makeid('eacontent') . '\';' . chr(10);

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
					 str_repeat(chr(9), 2) . '<script type="text/javascript" src="/js/jquery.lightbox-0.5.min.js"></script>' . chr(10) .
					 str_repeat(chr(9), 2) . '<script type="text/javascript" src="/js/bigbannersplus.js"></script>' . chr(10) .
					 str_repeat(chr(9), 2) . '<script type="text/javascript">' . '$(document).ready(function() { $(\'#_nav_' . $pageid . '\').removeClass().addClass(\'nav_top_selected\'); });' . '</script>'. chr(10);

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

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php echo(lclCUSTOM_makeHtmlHeadTags()); ?>
		<link rel="stylesheet" href="/css/styles.css" type="text/css">
		<?php echo($local_consolidatedvars); ?>
		<?php include('googleanalytics.php'); ?>
	</head>
	
	<body<?php if(getAuthenticationFlag()){echo(' onload="startup();"');} ?> leftmargin="0" marginheight="0" marginwidth="0" topmargin="0" style="background-color:#979a9f;">
		<div id="innerwrapper" align="center">
			<div id="innerwrappercenter">
				<div style="position:relative;width:960px;height:53px;background-image:url(/images/bkg-orange.jpg);">
					<div style="position:absolute;top:0px;left:0px;width:215px;height:61px;">
						<div class="top_left" style="position:relative;width:220px;height:71px;">
							<div style="position:absolute;top:15px;left:15px;width:184px;height:85px;"><a href="/" title="Home Page"><img src="/images/logo-bigbanners.jpg" alt="" width="184" height="85" border="0" /></a></div>
						</div>
					</div>
					<div style="position:absolute;top:0px;left:217px;width:744px;height:61px;">
						<div style="position:relative;width:744px;height:61px;background-image:url(/images/bkg-stripe.jpg);">
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
					<table width="920" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td align="left" valign="top" style="width:920px;padding-top:30px;">
								<div><?php echo(lclCUSTOM_fetchImagesIntoDivElement(($uploadsfolder), 'alpha', '_imagegallery', '', '', '', '', '', '', 'thumb_', '', '', '<a href="" title="">', '</a>', 0, '$(\'#_imagegallery a\').lightBox({fixedNavigation:true});')); ?></div>
							</td>	
						</tr>
						<tr>
							<td align="left" valign="top" class="supercopy" id="<?php echo(fn_makeid('eacontent')); ?>" style="width:900px;height:200px;padding-top:50px;padding-bottom:50px;"><?php echo(lclCUSTOM_fetchC('eacontent')); ?></td>
						</tr>
					</table>
					<div id="innerfooter">
						<div id="footerlogo" align="left"><a href="/" title="Home Page"><img src="/images/logo-bigbanners.jpg" alt="" width="184" height="85" border="0" /></a></div>
						<div id="innerfooternavigation" align="right"><?php include('footer3.php'); ?></div>
					</div>
				</div>
			</div>
		</div>
	</body>

</html>