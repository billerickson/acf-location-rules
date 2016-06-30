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
 * ACF Rule Type: Parent Page Template
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param array $choices, all of the available rule types
 * @return array
 */
function ea_acf_rule_type_parent_page_template( $choices ) {
	$choices['Page']['parent_page_template'] = 'Parent Page Template';
	return $choices;
}
add_filter( 'acf/location/rule_types', 'ea_acf_rule_type_parent_page_template' );

/**
 * ACF Rule Values: Parent Page Template
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param array $choices, available rule values for this type
 * @return array
 */
function ea_acf_rule_values_parent_page_template( $choices ) {

	$templates = get_page_templates();
	foreach($templates as $k => $v) {
		$choices[$v] = $k;
	}
	return $choices;
}
add_filter( 'acf/location/rule_values/parent_page_template', 'ea_acf_rule_values_parent_page_template' );

/**
 * ACF Rule Match: Parent Page Template
 *
 * @author Bill Erickson
 * @see http://www.billerickson.net/acf-custom-location-rules
 *
 * @param boolean $match, whether the rule matches (true/false)
 * @param array $rule, the current rule you're matching. Includes 'param', 'operator' and 'value' parameters
 * @param array $options, data about the current edit screen (post_id, page_template...)
 * @return boolean $match
 */
function ea_acf_rule_match_parent_page_template( $match, $rule, $options ) {
	
	if ( ! $options['post_id'] || 'page' !== get_post_type( $options['post_id'] ) )
		return false;
		
	$parent = get_post( $options['post_id'] )->post_parent;
	if( empty( $parent ) )
		return false;
		
	$is_template_match = $rule['value'] == get_page_template_slug( $parent );
	
	if ( '==' == $rule['operator'] ) { 
		$match = $is_template_match;
	
	} elseif ( '!=' == $rule['operator'] ) {
		$match = ! $is_template_match;
	}
	
	return $match;

}
add_filter( 'acf/location/rule_match/parent_page_template', 'ea_acf_rule_match_parent_page_template', 10, 3 );
