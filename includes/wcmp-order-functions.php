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
 * @since 3.4.0
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

/**
 * Get total refunded commission amount associated with refund.
 *
 * @since 3.4.0
 * @return boolean
 */
function get_refund_commission_amount($refund_id, $context = 'view') {
    if( $refund_id ){
        $order_id = wp_get_post_parent_id( $refund_id );
        $commission_id = get_post_meta( $order_id, '_commission_id', true );
        $commission_refunded_data = get_post_meta( $commission_id, '_commission_refunded_data', true );
        if( isset($commission_refunded_data[$refund_id][$commission_id]) ){
            $refund_commission_data = $commission_refunded_data[$refund_id][$commission_id];
            return array_sum($refund_commission_data);
            //return $context == 'view' ? wc_price($refund_commission, array('currency' => $order->get_currency())) : $refund_commission;
        }
    }
    return false;
}

/**
 * Get total refunded item commission amount associated with refund.
 *
 * @since 3.4.0
 * @return boolean
 */
function wcmp_get_total_refunded_for_item( $item_id, $order_id ) {
    if( $item_id && $order_id ) {
        $commission_id = get_post_meta( $order_id, '_commission_id', true );
        $commission_refunded_items_data = get_post_meta( $commission_id, '_commission_refunded_items_data', true );
        $refunds = wc_get_orders(
            array(
                'type'   => 'shop_order_refund',
                'parent' => $order_id,
                'limit'  => -1,
            )
        );
        $item_total = 0;
        if($refunds){
            foreach ( $refunds as $refund ) {
                foreach ( $refund->get_items( 'line_item' ) as $refunded_item ) {
                    if ( absint( $refunded_item->get_meta( '_refunded_item_id' ) ) === $item_id ) {
                        if( isset($commission_refunded_items_data[$refund->get_id()][$item_id]) )
                            $item_total += $commission_refunded_items_data[$refund->get_id()][$item_id];
                    }
                }
            }
        }
        return $item_total;
    }
    return false;
}