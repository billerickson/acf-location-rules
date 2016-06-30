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
 * ACF Rule Values: Page Type
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param array $choices, available rule values for this type
 * @return array
 */
function ea_acf_rule_values_page_type( $choices ) {
	$choices['no_children'] = 'No Children';
	return $choices;
}
add_filter( 'acf/location/rule_values/page_type', 'ea_acf_rule_values_page_type' ); 

/**
 * ACF Rule Match: Page Type
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param boolean $match, whether the rule matches (true/false)
 * @param array $rule, the current rule you're matching. Includes 'param', 'operator' and 'value' parameters
 * @param array $options, data about the current edit screen (post_id, page_template...)
 * @return boolean $match
 */
function ea_acf_rule_match_page_type( $match, $rule, $options ) {

	// Only run for 'no_children' value
	if ( 'no_children' != $rule['value'] ) {
		return $match;
	}
		
	// Only run if post ID is defined and this is a page
	if ( ! $options['post_id'] || 'page' != get_post_type( $options['post_id'] ) ) {
		return $match;
	}

	$children = get_pages( array( 'child_of' => get_the_ID() ) );
	
	if ( '==' == $rule['operator'] ) {
		$match = empty( $children );
	
	} elseif ( '!=' == $rule['operator'] ) {
		$match = ! empty( $children );
	}
	
	return $match;
	
}
add_filter( 'acf/location/rule_match/page_type', 'ea_acf_rule_match_page_type', 10, 3 );