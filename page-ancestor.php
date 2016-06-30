<?php
/**
 * Core Functionality Plugin
 * 
 * @package    CoreFunctionality
 * @since      1.0.0
 * @copyright  Copyright (c) 2014, Bill Erickson & Jared Atchison
 * @license    GPL-2.0+
 */

/**
 * ACF Rule Type: Page Ancestor
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param array $choices, all of the available rule types
 * @return array
 */
function ea_acf_rule_type_page_ancestor( $choices ) {
	$choices['Page']['page_ancestor'] = 'Page Ancestor';
	return $choices;
}
add_filter( 'acf/location/rule_types', 'ea_acf_rule_type_page_ancestor' );

/**
 * ACF Rule Values: Page Ancestor
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param array $choices, available rule values for this type
 * @return array
 */
function ea_acf_rule_values_page_ancestor( $choices ) {

	// Copied from acf/core/controllers/field_group.php
	// @see https://github.com/elliotcondon/acf/blob/8ffdf88889c8c81e7f628e8e1ef95c6de17eb02d/core/controllers/field_group.php#L580
	
	$post_type = 'page';
	$posts = get_posts(array(
		'posts_per_page'			=>	-1,
		'post_type'					=> $post_type,
		'orderby'					=> 'menu_order title',
		'order'						=> 'ASC',
		'post_status'				=> 'any',
		'suppress_filters'			=> false,
		'update_post_meta_cache'	=> false,
	));
	
	if( $posts )
	{
		// sort into hierachial order!
		if( is_post_type_hierarchical( $post_type ) )
		{
			$posts = get_page_children( 0, $posts );
		}
		
		foreach( $posts as $page )
		{
			$title = '';
			$ancestors = get_ancestors($page->ID, 'page');
			if($ancestors)
			{
				foreach($ancestors as $a)
				{
					$title .= '- ';
				}
			}
			
			$title .= apply_filters( 'the_title', $page->post_title, $page->ID );
			
			
			// status
			if($page->post_status != "publish")
			{
				$title .= " ($page->post_status)";
			}
			
			$choices[ $page->ID ] = $title;
			
		}
	
	}
	
	return $choices;
}
add_filter( 'acf/location/rule_values/page_ancestor', 'ea_acf_rule_values_page_ancestor' );

/**
 * ACF Rule Match: Page Ancestor
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param boolean $match, whether the rule matches (true/false)
 * @param array $rule, the current rule you're matching. Includes 'param', 'operator' and 'value' parameters
 * @param array $options, data about the current edit screen (post_id, page_template...)
 * @return boolean $match
 */
function ea_acf_rule_match_page_ancestor( $match, $rule, $options ) {
	
	if ( ! $options['post_id'] || 'page' !== get_post_type( $options['post_id'] ) )
		return false;
		
	$ancestors = get_ancestors( $options['post_id'], 'page' );
	$is_ancestor = in_array( $rule['value'], $ancestors );
	
	if ( '==' == $rule['operator'] ) { 
		$match = $is_ancestor;
	
	} elseif ( '!=' == $rule['operator'] ) {
		$match = ! $is_ancestor;
	}
	
	return $match;

}
add_filter( 'acf/location/rule_match/page_ancestor', 'ea_acf_rule_match_page_ancestor', 10, 3 );
