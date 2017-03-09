
<div class="payenginecw-external-checkout-confirm">
	<?php if (!empty($errorMessage)): ?>
		<p class="payment-error woocommerce-error">
			<?php print $errorMessage; ?>
		</p>
	<?php endif; ?>
	<div class="col2-set payenginecw-external-checkout-customer-details">
		<div class="col-1 woocommerce-billing-fields payenginecw-external-checkout-billing">
			<h3><?php echo __("Billing Address", "woocommerce_payenginecw"); ?></h3>
			<div class="payenginecw-external-checkout-fix">
				<div class="payenginecw-external-checkout-addressline"><?php echo $billing['name'];?></div>
				<div class="payenginecw-external-checkout-addressline"><?php echo $billing['street'];?></div>
				<div class="payenginecw-external-checkout-addressline"><?php echo $billing['postCode'].' '.$billing['city'].(empty($billing['state']) ? '': ', '.$billing['state']); ?></div>
				<div class="payenginecw-external-checkout-addressline"><?php echo $billing['country']; ?></div>
			</div>
		</div>
		<div class="col-2 woocommerce-shipping-fields payenginecw-external-checkout-shipping">
			<h3><?php echo __("Shipping Address", "woocommerce_payenginecw"); ?></h3>
			<div class="payenginecw-external-checkout-fix">
				<div class="payenginecw-external-checkout-addressline"><?php echo $shipping['name'];?></div>
				<div class="payenginecw-external-checkout-addressline"><?php echo $shipping['street'];?></div>
				<div class="payenginecw-external-checkout-addressline"><?php echo $shipping['postCode'].' '.$shipping['city'].(empty($shipping['state']) ? '': ', '.$shipping['state']); ?></div>
				<div class="payenginecw-external-checkout-addressline"><?php echo $shipping['country']; ?></div>
			</div>
		</div>
	</div>
	<div class="payenginecw-external-checkout-line-items" >
		<h3><?php echo __("Order Review", "woocommerce_payenginecw"); ?></h3>
			<?php echo $orderReview; ?>
	</div>
	<?php if($showConfirm) : ?>
		<?php if (wc_get_page_id( 'terms' ) > 0) : ?>
		<div class="payenginecw-external-checkout-terms" >
				<label for="terms" class="checkbox"><?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">terms &amp; conditions</a>', 'woocommerce' ), esc_url( wc_get_page_permalink( 'terms' ) ) ); ?></label>
				<input type="checkbox" class="input-checkbox" name="terms" id="terms" />
		</div>
		<?php endif; ?>
		<div class="payenginecw-external-checkout-confirm-buttons" id="payenginecw-external-checkout-confirm-buttons">
			<input id="payenginecw-external-checkout-confirm-button" type="submit" value="<?php echo __('Place Order', 'woocommerce_payenginecw') ?>" class="button payenginecw-external-checkout-button btn btn-success"/>
		</div>
		<div class="payenginecw-external-checkout-wait-please" id="payenginecw-external-checkout-wait-please" style="display:none;">
			<?php echo __('Submitting order information...', 'woocommerce_payenginecw') ?>
		</div>
		<script type="text/javascript">
			jQuery('#payenginecw-external-checkout-confirm-button').on('click', function() {
				var container = jQuery('#payenginecw-external-checkout-confirm-buttons');
				container.prop("disabled",true);
				jQuery(this).hide();
				jQuery('#payenginecw-external-checkout-wait-please').show();
			});
		</script>
	<?php endif; ?>
</div>

