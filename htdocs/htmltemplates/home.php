<?php
// Property of: Parallax Digital Studios, Inc.
// htmltemplates/home.php
// Updated: 09.02.2011_rg, 09.05.2011_rg

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { exit(0); } // This template must be included in a calling routine

// Make CSS stylesheet(s) calls
$local_cssstylesheets = '';

// Make CSS variables
$local_cssvars = '';

$local_cssvars = str_repeat(chr(9), 3) . '#rotatingimage img { display:none; }' . chr(10);

if (getAuthenticationFlag())
	$local_cssvars .= str_repeat(chr(9), 3) . '#_editableareafld_0 { width:300px; height:40px; }' . chr(10);

if (trim($local_cssvars) != '')
	$local_cssvars = '<style type="text/css"><!--' . chr(10) . $local_cssvars . str_repeat(chr(9), 2) . '//--></style>' . chr(10); 

if (trim($local_cssstylesheets) != '')
	$local_cssvars = $local_cssstylesheets . $local_cssvars; 

// Make JS variables
$local_jsvars = '';
$local_jsvars .= str_repeat(chr(9), 3) . 'var pagename = \'Home\';' . chr(10);
				 
if (getAuthenticationFlag())				 
	$local_jsvars .= str_repeat(chr(9), 3) . 'var uploadfoldername = \'home\';' . chr(10);

if (getAuthenticationFlag())
	$local_jsvars .= str_repeat(chr(9), 3) . 'var editableareas_idlist = \'' . fn_makeid('headline') . '\';' . chr(10);

if (getAuthenticationFlag())
	$local_jsvars .= str_repeat(chr(9), 3) . 'var toolboxidattributevalue = \'toolbox\';' . chr(10);

if (trim($local_jsvars) != '')
	$local_jsvars = '<script type="text/javascript"><!--' . chr(10) . $local_jsvars . str_repeat(chr(9), 2) . '//--></script>' .  chr(10);

// Make JS libraries call(s)
$local_jslibcalls = '';
if (getAuthenticationFlag() || getMemberAuthenticationFlag())
	$local_jslibcalls = '<script type="text/javascript" src="/admin/dojo.js"></script>' . chr(10) .
						str_repeat(chr(9), 2) . '<script type="text/javascript" src="/admin/alexjet.js"></script>' . chr(10);
if ($local_jslibcalls != '')	
	$local_jslibcalls .= str_repeat(chr(9), 2);

$local_jslibcalls .= '<script type="text/javascript" src="/admin/jquery.js"></script>' . chr(10) .
					 str_repeat(chr(9), 2) . '<script type="text/javascript" src="/js/bigbannersplus.js"></script>' . chr(10) .
					 str_repeat(chr(9), 2) . '<script type="text/javascript" src="/js/jquery.cycle.lite.1.0.min.js"></script>' . chr(10) .
					 str_repeat(chr(9), 2) . '<script type="text/javascript" src="/js/jsor-jcarousel-9f65793/lib/jquery.jcarousel.min.js"></script>' . chr(10) .
					 str_repeat(chr(9), 2) . '<script type="text/javascript">' . chr(10) .
					 str_repeat(chr(9), 3) . '$(document).ready(function() {' . chr(10) .	
					 str_repeat(chr(9), 4) . '$(\'#rotatingimage\').cycle({ delay:2000, speed:500 });' . chr(10) .
					 str_repeat(chr(9), 4) . '$(\'#homecarousel\').jcarousel({ auto:20 });' . chr(10) .					 
					 str_repeat(chr(9), 4) . '$(\'#swatch_green1\').click(function(){changeBackgroundColor(\'green1\');});' . chr(10) .					 
					 str_repeat(chr(9), 4) . '$(\'#swatch_orange\').click(function(){changeBackgroundColor(\'orange\');});' . chr(10) .
					 str_repeat(chr(9), 4) . '$(\'#swatch_red\').click(function(){changeBackgroundColor(\'red\');});' . chr(10) .
					 str_repeat(chr(9), 4) . '$(\'#swatch_green2\').click(function(){changeBackgroundColor(\'green2\');});' . chr(10) .					 
					 str_repeat(chr(9), 4) . '$(\'#swatch_yellow\').click(function(){changeBackgroundColor(\'yellow\');});' . chr(10) .					 
					 str_repeat(chr(9), 4) . '$(\'#swatch_blue\').click(function(){changeBackgroundColor(\'blue\');});' . chr(10) .					 
					 str_repeat(chr(9), 3) . '});' . chr(10) .
					 str_repeat(chr(9), 2) . '</script>'. chr(10);					 

// Consolidate CSS and JS variables - JS Lib call(s)
$local_consolidatedvars = $local_cssvars;
if (trim($local_consolidatedvars) != '')
	$local_consolidatedvars .= str_repeat(chr(9), 2);
$local_consolidatedvars .= $local_jsvars;	
if (trim($local_jslibcalls) != '')
	$local_consolidatedvars .= str_repeat(chr(9), 2) . $local_jslibcalls;

// When a favicon is available
$local_consolidatedvars = '<link rel="shortcut icon" href="/favicon.ico">' . chr(10) . str_repeat(chr(9), 2) . $local_consolidatedvars;

// Rotating images
$local_rotatingimage = lclCUSTOM_fetchImagesIntoDivElement('home', '', '', '', '', '', '', '', '', '', '', '', '', '', 10, '') . chr(10);
$local_rotatingimage = str_replace('<div>', '', $local_rotatingimage);
$local_rotatingimage = str_replace('</div>', '', $local_rotatingimage);

// Background color
$local_backgroundimage = '/images/bkg-all-red-rept.jpg';
if (isset($_COOKIE['bb_bkgclr'])) // Cookie exists with background color
	if (trim($_COOKIE['bb_bkgclr']) != '')
		$local_backgroundimage = '/images/bkg-all-' .  $_COOKIE['bb_bkgclr'] . '-rept.jpg';	

// Carousel
$local_statichtmlstring = local_fetchStaticHTMLObjects('carousel');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php echo(lclCUSTOM_makeHtmlHeadTags()); ?>
		<meta name="verify-v1" content="NMJYqmNdanjtKbTwAmR8BYCEuLV+bD43ciGmwDI1Hog=" />
		<link rel="stylesheet" href="/css/styles.css" type="text/css">
		<?php echo($local_consolidatedvars); ?>
		<?php include('googleanalytics.php'); ?>
	</head>
	
	<body<?php if(getAuthenticationFlag()){echo(' onload="startup();"');} ?> leftmargin="0" marginheight="0" marginwidth="0" topmargin="0" style="background-color:#979a9f;">
		
		
		<div id="top"></div>
		<div id="homewrapper" align="center">
			<div id="homewrappercenter">
				
				<div style="position:relative;width:970px;height:120px;background-color: #fff;left: -4px;">
					<div style="position:absolute;top:0px;left:0px;width:215px;height:61px;">
						<div class="top_left" style="position:relative;width:215px;height:61px;">
							<div style="position:absolute;top:15px;left:1px;width:184px;height:85px;"><img src="/images/logo-bigbanners.jpg" alt="" width="184" height="85" border="0" /></div>
						</div>
					</div>
					<div style="position:absolute;top:0px;left:217px;width:744px;height:61px;">
						<div style="position:relative;width:746px;height:102.5px;background-image:url(/images/bkg-stripe.jpg);">
							<div style="position:absolute;top:90px;left:0px;width:735px;height:20px;z-index: 106;"><?php include('topmenunav.php'); ?></div>
						</div>
					</div>
				</div>
			<div id="innerphone"><?php echo($local_tollfreephoneno); ?></div>
				<!------------------some text----------------------------->
				
				<div id="startorder" align="center"  >

			<div align="left" style="position:relative;width:961px;height:841px;background-image:url(/images/bkg-blue.jpg);top:62px ;">

				<div style="z-index:200;position:relative;width:427px;height:641px;float:right;">

					<div style="position:absolute;top:30px;left:0px;width:413px;height:606px;">

						<!--<div style="position:relative;width:413px;height:606px;">

							<div class="headline_2" style="position:absolute;top:12px;left:25px;width:315px;height:27px;">

								<div align="left">START YOUR ORDER<br /><span class="smallwhite"><strong>(Width and Height in Inches)</strong></span></div>

							</div>

							<form name="_myquickstartorderform" id="_myquickstartorderform" method="post" action="" enctype="multipart/form-data" onsubmit="javascript:return validateQuickStart();">

							<div style="position:absolute;top:53px;left:26px;width:60px;height:21px;">

								<input class="text_box" type="text" name="fld_width1" id="fld_width1" value="width" size="8" maxlength="10" /></div>

							<div style="position:absolute;top:53px;left:116px;width:60px;height:21px;">

								<input class="text_box" type="text" name="fld_height1" id="fld_height1" value="height" size="8" maxlength="10" /></div>

							<div style="position:absolute;top:53px;left:206px;width:60px;height:21px;">

								<input class="text_box" type="text" name="fld_qty1" id="fld_qty1" value="quantity" size="8" maxlength="10" /></div>

							<div style="position:absolute;top:98px;left:26px;width:120px;height:37px;">

								<input type="image" alt="Continue" name="Continue" src="/images/button-continue.png" border="0" width="120" height="37" /></div>

							<div style="position:absolute;top:161px;left:27px;width:384px;height:444px;">

							</div>

							<input type="hidden" name="ispost" value="1" />

							</form>

						</div>-->

					</div>

				</div>

				

				<div id="rotatingimage" style="z-index:100;height:277px;width:534px;left:0px;top:0px;position:absolute;"><?php echo($local_rotatingimage); ?></div>



				<div id="orangearea" style="background-image:url(/images/bkg-orange.jpg);background-repeat:repeat;height:464px;width:534px;left:0px;top:277px;position:absolute;">

					



				<div id="overlayimage" style="z-index:150;"><img src="/images/ad001.png" alt="" border="0" /></div>

                <div id="txt-img" style="z-index:150;"><img src="/images/BigbannerText .png" alt="" border="0" /></div>
                <div id="rdbkrg" style="z-index:60;"><img src="/images/BigBanner_BG2.png" alt="" border="0" /></div>

			</div>

		</div>
				
				<!-----------------------end------------------------->
				<!--<div id="swatch_green1"></div><div id="swatch_orange"></div><div id="swatch_red"></div><div id="swatch_green2"></div><div id="swatch_yellow"></div><div id="swatch_blue"></div>-->
				<div id="rotatingimage">
					
				</div>
				<div id="homeheadline">
					<div id="homeheadlinetext" align="left"><h1 class="home_banner_type" style="display:inline;"><?php echo(lclCUSTOM_fetchC('headline')); ?></h1></div>
					<!--<div id="homephone" align="right"><span class="phone_number"><?php echo($local_tollfreephoneno); ?></span></div>-->
				</div>
				
				
				
					
				
				</div>
			</div>
		</div>
		<div id="footer" >

			<div align="center">

				<?php include('footer.php'); ?>

			</div>

		</div>
		
	</body>

</html>