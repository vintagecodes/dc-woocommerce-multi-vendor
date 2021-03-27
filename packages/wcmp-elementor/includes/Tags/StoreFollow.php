<?php

class StoreFollow extends WCMp_Elementor_TagBase {

    /**
     * Class constructor
     *
     * @since 3.7
     *
     * @param array $data
     */
    public function __construct( $data = [] ) {
        parent::__construct( $data );
    }

    /**
     * Tag name
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_name() {
        return 'wcmp-store-follow-tag';
    }

    /**
     * Tag title
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Follow Button', 'dc-woocommerce-multi-vendor' );
    }

    /**
     * Render tag
     *
     * @since 3.7
     *
     * @return void
     */
    public function render() {
    	global $WCMp;
    	
        if ( wcmp_is_store_page() ) {
            $vendor_id = wcmp_find_shop_page_vendor();
            $wcmp_customer_follow_vendor = get_user_meta( get_current_user_id(), 'wcmp_customer_follow_vendor', true ) ? get_user_meta( get_current_user_id(), 'wcmp_customer_follow_vendor', true ) : array();
            $vendor_lists = !empty($wcmp_customer_follow_vendor) ? wp_list_pluck( $wcmp_customer_follow_vendor, 'user_id' ) : array();
            $follow_status = in_array($vendor_id, $vendor_lists) ? __( 'Unfollow', 'dc-woocommerce-multi-vendor' ) : __( 'Follow', 'dc-woocommerce-multi-vendor' );
        	echo is_user_logged_in() ? esc_attr($follow_status) : esc_html_e('You must logged in to follow', 'dc-woocommerce-multi-vendor');
        } else {
            echo esc_html_e( 'Follow', 'dc-woocommerce-multi-vendor' );
        }
    }
}
