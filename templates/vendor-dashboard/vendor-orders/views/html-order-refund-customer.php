<?php
/**
 * Order details items template.
 *
 * Used by vendor-order-details.php template
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-orders/views/html-order-refund-customer.php.
 * 
 * @author 	WC Marketplace
 * @package 	WCMp/templates/vendor dashboard/vendor orders/views
 * @version     3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $WCMp;
?>
<div class="panel panel-default panel-pading pannel-outer-heading cust-refund-request">
    <div class="panel-heading">
        <h3><?php _e('Customer Refund Request', 'dc-woocommerce-multi-vendor'); ?></h3>
    </div>
    <div class="panel-body panel-content-padding">
        <form method="post">
        <div class="form-group mb-0">
            <?php 
            $refund_status = get_post_meta( $order->get_id(), '_customer_refund_order', true ) ? get_post_meta( $order->get_id(), '_customer_refund_order', true ) : '';
            $refund_statuses = array( 
                '' => __( 'Refund Status','dc-woocommerce-multi-vendor' ),
                'refund_request' => __( 'Refund Requested', 'dc-woocommerce-multi-vendor' ), 
                'refund_accept' => __( 'Refund Accepted','dc-woocommerce-multi-vendor' ), 
                'refund_reject' => __( 'Refund Rejected','dc-woocommerce-multi-vendor' ) 
            );
            ?>
            <select id="refund_order_customer" name="refund_order_customer">
                <?php foreach ( $refund_statuses as $key => $value ) { ?>
                <option value="<?php echo $key; ?>" <?php selected( $refund_status, $key ); ?> ><?php echo $value; ?></option>
                <?php } ?>
            </select>
            <button class="button grant_access btn btn-default" name="update_cust_refund_status"><?php echo __('Update status', 'dc-woocommerce-multi-vendor'); ?></button>
        </div>
        </form>
    </div>
</div>