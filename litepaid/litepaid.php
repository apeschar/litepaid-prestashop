<?php

if (!defined('_PS_VERSION_'))
	exit;

class LitePaid extends PaymentModule {
    public function __construct() {
        $this->name = 'litepaid';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.1';
        $this->author = 'Albert Peschar';
        parent::__construct();
        $this->displayName = $this->l('LitePaid');
        $this->description = $this->l('Accept digital currencies easy and fast.');
    }

    public function install() {
        if(!parent::install() || !$this->registerHook('payment') || !$this->registerHook('paymentReturn'))
            return false;

        Configuration::updateValue('LITEPAID_API_KEY', '');
        Configuration::updateValue('LITEPAID_TEST_MODE', '');
        Configuration::updateValue('LITEPAID_OS_ERROR', 8);
        Configuration::updateValue('LITEPAID_OS_ACCEPTED', 5);

        Db::getInstance()->execute("
            ALTER TABLE `" . _DB_PREFIX_ . "cart`
                ADD COLUMN litepaid_id VARCHAR(255)
        ");

        return true;
    }

    public function uninstall() {
        Configuration::deleteByName('LITEPAID_API_KEY');
        Configuration::deleteByName('LITEPAID_TEST_MODE');
        Configuration::deleteByName('LITEPAID_OS_ERROR');
        Configuration::deleteByName('LITEPAID_OS_ACCEPTED');

        Db::getInstance()->execute("
            ALTER TABLE `" . _DB_PREFIX_ . "cart`
                DROP COLUMN litepaid_id
        ");

        return parent::uninstall();
    }

    public function getContent() {
        $html = '<h1>' . $this->displayName . '</h1>';

        if(Tools::isSubmit('litepaid')) {
            Configuration::updateValue('LITEPAID_API_KEY', trim(Tools::getValue('api_key')));
            Configuration::updateValue('LITEPAID_TEST_MODE', Tools::getValue('test_mode') ? '1' : '');
            Configuration::updateValue('LITEPAID_OS_ERROR', Tools::getValue('LITEPAID_OS_ERROR'));
            Configuration::updateValue('LITEPAID_OS_ACCEPTED', Tools::getValue('LITEPAID_OS_ACCEPTED'));
            $html .= $this->displayConfirmation($this->l('Settings updated'));
        }

        ob_start();
        require 'html/admin_form.php';
        $html .= ob_get_clean();

        return $html;
    }

	private function getOrderStatesOptions($selected = null)
	{
		$order_states = OrderState::getOrderStates((int)$this->context->cookie->id_lang);

		$result = '';
		foreach ($order_states as $state)
		{
			$result .= '<option value="'.$state['id_order_state'].'" ';
			$result .= ($state['id_order_state'] == $selected ? 'selected="selected"' : '');
			$result .= '>'.$state['name'].'</option>';
		}

		return $result;
	}

    public function hookPayment($params) {
        if(!$this->isPayment())
            return;

        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

    public function hookPaymentReturn($params) {
        if(!$this->isPayment())
            return;

		$this->context->smarty->assign(array(
			'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
			'status' => ($params['objOrder']->getCurrentState() == Configuration::get('LITEPAID_OS_ACCEPTED') ? true : false))
		);

		return $this->display(__FILE__, 'views/templates/hook/payment_return.tpl');
    }

    public function isPayment() {
        return $this->active && Configuration::get('LITEPAID_API_KEY');
    }

    private function escape($str) {
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }
}
