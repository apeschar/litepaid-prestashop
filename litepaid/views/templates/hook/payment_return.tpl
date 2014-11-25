{if $status === true}
	<p>
		{l s='Your order on' mod='litepaid'} <span class="bold">{$shop_name|escape:'htmlall':'UTF-8'}</span> {l s='is complete.' mod='litepaid'}
		<br /><br />
		{l s='The total amount of this order is' mod='litepaid'} <span class="price">{$total_to_pay|escape:'htmlall':'UTF-8'}</span>
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='litepaid'} 
		<a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}contact-form.php">{l s='customer support' mod='litepaid'}</a>.
	</p>
{/if}
