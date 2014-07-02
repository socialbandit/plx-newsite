<?php
// Property of: Parallax Digital Studios, Inc.
// htmltemplates/footer.php
// Updated: 09.03.2011_rg, 09.04.2011_rg, 04.12.2012_rg

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { exit(0); } // This template must be included in a calling routine

?>
<span class="type_footer">3675 Kennesaw 75 Pkwy NW </span><span class="type_footer_orange">&bull;</span><span class="type_footer"> Kennesaw, GA 30144 </span><span class="type_footer_orange">|</span><span class="type_footer"> <a href="mailto: service@bigbannersplus.com "> service@bigbannersplus.com </a> </span><span class="type_footer_orange">|</span><span class="type_footer"> Phone <?php echo($local_phoneno); ?> </span><span class="type_footer_orange">|</span><span class="type_footer"> Toll Free <?php echo($local_tollfreephoneno); ?><br />
							</span><span class="type_footer_small"><a title="About" href="/about">About</a></span><span class="footer_word_space"> </span><span class="type_footer_small"><a title="FAQ" href="/faq">FAQ</a></span><span class="footer_word_space"> </span><span class="type_footer_small"><a title="Privacy" href="/privacy">Privacy</a></span><span class="footer_word_space"> </span><span class="type_footer_small"><a title="Terms" href="/terms">Terms</a></span><span class="footer_word_space"> </span><span class="type_footer_small"><a title="Site Map" href="/sitemap">Site Map</a></span><span class="footer_word_space"> </span><span class="type_footer_small">Copyright &copy; BigBannersPlus.com. All rights reserved.</span><span class="type_footer_small"> - Site by <a title="Novatross-IQ" href="http://www.novatross.com" target="_blank">Novatross-IQ</a></span>
							<?php if (getAuthenticationFlag()) { ?>
								<br /><span id="toolbox"></span>
								<?php if ($pageid == 'home') { ?>
									<div class="type_basic" style="width:300px; border-width: 1px; border-color:#D94731; border-style:dashed; margin-top:10px; margin-bottom:20px;" id="<?php echo(fn_makeid('headline')); ?>"><?php echo(lclCUSTOM_fetchC('headline')); ?></div>
								<?php } ?>					
							<?php } ?>
