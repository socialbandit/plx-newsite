<?php
// Property of: Parallax Digital Studios, Inc.
// htmltemplates/fblike.php
// Updated: 09.04.2011_rg

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { exit(0); } // This template must be included in a calling routine

if (!isset($local_fblikeurl))
	$local_fblikeurl = $glbl_websiteaddress;
else	
	$local_fblikeurl = $glbl_websiteaddress . $local_fblikeurl;

?>
&nbsp;&nbsp;<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="<?php echo($local_fblikeurl); ?>" send="false" layout="button_count" width="150" show_faces="false" action="like" font=""></fb:like>
