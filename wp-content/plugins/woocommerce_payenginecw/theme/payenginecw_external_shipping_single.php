
<?php if ( ! empty( $availableMethods ) ) : ?>
	<thead>
		<th></th>
		<th><?php echo __("Shipping Method" ,"woocommerce_payenginecw");?></th>
		<th><?php echo __("Costs", "woocommerce_payenginecw");?></th>
	</thead>
	<tbody>
	<?php if ( count( $availableMethods ) > 0) :
			foreach ( $availableMethods as $method ) : ?>
				<tr><td>
					<input  class="shipping_method" type="radio" name="shipping_method[<?php echo $index; ?>]" id="shipping_method_<?php echo $index; ?>_<?php echo sanitize_title( $method->id ); ?>" value="<?php echo esc_attr( $method->id ); ?>" <?php checked( $method->id, $selectedMethod ); ?> />
				</td>
				<td>
					<?php 	echo $method->label; ?>
				</td>
				<td>
					<?php echo woocommerce_payenginecw_format_shipping_amount_like_shop($method);?>
				</td>
				</tr>
			
			<?php endforeach;
		endif; ?>
	</tbody>
<?php else : ?>
	<?php echo apply_filters( 'woocommerce_no_shipping_available_html',
				'<p>' . __( 'There are no shipping methods available. Please contact us if you need any help.', 'woocommerce' ) . '</p>'
	); ?>

<?php endif; ?>
