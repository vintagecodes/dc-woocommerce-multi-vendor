<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCMp_Widget_Vendor_Top_Rated_Products extends WC_Widget {

	public $vendor_term_id;

    public function __construct() {
        $this->widget_cssclass = 'wcmp woocommerce wcmp_widget_vendor_top_rated_products widget_top_rated_products';
        $this->widget_description = __('Displays a list of vendor top-rated products on the vendor shop page.', 'dc-woocommerce-multi-vendor');
        $this->widget_id = 'wcmp_vendor_top_rated_products';
        $this->widget_name = __('WCMp: Vendor\'s Products by Rating', 'dc-woocommerce-multi-vendor');
        $this->settings = array(
            'title' => array(
                'type' => 'text',
                'std' => __('Vendor top rated products', 'dc-woocommerce-multi-vendor'),
                'label' => __('Title', 'dc-woocommerce-multi-vendor'),
            ),
            'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of products to show', 'dc-woocommerce-multi-vendor' ),
			),
        );
        parent::__construct();
    }

    public function widget($args, $instance) {
        global $wp_query, $WCMp;
        if ( $this->get_cached_widget( $args ) ) {
                return;
        }
        
        $enable_vshop_only = apply_filters( 'wcmp_vendor_top_rated_products_widget_shows_only_vendor_shop', true );	
        if ( $enable_vshop_only && !is_tax( $WCMp->taxonomy->taxonomy_name ) ) return;
        if( !is_woocommerce() ) return;
        $author_in = array();
        if( !$enable_vshop_only ){
            // Get all active vendors
            $query_args = wp_parse_args( apply_filters( 'wcmp_vendor_top_rated_products_author_user_query', array() ), array('role' => 'dc_vendor', 'fields' => 'ids', 'orderby' => 'registered', 'meta_key' => '_vendor_turn_off', 'meta_value' => '', 'meta_compare' => 'NOT EXISTS'));
            $user_query = new WP_User_Query( $query_args );
            if( is_woocommerce() && is_tax( $WCMp->taxonomy->taxonomy_name ) ){
                $this->vendor_term_id = $wp_query->queried_object->term_id;
                $vendor = get_wcmp_vendor_by_term($this->vendor_term_id);
                $author_in[] = $vendor->id;
            }elseif( is_product() ){
                global $product;
                $vendor = get_wcmp_product_vendors( $product->get_id() );
                if( !$vendor ) $author_in = $user_query->results;
                else $author_in[] = $vendor->id;
            }elseif( is_woocommerce() ){
                $author_in = $user_query->results;
            }
        }else{
            $this->vendor_term_id = $wp_query->queried_object->term_id;
            $vendor = get_wcmp_vendor_by_term($this->vendor_term_id);
            $author_in[] = $vendor->id;
        }

        ob_start();

        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

        $query_args = array(
                'posts_per_page' => $number,
                'no_found_rows'  => 1,
                'post_status'    => 'publish',
                'post_type'      => 'product',
                'author__in'	 => apply_filters( 'wcmp_vendor_top_rated_products_author_in', $author_in ),
                'meta_key'       => '_wc_average_rating',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
                'meta_query'     => WC()->query->get_meta_query(),
                'tax_query'      => WC()->query->get_tax_query(),
        ); // WPCS: slow query ok.

        $r = new WP_Query( $query_args );

        if ( $r->have_posts() ) {
                $instance['title'] = apply_filters( 'wcmp_vendor_top_rated_products_widget_title', $this->settings['title']['std'] );
                $this->widget_start( $args, $instance );

                echo wp_kses_post( apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' ) );

                $template_args = array(
                        'widget_id'   => $args['widget_id'],
                        'show_rating' => true,
                );

                while ( $r->have_posts() ) {
                        $r->the_post();
                        wc_get_template( 'content-widget-product.php', $template_args );
                }

                echo wp_kses_post( apply_filters( 'woocommerce_after_widget_product_list', '</ul>' ) );

                $this->widget_end( $args );
        }

        wp_reset_postdata();

        $content = ob_get_clean();

        echo $content; // WPCS: XSS ok.

        $this->cache_widget( $args, $content );
    }
}
