<?php
// Property of: Parallax Digital Studios, Inc.
// htmltemplates/footer.php
// Updated: 09.03.2011_rg, 09.04.2011_rg, 04.12.2012_rg

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { exit(0); } // This template must be included in a calling routine

?>

						<!------------------new footer----------------------------->
						
								<br /><span id="toolbox"></span>
								<?php if ($pageid == 'home') { ?>
									<div class="type_basic" style="width:300px; border-width: 1px; border-color:#D94731; border-style:dashed; margin-top:10px; margin-bottom:20px;" id="<?php echo(fn_makeid('headline')); ?>"><?php echo(lclCUSTOM_fetchC('headline')); ?></div>
								<?php } ?>					
							<?php } ?>