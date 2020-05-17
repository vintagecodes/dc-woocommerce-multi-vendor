<?php

class WCMp_Settings_WCMp_Reports {

	/**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

    /**
     * Start up
     */
    public function __construct($tab) {
    	$screen = get_current_screen();
    	
        $this->tab = $tab;
		$this->options = get_option("wcmp_{$this->tab}_settings_name");		
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp, $wp_version;

        $reports        = apply_filters('wcmp_backend_sales_report_tabs', array(	
			                "overview" => array(
			                    'title' => __('Overview', 'dc-woocommerce-multi-vendor'),
			                    'description' => '',
			                    'hide_title' => true,
			                    'callback' => array(__CLASS__, 'wcmp_get_report')
			                ),
			                "admin_overview" => array(
			                    'title' => __('Overview', 'dc-woocommerce-multi-vendor'),
			                    'description' => '',
			                    'hide_title' => true,
			                    'callback' => array(__CLASS__, 'wcmp_get_report')
			                ),
			                "vendor" => array(
			                    'title' => __('Vendor', 'dc-woocommerce-multi-vendor'),
			                    'description' => '',
			                    'hide_title' => true,
			                    'callback' => array(__CLASS__, 'wcmp_get_report')
			                ),
			                "product" => array(
			                    'title' => __('Product', 'dc-woocommerce-multi-vendor'),
			                    'description' => '',
			                    'hide_title' => true,
			                    'callback' => array(__CLASS__, 'wcmp_get_report')
			                ),
			                "banking_overview" => array(
			                    'title' => __('Banking Overview', 'dc-woocommerce-multi-vendor'),
			                    'description' => '',
			                    'hide_title' => true,
			                    'callback' => array(__CLASS__, 'wcmp_get_report')
			                )	
        				));

        if( !is_user_wcmp_vendor(get_current_user_id() ) ) {
            if( isset( $reports['overview'] ) ){
                unset( $reports['overview'] );
            }
        }

		$first_tab      = array_keys( $reports );
		$current_tab    = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $reports ) ? sanitize_title( $_GET['tab'] ) : $first_tab[0];
        ?>
		<div class="wrap woocommerce">
			<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
				<?php
				foreach ( $reports as $key => $report_group ) {
					echo '<a href="' . admin_url( 'admin.php?page=reports&tab=' . urlencode( $key ) ) . '" class="nav-tab ';
					if ( $current_tab == $key ) {
						echo 'nav-tab-active';
					}
					echo '">' . esc_html( $report_group['title'] ) . '</a>';
				}

				do_action( 'wc_reports_tabs' );
				?>
			</nav>
			<?php

			if ( isset( $reports[ $current_tab ] ) ) {
				$report = $reports[ $current_tab ];

				if ( ! isset( $report['hide_title'] ) || true != $report['hide_title'] ) {
					echo '<h1>' . esc_html( $report['title'] ) . '</h1>';
				} else {
					echo '<h1 class="screen-reader-text">' . esc_html( $report['title'] ) . '</h1>';
				}

				if ( $report['description'] ) {
					echo '<p>' . $report['description'] . '</p>';
				}

				if ( $report['callback'] && ( is_callable( $report['callback'] ) ) ) {
					call_user_func( $report['callback'], $current_tab );
				}
			}
			?>
		</div>
		<?php
    }

    /**
	 * Get a report from our reports folder.
	 *
	 * @param string $name
	 */
	public static function wcmp_get_report($name) {
        $name = sanitize_title(str_replace('_', '-', $name));
        $class = 'WCMp_Report_' . ucfirst(str_replace('-', '_', $name));
       
        if (!class_exists('WC_Admin_Report'))
        		include(WC_ABSPATH . 'includes/admin/reports/class-wc-admin-report.php');
        require_once( apply_filters('wcmp_admin_reports_path', dirname(plugin_dir_path(__FILE__)) .'/classes/reports/class-wcmp-report-' . $name . '.php', $name, $class) );

        if (!class_exists($class))
            return;
        $report = new $class();
        $report->output_report();
    }


}