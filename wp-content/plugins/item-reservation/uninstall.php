<?php
//if uninstall/delete not called from WordPress exit
if( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();
else
	pluginUninstall();
// Delete options array from options table

function pluginUninstall() {

	global $wpdb;
	global $tablename;
	global $kit_post_type;
	//Table to delete
	$poststable = "wp_posts";
	//Delete options for plugin
	delete_option( 'glkit_options' );

	//Delete Taxonomies before item
	remove_custom_taxonomies();

	//Remove custom posts
	$kit_custom_posts = get_posts( array( 'post_type' => 'kit_item') );

	foreach( $kit_custom_posts as $kit_post ) {		
		
		//Delete Meta Values		
		delete_post_meta($kit_post->ID, 'gift_sku' );
		delete_post_meta($kit_post->ID, 'gift_price' );
		delete_post_meta($kit_post->ID, 'gift_colour' );
		delete_post_meta($kit_post->ID, 'gift_supplier' );
		delete_post_meta($kit_post->ID, 'gift_url' );
		delete_post_meta($kit_post->ID, 'gift_required' );
		delete_post_meta($kit_post->ID, 'gift_description' );

    	// Delete's each post.
		wp_delete_post($kit_post->ID, true);
		// Set to False if you want to send them to Trash.
	}

	//Drop the table
	$wpdb->query("DROP TABLE IF EXISTS $tablename");
}

function remove_custom_taxonomies() {
	global $wp_taxonomies;

	$taxonomies = array(
			'gift_price'
	);
	$args = array(
			'orderby'       => 'name',
			'order'         => 'ASC',
			'hide_empty'    => false,
			'exclude'       => array(),
			'exclude_tree'  => array(),
			'include'       => array(),
			'number'        => '',
			'fields'        => 'all',
			'slug'          => '',
			'parent'         => '',
			'hierarchical'  => 0,
			'child_of'      => 0,
			'get'           => 'all',
			'name__like'    => '',
			'pad_counts'    => false,
			'offset'        => '',
			'search'        => '',
			'cache_domain'  => 'core'
	);
	//ToDo Only returning attached taxonomies
	$terms = get_terms( $taxonomies);

	if($terms!=false)
	{
		foreach( $terms as $term ) {
			//Delete term
			wp_delete_term($term->term_id,'gift_price');
		}
	}
	$taxonomies = array(
			'gift_type'
	);
	$args = array(
			'orderby'       => 'name',
			'order'         => 'ASC',
			'hide_empty'    => false,
			'exclude'       => array(),
			'exclude_tree'  => array(),
			'include'       => array(),
			'number'        => '',
			'fields'        => 'all',
			'slug'          => '',
			'parent'         => '',
			'hierarchical'  => 0,
			'child_of'      => 0,
			'get'           => 'all',
			'name__like'    => '',
			'pad_counts'    => false,
			'offset'        => '',
			'search'        => '',
			'cache_domain'  => 'core'
	);
	//ToDo Only returning attached taxonomies
	$terms = get_terms( $taxonomies);
	
	if($terms!=false)
	{
		foreach( $terms as $term ) {
			//Delete term
			wp_delete_term($term->term_id,'gift_type');
		}
	}

	$taxonomy = 'gift_type';

	if ( taxonomy_exists( $taxonomy))
	{
		$id = get_taxonomy($taxonomy);

		unset( $wp_taxonomies[$taxonomy]);
	}
	
	$taxonomy = 'gift_price';

	if ( taxonomy_exists( $taxonomy))
	{
		unset( $wp_taxonomies[$taxonomy]);
	}
}
