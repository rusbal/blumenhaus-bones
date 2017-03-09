<div class="payengine-masterpass-shipping-pane">
	<form action="{$confirmationPageUrl}" method="post">
		{$shippingPane}
		<input type="hidden" name="masterpass_update_shipping" value="true" />
	</form>
</div>

<div class="payengine-masterpass-confirmation-pane">
	<form action="{$confirmationPageUrl}" method="post">
		{$additionalFormElements}
		{$reviewPane}
		<input type="hidden" name="masterpass_confirmation" value="true" />
	</form>
</div>

<script type="text/javascript">
{$javascript}
</script>