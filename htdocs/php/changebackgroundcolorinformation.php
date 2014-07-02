<?php
// Property of: Parallax Digital Studios, Inc.
// php/changebackgroundcolorinformation.php
// Updated: 09.03.2011_rg

if (isset($_POST['bkgclr']))
	setcookie('bb_bkgclr', $_POST['bkgclr'], 0, '/');
echo('//');
exit(0);

?>