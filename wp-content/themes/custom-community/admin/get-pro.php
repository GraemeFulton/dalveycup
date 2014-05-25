<?php function get_pro(){ 

	if( defined('is_pro') && current_user_can('edit_theme_options')): 	
		return; 

	else: ?>
		 <div id="cap_getpro">
			<div class="getpro_content">
			    <a href="http://themekraft.com/shop/" title="WordPress Premium Themes and Plugins by ThemeKraft" target="_new">
			    <img src="<?php echo get_template_directory_uri(); ?>/images/go-pro.jpg" width="861" height="830" style="margin:10px 0;" />
			    </a> 
			</div>
		</div>
	    <div class="spacer"></div><?php 
	endif; 
} ?>