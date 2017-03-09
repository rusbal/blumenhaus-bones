
<table class="payenginecw-external-checkout-line-items-table">

	<thead>
		<tr>
			<th class="head-name"><?php echo __("Product", "woocommerce_payenginecw"); ?></th>
			<td class="head-quantity"><?php echo __("Quantity", "woocommerce_payenginecw"); ?></td>
			<td class="head-total"><?php echo __("Subtotal", "woocommerce_payenginecw"); ?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($lineItems as $item) : ?>
			<tr class="cart_item">
				<td class="product-name"><?php echo $item['name']; ?></td>
				<td class="product-quantity"><?php echo $item['quantity']; ?>&times;</td>
				<td class="product-total"><?php echo $item['total']; ?></td>
			</tr>
		<?php endforeach;?>
	</tbody>
	<tfoot>

		<tr class="cart-subtotal">
			<th colspan="2"><?php echo __( 'Cart Subtotal', 'woocommerce_payenginecw' ); ?></th>
			<td><?php echo $subTotal; ?></td>
		</tr>

		<?php foreach ($coupons as $code => $coupon ) : ?>
			<tr class="discount coupon-<?php echo esc_attr( $code ); ?>">
				<th colspan="2"><?php echo $coupon['name']; ?></th>
				<td><?php echo $coupon['amount']; ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if(isset($shipping)): ?>
			<tr class="shipping">
				<th colspan="2"><?php echo __( 'Shipping &amp; Handling', 'woocommerce_payenginecw' ); ?></th>
				<td><?php echo $shipping; ?></td>
			</tr>
		<?php endif; ?>

		<?php foreach ( $fees as $fee ) : ?>
			<tr class="fee">
				<th colspan="2"><?php echo $fee['name']; ?></th>
				<td><?php echo $fee['amount']; ?></td>
			</tr>
		<?php endforeach; ?>

		<?php foreach ( $taxes as $code => $tax ) : ?>
			<tr class="taxes tax-rate-<?php echo sanitize_title( $code ); ?>">
				<th colspan="2"><?php echo $tax['name']; ?></th>
				<td><?php echo $tax['amount']; ?></td>
			</tr>
		<?php endforeach; ?>

		<tr class="order-total">
			<th colspan="2"><?php echo __( 'Order Total', 'woocommerce_payenginecw' ); ?></th>
			<td><?php echo $totalAmount; ?></td>
		</tr>

	</tfoot>
</table>