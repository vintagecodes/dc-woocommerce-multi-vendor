<?php

/**
 * General coupon tab template
 *
 * Used by add-coupon.php template
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/vendor-dashboard/coupon-manager/views/html-coupon-data-general.php.
 *
 * HOWEVER, on occasion WCMp will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		WC Marketplace
 * @package 	WCMp/templates/vendor dashboard/coupon manager/views
 * @version     3.3.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div role="tabpanel" class="tab-pane fade active in" id="general_coupon_data">
    <div class="row-padding">
        <?php do_action( 'wcmp_afm_before_general_coupon_data', $post->ID, $coupon ); ?>
        <div class="form-group-row"> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="discount_type"><?php esc_html_e( 'Discount type', 'woocommerce' ); ?></label>
                <div class="col-md-6 col-sm-9">
                    <select class="form-control" id="discount_type" name="discount_type">
                        <?php
                        $coupon_types = wc_get_coupon_types();
                        $coupon_type = $coupon->get_discount_type( 'edit' );
                        foreach ( $coupon_types as $key => $value ) {
                            echo '<option value="' . $key . '" ' . selected( $coupon_type, $key, false ) . '>' . $value . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="coupon_amount">
                    <?php esc_html_e( 'Coupon amount', 'woocommerce' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'Value of the coupon.', 'woocommerce' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input id="coupon_amount" name="coupon_amount" value="<?php esc_attr_e( $coupon->get_amount( 'edit' ) ); ?>" type="text" placeholder="<?php esc_attr_e( wc_format_localized_price( 0 ) ); ?>" class="form-control">
                </div>
            </div> 
            <?php
            // Free Shipping.
            if ( wc_shipping_enabled() ) :
                ?>
                <!--div class="form-group">
                    <label class="control-label col-sm-3 col-md-3" for="free_shipping"><?php esc_html_e( 'Allow free shipping', 'woocommerce' ); ?></label>
                    <div class="col-md-6 col-sm-9">
                        <input id="free_shipping" name="free_shipping" type="checkbox" class="form-control" value="yes" <?php checked( wc_bool_to_string( $coupon->get_free_shipping( 'edit' ) ), 'yes' ); ?>>
                        <span class="form-text"><?php echo sprintf( __( 'Check this box if the coupon grants free shipping. A <a href="%s" target="_blank">free shipping method</a> must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'woocommerce' ), 'https://docs.woocommerce.com/document/free-shipping/' ); ?></span>
                    </div>
                </div--> 
            <?php endif; ?>
            <?php
            // Expiry date.
            $expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( 'Y-m-d' ) : '';
            ?>
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="expiry_date"><?php esc_html_e( 'Coupon expiry date', 'woocommerce' ); ?></label>
                <div class="col-md-6 col-sm-9">
                    <input class="form-control date-picker" id="expiry_date" name="expiry_date" value="<?php esc_attr_e( $expiry_date ); ?>" type="text" placeholder="<?php esc_attr_e( 'YYYY-MM-DD' ); ?>" pattern="<?php esc_attr_e( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ); ?>">
                </div>
            </div>
        </div>
        <?php do_action( 'wcmp_afm_after_general_coupon_data', $post->ID, $coupon ); ?>
    </div>
</div>