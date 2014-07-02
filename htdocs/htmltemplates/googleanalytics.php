<?php
// Property of: Parallax Digital Studios, Inc.
// htmltemplates/googleanalytics.php
// Updated: 09.02.2011_rg

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { exit(0); } // This template must be included in a calling routine

?>
<script type="text/javascript">
			var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
			document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
			var pageTracker = _gat._getTracker("UA-1408926-3");
			pageTracker._trackPageview();
		</script>		
