
<div class="payenginecw-external-checkout-additional">
	<?php if (!empty($errorMessage)): ?>
		<p class="payment-error woocommerce-error">
			<?php print $errorMessage; ?>
		</p>
	<?php endif; ?>

	<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) ) : ?>
		
		<h3><?php echo __( 'Additional Information', 'woocommerce' ); ?></h3>
			<div class="form-group">
			<label for="order-note" class=""><?php __('Order Notes', 'woocommerce'); ?></label>
			<textarea name="order-note" class="input-text " id="order-note" placeholder="<?php echo __('Notes about your order, e.g. special notes for delivery.',  'woocommerce'); ?>" rows="4" cols="20"><?php echo isset($values['order-note']) ? $values['order-note'] : '';?></textarea>
		
		</div>	
	<?php endif; ?>
</div>
