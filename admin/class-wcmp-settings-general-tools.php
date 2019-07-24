<?php

class WCMp_Settings_General_Tools {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmp_{$this->tab}_{$this->subsection}_settings_name");
        $this->settings_page_init();
        $this->settings_tools_action();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;
        ?>
        <table class="wcmp_tools_table wcmp_tools_table--tools widefat" cellspacing="0">
		<tbody class="tools">
			<?php foreach ( $this->get_tools() as $action => $tool ) : ?>
				<tr class="<?php echo sanitize_html_class( $action ); ?>">
					<th>
						<strong class="name"><?php echo esc_html( $tool['name'] ); ?></strong>
						<p class="description"><?php echo wp_kses_post( $tool['desc'] ); ?></p>
					</th>
					<td class="run-tool">
						<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wcmp-setting-admin&tab=general&tab_section=tools&action=' . $action ), 'wcmp_settings_tools_action' ); ?>" class="button button-large <?php echo esc_attr( $action ); ?>"><?php echo esc_html( $tool['button'] ); ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
        <?php
    }
    
    /**
     * A list of available tools for use in the system status section.
     * 'button' becomes 'action' in the API.
     *
     * @return array
     */
    public function get_tools() {
        $tools = array(
            'clear_all_vendor_transients'             => array(
                'name'   => __( 'WCMp vendors transients', 'dc-woocommerce-multi-vendor' ),
                'button' => __( 'Clear transients', 'dc-woocommerce-multi-vendor' ),
                'desc'   => __( 'This tool will clear all WCMp vendors transients cache.', 'dc-woocommerce-multi-vendor' ),
            ),
            'reset_table_visitors_stats'   => array(
                'name'   => __( 'Reset visitors stats table', 'dc-woocommerce-multi-vendor' ),
                'button' => __( 'Reset database', 'dc-woocommerce-multi-vendor' ),
                'desc'   => __( 'This tool will clear ALL the table data of WCMp visitors stats.', 'dc-woocommerce-multi-vendor' ),
            ), 
            'force_wcmp_orders_migration'   => array(
                'name'   => __( 'Force WCMp order migrate', 'dc-woocommerce-multi-vendor' ),
                'button' => __( 'Order migrate', 'dc-woocommerce-multi-vendor' ),
                'desc'   => __( 'This will regenerate all vendors older orders to individual orders', 'dc-woocommerce-multi-vendor' ),
            ),
        );

        return apply_filters( 'wcmp_settings_general_tools', $tools );
   }
   
    public function settings_tools_action(){
        global $wpdb;
        if ( ! empty( $_GET['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), 'wcmp_settings_tools_action' ) ) { // WPCS: input var ok, sanitization ok.
            $action = wc_clean( wp_unslash( $_GET['action'] ) ); // WPCS: input var ok.
            $ran = true;
            switch ( $action ) {
                case 'clear_all_vendor_transients':
                    $vendors = get_wcmp_vendors();
                    foreach ( $vendors as $vendor ) {
                        if( $vendor ) $vendor->clear_all_transients($vendor->id);
                    }
                    
                    $message = __( 'WCMp transients cleared', 'dc-woocommerce-multi-vendor' );
                    break;

                case 'reset_table_visitors_stats':
                    $delete = $wpdb->query(apply_filters( 'wcmp_tools_action_reset_table_visitors_stats_query', "TRUNCATE {$wpdb->prefix}wcmp_visitors_stats" ) );
                    if ( $delete ){
                        $message = __( 'WCMp visitors stats successfully deleted', 'dc-woocommerce-multi-vendor' );
                    } else {
                        $ran     = false;
                        $message = __( 'There was an error calling this tool. There is no callback present.', 'woocommerce' );
                    }
                    break;
                case 'force_wcmp_orders_migration':
                    delete_option('wcmp_orders_table_migrated');
                    wp_schedule_event( time(), 'hourly', 'wcmp_orders_migration' );
                    $message = __( 'Force order migration started.', 'dc-woocommerce-multi-vendor' );
                    break;
                default:
                    do_action( 'wcmp_settings_tools_action_default_case', $action, $_REQUEST );
                    $ran     = false;
                    $message = __( 'There was an error calling this tool. There is no callback present.', 'woocommerce' );
                    break;
            }
            
            if ( $ran ) {
                    echo '<div class="updated inline"><p>' . esc_html( $message ) . '</p></div>';
            } else {
                    echo '<div class="error inline"><p>' . esc_html( $message ) . '</p></div>';
            }
            $message = '';
        }
    }
}
