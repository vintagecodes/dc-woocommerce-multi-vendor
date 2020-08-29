<?php
/**
 * Vendor List Map
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/shortcode/vendor-list/content-vendor.php
 *
 * @package WCMp/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCMp, $vendor_list;
$vendor = get_wcmp_vendor( $vendor_id );
$image = $vendor->get_image() ? $vendor->get_image('image', array(125, 125)) : $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
$banner = $vendor->get_image('banner') ? $vendor->get_image('banner') : '';
$rating_info = wcmp_get_vendor_review_info($vendor->term_id);
$rating = round( $rating_info['avg_rating'], 2 );
$review_count = intval( $rating_info['total_rating'] );
$vendor_phone = $vendor->phone ? $vendor->phone : __('No number yet','dc-woocommerce-multi-vendor');
?>

<div class="wcmp-store-list wcmp-store-list-vendor">
    <?php do_action('wcmp_vendor_lists_single_before_image', $vendor->term_id, $vendor->id); ?>
    <div class="wcmp-vendorblocks">
        <div class="wcmp-vendor-details">
            <div class="vendor-heading">
                <div class="wcmp-store-picture">
                    <img class="vendor_img" src="<?php echo $image; ?>" id="vendor_image_display">
                    <div class="wcmp-vendor-name">
                        <?php $button_text = apply_filters('wcmp_vendor_lists_single_button_text', $vendor->page_title); ?>
                    </div>
                </div>
                <a href="<?php echo $vendor->get_permalink(); ?>" class="store-name"><?php echo $button_text; ?></a>
                <?php do_action('wcmp_vendor_lists_single_after_button', $vendor->term_id, $vendor->id); ?>
                <?php do_action('wcmp_vendor_lists_vendor_after_title', $vendor); ?>
            </div>
            <!-- star rating -->
            <div class="wcmp-rating-block">
                <div class="wcmp-rating-rate"><?php echo $rating; ?></div>
                <?php
                $WCMp->template->get_template('review/rating_vendor_lists.php', array('rating_val_array' => $rating_info));
                ?>
                <div class="wcmp-rating-review"><?php echo $review_count; ?></div>
            </div>
            <!-- star rating -->
            <div class="add-call-block">
                <div class="wcmp-detail-block">
                    <i class="wcmp-font ico-call_icon"></i>
                    <span class="vendor-call"><?php echo $vendor_phone; ?></span>
                </div>
                <div class="wcmp-detail-block">
                    <i class="wcmp-font ico-at_icon" aria-hidden="true"></i>
                    <span class="add-address">
                        <?php echo substr( $vendor->get_formatted_address(), 0, 10 ). '...' ; ?>
                    </span>
                </div>
                <?php if( $vendor->description ) : ?>
                <div class="wcmp-detail-block">
                    <i class="wcmp-font ico-location-icon2" aria-hidden="true"></i>
                    <span class=""><?php echo substr( $vendor->description, 0, 10 ). '...'; ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php do_action('wcmp_vendor_lists_vendor_top_products', $vendor); ?>
            <a href="<?php echo esc_url($vendor->permalink); ?>" class="wcmp-contactNow"><?php echo __( 'Contact Now' , 'dc-woocommerce-multi-vendor' ); ?></a>
        </div>
    </div>
</div> 