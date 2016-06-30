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
 * ACF Rule Type: Post Category Ancestor
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param array $choices, all of the available rule types
 * @return array
 */
function ea_acf_rule_type_post_category_ancestor( $choices ) {
	$choices['Post']['post_category_ancestor'] = 'Post Category Ancestor';
	return $choices;
}
add_filter( 'acf/location/rule_types', 'ea_acf_rule_type_post_category_ancestor' );

/**
 * ACF Rule Values: Post Category Ancestor
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param array $choices, available rule values for this type
 * @return array
 */
function ea_acf_rule_values_post_category_ancestor( $choices ) {

	$terms = acf_get_taxonomy_terms( 'category' );
	$choices = $terms['Category'];
				
	return $choices;
}
add_filter( 'acf/location/rule_values/post_category_ancestor', 'ea_acf_rule_values_post_category_ancestor' );

/**
 * ACF Rule Match: Post Category Ancestor
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param boolean $match, whether the rule matches (true/false)
 * @param array $rule, the current rule you're matching. Includes 'param', 'operator' and 'value' parameters
 * @param array $options, data about the current edit screen (post_id, page_template...)
 * @return boolean $match
 */
function ea_acf_rule_match_post_category_ancestor( $match, $rule, $options ) {

	if ( ! ( $rule['value'] && $options['post_id'] && is_object_in_taxonomy( get_post_type( $options['post_id'] ), 'category' ) ) )
		return false;
		
	$ancestor = explode( ':', $rule['value'] );
	$ancestor = get_term_by( 'slug', $ancestor[1], $ancestor[0] );
	
	$is_ancestor = false;
	$terms = get_the_terms( $options['post_id'], 'category' );
	foreach( $terms as $term ) {
		if( cat_is_ancestor_of( $ancestor->term_id, $term->term_id ) ) {
			$is_ancestor = true;
		}
	}
	
	if ( '==' == $rule['operator'] ) { 
		$match = $is_ancestor;
	
	} elseif ( '!=' == $rule['operator'] ) {
		$match = ! $is_ancestor;
	}
	
	return $match;

}
add_filter( 'acf/location/rule_match/post_category_ancestor', 'ea_acf_rule_match_post_category_ancestor', 10, 3 );