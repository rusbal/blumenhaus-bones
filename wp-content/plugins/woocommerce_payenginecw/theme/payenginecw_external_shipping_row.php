

<tr class="payenginecw-external-checkout-shipping-row">
<th><?php
	printf( __( 'Shipping #%d', 'woocommerce_payenginecw' ), $index + 1 );
?>
</th>
	<td>
		<?php if ( count( $availableMethods ) > 0) : ?>
			<ul id="shipping_method">
			<?php foreach ( $availableMethods as $method ) : ?>
				<li>
					<input type="radio" name="shipping_method[<?php echo $index; ?>]" id="shipping_method_<?php echo $index; ?>_<?php echo sanitize_title( $method->id ); ?>" value="<?php echo esc_attr( $method->id ); ?>" <?php checked( $method->id, $selectedMethod ); ?> class="shipping_method" />
					<label for="shipping_method_<?php echo $index; ?>_<?php echo sanitize_title( $method->id ); ?>"><?php echo $method->label; ?>: <?php echo woocommerce_payenginecw_format_shipping_amount_like_shop($method);?></label>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php else : ?>

			<?php echo apply_filters( 'woocommerce_no_shipping_available_html',
				'<p>' . __( 'There are no shipping methods available. Please contact us if you need any help.', 'woocommerce' ) . '</p>'
			); ?>

		<?php endif; ?>
		
		<?php
			foreach ( $package['contents'] as $item_id => $values ) {
				if ( $values['data']->needs_shipping() ) {
					$product_names[] = $values['data']->get_title() . ' &times;' . $values['quantity'];
				}
			}
			echo '<p class="payenginecw-external-checkout-contents"><small>' . __( 'Shipping', 'woocommerce_payenginecw' ) . ': ' . implode( ', ', $product_names ) . '</small></p>';
		?>

	</td>
</tr>
