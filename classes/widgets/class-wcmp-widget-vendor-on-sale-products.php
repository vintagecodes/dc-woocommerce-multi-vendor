<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCMp_Widget_Vendor_On_Sale_Products extends WC_Widget {

	public $vendor_term_id;

    public function __construct() {
        $this->widget_cssclass = 'wcmp_vendor_on_sale_products';
        $this->widget_description = __('Displays a list of vendor on sale products on the vendor shop page.', 'dc-woocommerce-multi-vendor');
        $this->widget_id = 'wcmp_vendor_on_sale_products';
        $this->widget_name = __('WCMp: Vendor\'s On Sale Products', 'dc-woocommerce-multi-vendor');
        $this->settings = array(
            'title' => array(
                'type' => 'text',
                'std' => __('Vendor on sale products', 'dc-woocommerce-multi-vendor'),
                'label' => __('Title', 'dc-woocommerce-multi-vendor'),
            ),
            'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 3,
				'label' => __( 'Number of products to show', 'dc-woocommerce-multi-vendor' ),
			),
        );
        parent::__construct();
    }

    public function widget($args, $instance) {
        global $wp_query, $WCMp;
        
        $this->vendor_term_id = ( isset( $wp_query->queried_object->term_id ) ) ? $wp_query->queried_object->term_id : 0;
        $vendor = get_wcmp_vendor_by_term($this->vendor_term_id);
        if ((!is_tax($WCMp->taxonomy->taxonomy_name) && !$vendor)) {
            return;
        }

        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

        $query_args = array(
            'posts_per_page' => $number,
            'post_status'    => 'publish',
            'post_type'      => 'product',
            'author'         => $vendor->id,
            'no_found_rows'  => 1,
            'order'          => 'DESC',
            'orderby'        => 'date',
        );

        $product_ids_on_sale    = wc_get_product_ids_on_sale();
        $product_ids_on_sale[]  = 0;
        $query_args['post__in'] = $product_ids_on_sale;
        
        $products = new WP_Query( apply_filters( 'woocommerce_products_widget_query_args', $query_args ) );
        
        if ( $products && $products->have_posts() ) {
            
            $this->widget_start( $args, $instance );;
            
            do_action($this->widget_cssclass . '_top', $vendor);

            echo wp_kses_post( apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' ) );

            $template_args = array(
                'widget_id'   => $args['widget_id'],
                //'show_rating' => true,
            );

            while ( $products->have_posts() ) {
                $products->the_post();
                wc_get_template( 'content-widget-product.php', $template_args );
            }

            echo wp_kses_post( apply_filters( 'woocommerce_after_widget_product_list', '</ul>' ) );
            
            do_action($this->widget_cssclass . '_bottom', $vendor);

            $this->widget_end( $args );

        }
    }
}