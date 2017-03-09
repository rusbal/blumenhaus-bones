
<div class="payenginecw-external-checkout-shipping">
	<?php if (!empty($errorMessage)): ?>
		<p class="payment-error woocommerce-error">
			<?php print $errorMessage; ?>
		</p>
	<?php endif; ?>
	<h3><?php echo __("Shipping Option", "woocommerce_payenginecw")?></h3>
	<table class="payenginecw-external-checkout-shipping-table">
	
	<?php 
	echo $rows;
	?>
	
	</table>
	<input type="submit" class="payenginecw-external-checkout-shipping-method-save-btn button btn btn-success payenginecw-external-checkout-button" name="save" value="<?php echo __("Save Shipping Method", "woocommerce_payenginecw"); ?>" data-loading-text="<?php echo __("Processing...", "woocommerce_payenginecw"); ?>">
	
	
	<script type="text/javascript">
	jQuery(function(){
		jQuery('.payenginecw-external-checkout-shipping-method-save-btn').hide();
	
		
		jQuery('.payenginecw-external-checkout-shipping-table  input:radio').on('change', function(){
					jQuery('.payenginecw-external-checkout-shipping-method-save-btn').click();
				
		});
	});
	</script>
</div>
