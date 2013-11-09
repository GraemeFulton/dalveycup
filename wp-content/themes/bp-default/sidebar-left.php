<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div id="sidebar-left">
    <h1> <?php echo get_the_title(); ?> </h1>
    
    
    <?php if ( function_exists( 'display_taxonomy_tree' ) ) { echo display_taxonomy_tree(); } ?>
</div>