<?php

/**
 * Vendor List Map
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/shortcode/vendor-list/content-vendor.php
 *
 * @package WCMp/Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $WCMp, $vendor_list;
$vendor = get_wcmp_vendor($vendor_id);
$image = $vendor->get_image() ? $vendor->get_image('image', array(125, 125)) : $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
$rating_info = wcmp_get_vendor_review_info($vendor->term_id);
$rating = round($rating_info['avg_rating'], 2);
$review_count = empty(intval($rating_info['total_rating'])) ? '' : intval($rating_info['total_rating']);
$vendor_phone = $vendor->phone ? $vendor->phone : __('No number yet', 'dc-woocommerce-multi-vendor');
?>
<div class="wcmp-store-list wcmp-store-list-vendor">
    <?php do_action('wcmp_vendor_lists_single_before_image', $vendor->term_id, $vendor->id); ?>
    <div class="wcmp-vendorblocks">
        <div class="wcmp-vendor-details">
            <div class="vendor-heading">
                <div class="wcmp-store-picture">
                    <img class="vendor_img" src="<?php echo $image; ?>" id="vendor_image_display">

                </div>
                <div class="vendor-header-icon">
                    <div class="dashicons dashicons-phone">
                        <div class="on-hover-cls">
                            <p><?php echo $vendor_phone; ?></p>
                        </div>
                    </div>
                    <div class="dashicons dashicons-location">
                        <div class="on-hover-cls">
                        <p><?php echo $vendor->get_formatted_address() ? substr($vendor->get_formatted_address(), 0, 10) : __('No Address found', 'dc-woocommerce-multi-vendor'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wcmp-vendor-name">
                <a href="<?php echo $vendor->get_permalink(); ?>" class="store-name"><?php echo $vendor->page_title; ?></a>
                <?php do_action('wcmp_vendor_lists_single_after_button', $vendor->term_id, $vendor->id); ?>
                <?php do_action('wcmp_vendor_lists_vendor_after_title', $vendor); ?>
            </div>
            <!-- star rating -->
            <div class="wcmp-rating-block extraCls">
                <div class="wcmp-rating-rate"><?php echo $rating; ?></div>
                <?php
                $WCMp->template->get_template('review/rating_vendor_lists.php', array('rating_val_array' => $rating_info));
                ?>
                <div class="wcmp-rating-review"><?php echo $review_count; ?></div>
            </div>
            <!-- vendor description -->
            <div class="add-call-block">
                <div class="wcmp-detail-block"></div>
                <div class="wcmp-detail-block"></div>
                <?php if ($vendor->description) : ?>
                    <div class="wcmp-detail-block">
                        <i class="wcmp-font ico-location-icon2" aria-hidden="true"></i>
                        <span class="descrptn_txt"><?php echo substr($vendor->description, 0, 10) . '...'; ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <?php do_action('wcmp_vendor_lists_vendor_top_products', $vendor); ?>
        </div>
    </div>
</div>