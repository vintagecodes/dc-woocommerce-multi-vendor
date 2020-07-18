<?php
/**
 * WCMp Vendor Product Search Widget
 *
 * @author    WC Marketplace
 * @category  Widgets
 * @package   WCMp/Widgets
 * @version   3.5.4
 * @extends   WC_Widget
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget product search class.
 */
class WCMp_Widget_Vendor_Product_Search extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'wcmp-vproduct-search woocommerce widget_vproduct_search';
		$this->widget_description = __( 'A search form for vendor store products search.', 'dc-woocommerce-multi-vendor' );
		$this->widget_id          = 'wcmp_vendor_product_search';
		$this->widget_name        = __( 'WCMp: Vendor Product Search', 'dc-woocommerce-multi-vendor' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'dc-woocommerce-multi-vendor' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
        global $WCMp;

        if ( !is_tax( $WCMp->taxonomy->taxonomy_name ) ) return;

        $this->widget_start( $args, $instance );
        
        ob_start();

		do_action( 'wcmp_widget_before_vendor_product_search_form' );

		$WCMp->template->get_template('widget/vendor-product-searchform.php');

        $form = apply_filters( 'wcmp_widget_vendor_product_search_form', ob_get_clean() );
        
        echo $form;

		$this->widget_end( $args );
	}
}
