<?php

if(!defined('_PS_VERSION_'))
    exit;

class LitePaidPaymentModuleFrontController extends ModuleFrontController {
    public $ssl = true;

    public function initContent() {
        $redirect_timeout = '<script>window.setTimeout(function(){window.location=' . json_encode(_PS_BASE_URL_ . __PS_BASE_URI__ . 'index.php?controller=order&step=3') . ';}, 3000);</script>';

        if(!$this->isTokenValid())
            $this->errorPage('Invalid token');

        $cart = $this->context->cart;
        $amount = number_format($cart->getOrderTotal(), '2', '.', '');

        $data = array(
            'key' => Configuration::get('LITEPAID_API_KEY'),
            'value' => $amount,
            'return_url' => _PS_BASE_URL_ . $this->module->getPathUri() . 'confirmation.php',
            'description' => 'Cart #' . $cart->id,
            'test' => Configuration::get('LITEPAID_TEST_MODE') ? '1' : '0',
        );

        $response = @file_get_contents('https://www.litepaid.com/api?' . http_build_query($data));

        if(!$response
           || !($response = @json_decode($response, true))
           || empty($response['result'])
           || $response['result'] != 'success'
           || empty($response['data']['invoice_token'])
        ) {
            echo "<p>LitePaid API request failed. Choose another payment method to complete your order.</p>";
            if(!empty($response['data']['error_name']))
                echo "<p><b>Error:</b> " . htmlentities($response['data']['error_name'], ENT_QUOTES, 'UTF-8') . "</p>";
            echo $redirect_timeout;
            exit;
        }

        $litepaid_id = $response['data']['invoice_token'];

        Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "cart` SET litepaid_id = '" . pSQL($litepaid_id) . "' WHERE id_cart = " . (int)$cart->id);

        $url = 'https://www.litepaid.com/invoice/id:' . $litepaid_id;
        @header('Location: ' . $url);
        echo "<script>\nwindow.location = " . json_encode($url) . ";\n</script>\n";
        exit;
    }

    private function errorPage($message) {
        die($this->module->l($this->module->displayName . ' error: ' . $message));
    }
}
