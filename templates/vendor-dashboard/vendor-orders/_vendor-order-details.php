<?php
/**
 * The template for displaying vendor order detail and called from vendor_order_item.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-orders/vendor-order-details.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly    
    exit;
}
global $woocommerce, $WCMp;
$vendor = get_current_vendor();
$order = wc_get_order($order_id);
$vendor_order = wcmp_get_order($order_id);
$vendor_shipping_method = get_wcmp_vendor_order_shipping_method($order->get_id(), $vendor->id);
if (!$order) {
    ?>
    <div class="col-md-12">
        <div class="panel panel-default">
            <?php _e('Invalid order', 'dc-woocommerce-multi-vendor'); ?>
        </div>
    </div>
    <?php
    return;
}
$vendor_items = get_wcmp_vendor_orders(array('order_id' => $order->get_id(), 'vendor_id' => $vendor->id));
$vendor_order_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id(), 'vendor_id' => $vendor->id));
//print_r($vendor_order_amount);die;
$subtotal = 0;





global $wpdb;

// Get the payment gateway
$payment_gateway = wc_get_payment_gateway_by_order( $order );

// Get line items
$line_items          = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$discounts           = $order->get_items( 'discount' );
$line_items_fee      = $order->get_items( 'fee' );
$line_items_shipping = $order->get_items( 'shipping' );

if ( wc_tax_enabled() ) {
	$order_taxes      = $order->get_taxes();
	$tax_classes      = WC_Tax::get_tax_classes();
	$classes_options  = wc_get_product_tax_class_options();
	$show_tax_columns = count( $order_taxes ) === 1;
}
?>
<div class="col-md-12">
    <div class="woocommerce_order_items_wrapper wc-order-items-editable">
	<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
		<thead>
			<tr>
				<th class="item sortable" colspan="2" data-sort="string-ins"><?php esc_html_e( 'Item', 'woocommerce' ); ?></th>
				<?php do_action( 'woocommerce_admin_order_item_headers', $order ); ?>
				<th class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Cost', 'woocommerce' ); ?></th>
				<th class="quantity sortable" data-sort="int"><?php esc_html_e( 'Qty', 'woocommerce' ); ?></th>
				<th class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
				<?php
				if ( ! empty( $order_taxes ) ) :
					foreach ( $order_taxes as $tax_id => $tax_item ) :
						$tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
						$tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'woocommerce' );
						$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'woocommerce' );
						/* translators: %1$s: tax item name %2$s: tax class name  */
						$column_tip = sprintf( esc_html__( '%1$s (%2$s)', 'woocommerce' ), $tax_item['name'], $tax_class_name );
						?>
						<th class="line_tax tips" data-tip="<?php echo esc_attr( $column_tip ); ?>">
							<?php echo esc_attr( $column_label ); ?>
							<input type="hidden" class="order-tax-id" name="order_taxes[<?php echo esc_attr( $tax_id ); ?>]" value="<?php echo esc_attr( $tax_item['rate_id'] ); ?>">
							<a class="delete-order-tax" href="#" data-rate_id="<?php echo esc_attr( $tax_id ); ?>"></a>
						</th>
						<?php
					endforeach;
				endif;
				?>
				<th class="wc-order-edit-line-item" width="1%">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="order_line_items">
			<?php
			foreach ( $line_items as $item_id => $item ) {
				do_action( 'woocommerce_before_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );

				$product      = $item->get_product();
$product_link = $product ? admin_url( 'post.php?post=' . $item->get_product_id() . '&action=edit' ) : '';
$thumbnail    = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
$row_class    = apply_filters( 'woocommerce_admin_html_order_item_class', ! empty( $class ) ? $class : '', $item, $order );
?>
<tr class="item <?php echo esc_attr( $row_class ); ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
	<td class="thumb">
		<?php echo '<div class="wc-order-item-thumbnail">' . wp_kses_post( $thumbnail ) . '</div>'; ?>
	</td>
	<td class="name" data-sort-value="<?php echo esc_attr( $item->get_name() ); ?>">
		<?php
		echo $product_link ? '<a href="' . esc_url( $product_link ) . '" class="wc-order-item-name">' . esc_html( $item->get_name() ) . '</a>' : '<div class="wc-order-item-name">' . esc_html( $item->get_name() ) . '</div>';

		if ( $product && $product->get_sku() ) {
			echo '<div class="wc-order-item-sku"><strong>' . esc_html__( 'SKU:', 'woocommerce' ) . '</strong> ' . esc_html( $product->get_sku() ) . '</div>';
		}

		if ( $item->get_variation_id() ) {
			echo '<div class="wc-order-item-variation"><strong>' . esc_html__( 'Variation ID:', 'woocommerce' ) . '</strong> ';
			if ( 'product_variation' === get_post_type( $item->get_variation_id() ) ) {
				echo esc_html( $item->get_variation_id() );
			} else {
				/* translators: %s: variation id */
				printf( esc_html__( '%s (No longer exists)', 'woocommerce' ), $item->get_variation_id() );
			}
			echo '</div>';
		}
		?>
		<input type="hidden" class="order_item_id" name="order_item_id[]" value="<?php echo esc_attr( $item_id ); ?>" />
		<input type="hidden" name="order_item_tax_class[<?php echo absint( $item_id ); ?>]" value="<?php echo esc_attr( $item->get_tax_class() ); ?>" />

		<?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, $product ); ?>
		<?php //require 'html-order-item-meta.php'; ?>
		<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, $product ); ?>
	</td>

	<?php do_action( 'woocommerce_admin_order_item_values', $product, $item, absint( $item_id ) ); ?>

	<td class="item_cost" width="1%" data-sort-value="<?php echo esc_attr( $order->get_item_subtotal( $item, false, true ) ); ?>">
		<div class="view">
			<?php
				echo wc_price( $order->get_item_total( $item, false, true ), array( 'currency' => $order->get_currency() ) );

			if ( $item->get_subtotal() !== $item->get_total() ) {
				echo '<span class="wc-order-item-discount">-' . wc_price( wc_format_decimal( $order->get_item_subtotal( $item, false, false ) - $order->get_item_total( $item, false, false ), '' ), array( 'currency' => $order->get_currency() ) ) . '</span>';
			}
			?>
		</div>
	</td>
	<td class="quantity" width="1%">
		<div class="view">
			<?php
				echo '<small class="times">&times;</small> ' . esc_html( $item->get_quantity() );

			if ( $refunded_qty = $order->get_qty_refunded_for_item( $item_id ) ) {
				echo '<small class="refunded">-' . ( $refunded_qty * -1 ) . '</small>';
			}
			?>
		</div>
		<div class="edit" style="display: none;">
			<input type="number" step="<?php echo esc_attr( apply_filters( 'woocommerce_quantity_input_step', '1', $product ) ); ?>" min="0" autocomplete="off" name="order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" value="<?php echo esc_attr( $item->get_quantity() ); ?>" data-qty="<?php echo esc_attr( $item->get_quantity() ); ?>" size="4" class="quantity" />
		</div>
		<div class="refund" style="display: none;">
			<input type="number" step="<?php echo esc_attr( apply_filters( 'woocommerce_quantity_input_step', '1', $product ) ); ?>" min="0" max="<?php echo absint( $item->get_quantity() ); ?>" autocomplete="off" name="refund_order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" size="4" class="refund_order_item_qty" />
		</div>
	</td>
	<td class="line_cost" width="1%" data-sort-value="<?php echo esc_attr( $item->get_total() ); ?>">
		<div class="view">
			<?php
			echo wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) );

			if ( $item->get_subtotal() !== $item->get_total() ) {
				echo '<span class="wc-order-item-discount">-' . wc_price( wc_format_decimal( $item->get_subtotal() - $item->get_total(), '' ), array( 'currency' => $order->get_currency() ) ) . '</span>';
			}

			if ( $refunded = $order->get_total_refunded_for_item( $item_id ) ) {
				echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
			}
			?>
		</div>
		<div class="edit" style="display: none;">
			<div class="split-input">
				<div class="input">
					<label><?php esc_attr_e( 'Pre-discount:', 'woocommerce' ); ?></label>
					<input type="text" name="line_subtotal[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $item->get_subtotal() ) ); ?>" class="line_subtotal wc_input_price" data-subtotal="<?php echo esc_attr( wc_format_localized_price( $item->get_subtotal() ) ); ?>" />
				</div>
				<div class="input">
					<label><?php esc_attr_e( 'Total:', 'woocommerce' ); ?></label>
					<input type="text" name="line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $item->get_total() ) ); ?>" class="line_total wc_input_price" data-tip="<?php esc_attr_e( 'After pre-tax discounts.', 'woocommerce' ); ?>" data-total="<?php echo esc_attr( wc_format_localized_price( $item->get_total() ) ); ?>" />
				</div>
			</div>
		</div>
		<div class="refund" style="display: none;">
			<input type="text" name="refund_line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="refund_line_total wc_input_price" />
		</div>
	</td>

	<?php
	if ( ( $tax_data = $item->get_taxes() ) && wc_tax_enabled() ) {
		foreach ( $order_taxes as $tax_item ) {
			$tax_item_id       = $tax_item->get_rate_id();
			$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
			$tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : '';
			?>
			<td class="line_tax" width="1%">
				<div class="view">
					<?php
					if ( '' !== $tax_item_total ) {
						echo wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) );
					} else {
						echo '&ndash;';
					}

					if ( $item->get_subtotal() !== $item->get_total() ) {
						if ( '' === $tax_item_total ) {
							echo '<span class="wc-order-item-discount">&ndash;</span>';
						} else {
							echo '<span class="wc-order-item-discount">-' . wc_price( wc_round_tax_total( $tax_item_subtotal - $tax_item_total ), array( 'currency' => $order->get_currency() ) ) . '</span>';
						}
					}

					if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id ) ) {
						echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
					}
					?>
				</div>
				<div class="edit" style="display: none;">
					<div class="split-input">
						<div class="input">
							<label><?php esc_attr_e( 'Pre-discount:', 'woocommerce' ); ?></label>
							<input type="text" name="line_subtotal_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $tax_item_subtotal ) ); ?>" class="line_subtotal_tax wc_input_price" data-subtotal_tax="<?php echo esc_attr( wc_format_localized_price( $tax_item_subtotal ) ); ?>" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
						</div>
						<div class="input">
							<label><?php esc_attr_e( 'Total:', 'woocommerce' ); ?></label>
							<input type="text" name="line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $tax_item_total ) ); ?>" class="line_tax wc_input_price" data-total_tax="<?php echo esc_attr( wc_format_localized_price( $tax_item_total ) ); ?>" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
						</div>
					</div>
				</div>
				<div class="refund" style="display: none;">
					<input type="text" name="refund_line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="refund_line_tax wc_input_price" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
				</div>
			</td>
			<?php
		}
	}
	?>
	<td class="wc-order-edit-line-item" width="1%">
		<div class="wc-order-edit-line-item-actions">
			<?php if ( $order->is_editable() ) : ?>
				<a class="edit-order-item tips" href="#" data-tip="<?php esc_attr_e( 'Edit item', 'woocommerce' ); ?>"></a><a class="delete-order-item tips" href="#" data-tip="<?php esc_attr_e( 'Delete item', 'woocommerce' ); ?>"></a>
			<?php endif; ?>
		</div>
	</td>
</tr>
<?php

				do_action( 'woocommerce_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );
			}
			do_action( 'woocommerce_admin_order_items_after_line_items', $order->get_id() );
			?>
		</tbody>
		<tbody id="order_shipping_line_items">
			<?php
			$shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();
			foreach ( $line_items_shipping as $item_id => $item ) {
				include 'html-order-shipping.php';
			}
			do_action( 'woocommerce_admin_order_items_after_shipping', $order->get_id() );
			?>
		</tbody>
		<tbody id="order_fee_line_items">
			<?php
			foreach ( $line_items_fee as $item_id => $item ) {
				include 'html-order-fee.php';
			}
			do_action( 'woocommerce_admin_order_items_after_fees', $order->get_id() );
			?>
		</tbody>
		<tbody id="order_refunds">
			<?php
			if ( $refunds = $order->get_refunds() ) {
				foreach ( $refunds as $refund ) {
					include 'html-order-refund.php';
				}
				do_action( 'woocommerce_admin_order_items_after_refunds', $order->get_id() );
			}
			?>
		</tbody>
	</table>
    </div>
    <div class="wc-order-data-row wc-order-bulk-actions wc-order-data-row-toggle">
	<p class="add-items">
		<?php if ( $order->is_editable() ) : ?>
			<button type="button" class="button add-line-item"><?php esc_html_e( 'Add item(s)', 'woocommerce' ); ?></button>
			<?php if ( wc_coupons_enabled() ) : ?>
				<button type="button" class="button add-coupon"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
			<?php endif; ?>
		<?php else : ?>
			<span class="description"><?php echo wc_help_tip( __( 'To edit this order change the status back to "Pending"', 'woocommerce' ) ); ?> <?php esc_html_e( 'This order is no longer editable.', 'woocommerce' ); ?></span>
		<?php endif; ?>
		<?php if ( 0 < $order->get_total() - $order->get_total_refunded() || 0 < absint( $order->get_item_count() - $order->get_item_count_refunded() ) ) : ?>
			<button type="button" class="button refund-items"><?php esc_html_e( 'Refund', 'woocommerce' ); ?></button>
		<?php endif; ?>
		<?php
			// allow adding custom buttons
			do_action( 'woocommerce_order_item_add_action_buttons', $order );
		?>
		<?php if ( $order->is_editable() ) : ?>
			<button type="button" class="button button-primary calculate-action"><?php esc_html_e( 'Recalculate', 'woocommerce' ); ?></button>
		<?php endif; ?>
	</p>
</div>
</div>