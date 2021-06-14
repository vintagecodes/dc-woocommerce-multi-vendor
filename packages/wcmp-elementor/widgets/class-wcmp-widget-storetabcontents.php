<?php

class WCMp_Elementor_StoreTabContents extends WCMp_Elementor_StoreName {

    /**
     * Widget name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_name() {
        return 'wcmp-store-tab-contents';
    }

    /**
     * Widget title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Tab Contents', 'dc-woocommerce-multi-vendor' );
    }

    /**
     * Widget icon class
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-products';
    }

    /**
     * Widget categories
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_categories() {
        return [ 'wcmp-store-elements-single' ];
    }

    /**
     * Widget keywords
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_keywords() {
        return [ 'wcmp', 'store', 'vendor', 'tab', 'content', 'products' ];
    }

    /**
     * Register widget controls
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function _register_controls() {
    	global $wcmp_elementor;
        $this->add_control(
            'products',
            [
                'type' => WCMp_Elementor_DynamicHidden::CONTROL_TYPE,
                'dynamic' => [
                    'active' => true,
                    'default' => $wcmp_elementor->wcmp_elementor()->dynamic_tags->tag_data_to_tag_text( null, 'wcmp-store-dummy-products' ),
                ]
            ],
            [
                'position' => [ 'of' => '_title' ],
            ]
        );
    }

    /**
     * Set wrapper classes
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function get_html_wrapper_class() {
        return parent::get_html_wrapper_class() . ' wcmp-store-tab-content elementor-widget-' . parent::get_name();
    }

    /**
     * Frontend render method
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function render() {
        if ( wcmp_is_store_page() ) {
            global $WCMp;
            $store_id = wcmp_find_shop_page_vendor();
            $tab = 'products';
            if ( get_query_var( 'reviews' ) ) {
                $tab = 'reviews';
            }
            if ( get_query_var( 'policies' ) ) {
                $tab = 'policies';
            }
            $vendor_id = $store_id;
            $vendor = get_wcmp_vendor($vendor_id);
            $vendor_products = $vendor ? wp_list_pluck( $vendor->get_products_ids(), 'ID' ) : '';
            $vendor_product_ids_string = is_array($vendor_products) ? implode(",", $vendor_products) : '';

            $is_block = get_user_meta($vendor->id, '_vendor_turn_off' , true);
            if ($is_block) {
                $block_vendor_desc = apply_filters('wcmp_blocked_vendor_text', __('Site Administrator has blocked this vendor', 'dc-woocommerce-multi-vendor'), $vendor);
                ?><p class="blocked_desc"><?php echo esc_html($block_vendor_desc); ?><p><?php
            } else {
                switch( $tab ) {
                    case 'reviews':
                    $WCMp->review_rating->wcmp_seller_review_rating_form();
                    break;

                    case 'policies':
                    $WCMp->frontend->wcmp_vendor_shop_page_policies_endpoint($store_id, $tab);
                    break;
                    
                    default:

                    if (is_array($vendor_products) && !empty($vendor_products) && count($vendor_products) > 0) {
                        echo apply_filters('wcmp_elementor_vendor_product_page', do_shortcode( '[products ids='. $vendor_product_ids_string .' limit="12" paginate="true"]' ), $vendor_id);
                    } else {
                        do_action( 'woocommerce_no_products_found' );
                    }
                    break;
                }
            }

        } else {
            $settings = $this->get_settings_for_display();
            echo $settings['products'];
        }
    }

    /**
     * Elementor builder content template
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function _content_template() {
        ?>
            <#
                print( settings.products );
            #>
        <?php
    }
}
