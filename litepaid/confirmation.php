<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

if(file_exists(dirname(__FILE__).'/../../config/config.inc.php'))
    require dirname(__FILE__).'/../../config/config.inc.php';
else
    require preg_replace('|(/+[^/]*){3}$|', '', $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).'/config/config.inc.php';

require dirname(__FILE__).'/litepaid.php';

$litepaid_id = Tools::getValue('litepaid_id');
if(!$litepaid_id)
    die("<p>LitePaid error: <code>litepaid_id</code> is not set.</p>");

$cart_id = Db::getInstance()->getValue("SELECT id_cart FROM `" . _DB_PREFIX_ . "cart` WHERE litepaid_id = '" . pSQL($litepaid_id) . "'");

$cart = new Cart($cart_id);

if (empty(Context::getContext()->link))
    Context::getContext()->link = new Link();
Context::getContext()->language = new Language($cart->id_lang);
Context::getContext()->currency = new Currency($cart->id_currency);

$litepaid = new Litepaid();

$response = @file_get_contents('https://www.litepaid.com/api?' . http_build_query(array(
    'key' => Configuration::get('LITEPAID_API_KEY'),
    'id'  => $litepaid_id,
)));

if(!$response || !($response = @json_decode($response, true))) {
    echo "<p>LitePaid API request failed. Contact support.</p>";
    exit;
}

$success = isset($response['result']) && $response['result'] == 'success';
$order_state = $success ? Configuration::get('LITEPAID_OS_ACCEPTED') : Configuration::get('LITEPAID_OS_ERROR');

$customer = new Customer((int)$cart->id_customer);

if (!Order::getOrderByCartId($cart->id)) {
	$litepaid->validateOrder(
        $cart->id, $order_state, (float)number_format($cart->getOrderTotal(), 2, '.', ''),
		$litepaid->displayName, null, null, null, false, $customer->secure_key, null);
}

$order_id = Order::getOrderByCartId($cart->id);

$order = new Order($order_id);

/* Init Frontend variables for redirect */
$controller = new FrontController();
$controller->init();

if (version_compare(_PS_VERSION_, '1.5', '>='))
	Tools::redirect('index.php?controller=order-confirmation&id_cart='.$order->id_cart
		.'&id_module='.$litepaid->id.'&id_order='.$order_id
		.'&key='.$order->secure_key);
else
	Tools::redirect('order-confirmation.php?id_cart='.$order->id_cart
		.'&id_module='.$litepaid->id.'&id_order='.$order_id
		.'&key='.$order->secure_key);
