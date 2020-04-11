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
?>
<div class="wcmp-store-list">
    <?php do_action('wcmp_vendor_lists_single_before_image', $vendor->term_id, $vendor->id); ?>
    <div class="wcmp-profile-wrap">
        <div class="wcmp-cover-picture" style="background-image: url('<?php if($banner) echo $banner; ?>');"></div>
        <div class="store-badge-wrap">
            <?php do_action('wcmp_vendor_lists_vendor_store_badges', $vendor); ?>
        </div>
        <div class="wcmp-store-info">
            <div class="wcmp-store-picture">
                <img class="vendor_img" src="<?php echo $image; ?>" id="vendor_image_display">
            </div>
            <?php
                $rating_info = wcmp_get_vendor_review_info($vendor->term_id);
                $WCMp->template->get_template('review/rating_vendor_lists.php', array('rating_val_array' => $rating_info));
            ?>
        </div>
    </div>
    <?php do_action('wcmp_vendor_lists_single_after_image', $vendor->term_id, $vendor->id); ?>
    <div class="wcmp-store-detail-wrap">
        <?php do_action('wcmp_vendor_lists_vendor_before_store_details', $vendor); ?>
        <ul class="wcmp-store-detail-list">
            <li>
                <i class="wcmp-font ico-store-icon"></i>
                <?php $button_text = apply_filters('wcmp_vendor_lists_single_button_text', $vendor->page_title); ?>
                <a href="<?php echo $vendor->get_permalink(); ?>" class="store-name"><?php echo $button_text; ?></a>
                <?php do_action('wcmp_vendor_lists_single_after_button', $vendor->term_id, $vendor->id); ?>
                <?php do_action('wcmp_vendor_lists_vendor_after_title', $vendor); ?>
            </li>
            <?php if($vendor->get_formatted_address()) : ?>
            <li>
                <i class="wcmp-font ico-location-icon2"></i>
                <p><?php echo $vendor->get_formatted_address(); ?></p>
            </li>
            <?php endif; ?>
        </ul>
        <?php do_action('wcmp_vendor_lists_vendor_after_store_details', $vendor); ?>
    </div>
</div>
