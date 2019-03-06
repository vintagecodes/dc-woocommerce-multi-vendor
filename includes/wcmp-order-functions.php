<?php

/**
 * WCMp Order Functions
 *
 * Functions for order specific things.
 *
 * @package WCMp/Functions
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get all orders.
 *
 * @since 3.4.0
 * @used-by WC_Order::set_status
 * @return array
 */
function wcmp_get_orders($args = array(), $return_type = 'ids') {
    
    $default = array(
	'posts_per_page'   => -1,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'shop_order',
	'post_status'      => 'any',
	'fields'           => 'ids',
    );
    $args = wp_parse_args($args, $default);
    $query = new WP_Query( $args );
    if(strtolower($return_type) == 'object'){
        $orders = array();
        foreach ($query->get_posts() as $post_id) {
            $orders[$post_id] = wc_get_order($post_id);
        }
        return $orders;
    }
    return $query->get_posts();
}

/**
 * Get Vendor order object.
 *
 * @since 3.4.0
 * @return object/false Vendor order object
 */
function wcmp_get_order($id){
    global $WCMp;
    if($id){
        if(!class_exists('WCMp_Vendor_Order')){
            // Init WCMp Vendor Order class
            $WCMp->load_class('vendor-order');
        }
        $vendor_order = new WCMp_Vendor_Order($id);
        if(!$vendor_order->vendor_id) return false;
        return $vendor_order;
    }else{
        return false;
    }
}

/**
 * Checking order is vendor order or not.
 *
 * @since 3.2.0
 * @return boolean
 */
function is_wcmp_vendor_order($order) {
    $order_id = 0;
    if(is_object($order)){
        $order_id = $order->get_id();
    }else{
        $order_id = absint($order);
    }
    return (wcmp_get_order($order_id)) ? true : false;
}