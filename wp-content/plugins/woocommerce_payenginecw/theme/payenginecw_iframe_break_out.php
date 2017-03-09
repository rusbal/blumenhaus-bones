<?php
add_filter( 'run_wptexturize', '__return_false' );
?>
<div class="woocommerce payenginecw">
	<?php echo __('Redirecting... Please Wait ', 'woocommerce_payenginecw'); ?>
	<script type="text/javascript"> 
		top.location.href = '<?php echo $url; ?>';
	</script>
	

	<noscript>
		<a class="button btn btn-success payenginecw-continue-button" href="<?php echo $url; ?>" target="_top"><?php echo __('If you are not redirected shortly, click here.', 'woocommerce_payenginecw'); ?></a>
	</noscript>
</div>