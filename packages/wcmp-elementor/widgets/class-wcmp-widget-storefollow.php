<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;

class WCMp_Elementor_StoreFollow extends Widget_Button {

    /**
     * Widget name
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_name() {
        return 'wcmp-store-follow';
    }

    /**
     * Widget title
     *
     * @since 3.7
     *
     * @return string
     */                                                  
    public function get_title() {
        return __( 'Store Follow Button', 'dc-woocommerce-multi-vendor' );
    }

    /**
     * Widget icon class
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_icon() {
        return 'fa fa-child';
    }
    
    /**
     * Widget categories
     *
     * @since 3.7
     *
     * @return array
     */
    public function get_categories() {
        return [ 'wcmp-store-elements-single' ];
    }

    /**
     * Widget keywords
     *
     * @since 3.7
     *
     * @return array
     */
    public function get_keywords() {
        return [ 'wcmp', 'store', 'vendor', 'button', 'follower', 'follow', 'following', 'unfollow' ];
    }

    /**
     * Register widget controls
     *
     * @since 3.7
     *
     * @return void
     */
    protected function _register_controls() {
    	global $wcmp_elementor;
    	  
        parent::_register_controls();
        
        $this->update_control(
            'icon_align',
            [
                'default' => 'left',
            ]
        );

        $this->update_control(
            'button_text_color',
            [
                'default' => '#ffffff',
            ]
        );

        $this->update_control(
            'background_color',
            [
                'default' => '#17a2b8',
            ]
        );

        $this->update_control(
            'border_color',
            [
                'default' => '#17a2b8',
            ]
        );

        $this->update_control(
            'text',
            [
                'dynamic'   => [
                    'default' => $wcmp_elementor->wcmp_elementor()->dynamic_tags->tag_data_to_tag_text( null, 'wcmp-store-follow-tag' ),
                    'active'  => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-widget-container > .elementor-button-wrapper > .wcmp-store-follow-btn' => 'width: auto; margin: 0;',
                ]
            ]
        );
        
        $this->update_control(
            'link',
			[
				'type' => Controls_Manager::URL,
				'default' => [
					'is_external' => 'true',
				],
				'dynamic' => [
					'active' => false,
				],
				'placeholder' => __( 'No link required.', 'dc-woocommerce-multi-vendor' ),
			]
        );
    }

    /**
     * Button wrapper class
     *
     * @since 3.7
     *
     * @return string
     */
    protected function get_button_wrapper_class() {
        return parent::get_button_wrapper_class() . ' wcmp-store-follow-wrap';
    }
    /**
     * Button class
     *
     * @since 3.7
     *
     * @return string
     */
    protected function get_button_class() {
        return 'wcmp-store-follow';
    }

    /**
     * Render button
     *
     * @since 3.7
     *
     * @return void
     */
    protected function render() {
        global $WCMp;

        if ( ! wcmp_is_store_page() ) {
            return;
        }
        $vendor_id = wcmp_find_shop_page_vendor();
        $wcmp_customer_follow_vendor = get_user_meta( get_current_user_id(), 'wcmp_customer_follow_vendor', true ) ? get_user_meta( get_current_user_id(), 'wcmp_customer_follow_vendor', true ) : array();
        $vendor_lists = !empty($wcmp_customer_follow_vendor) ? wp_list_pluck( $wcmp_customer_follow_vendor, 'user_id' ) : array();
        $follow_status = in_array($vendor_id, $vendor_lists) ? __( 'Unfollow', 'dc-woocommerce-multi-vendor' ) : __( 'Follow', 'dc-woocommerce-multi-vendor' );

        $this->add_render_attribute( 'button', 'class', 'wcmp-butn' );
        if (is_user_logged_in()) {
            $this->add_render_attribute( 'button', 'class', 'wcmp-stroke-butn' );
        }
        $this->add_render_attribute( 'button', 'data-vendor_id', $vendor_id );
        $this->add_render_attribute( 'button', 'data-status', $follow_status );			
		
        parent::render();
    }
    
}
