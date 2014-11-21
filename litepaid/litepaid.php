<?php

if (!defined('_PS_VERSION_'))
	exit;

class LitePaid extends PaymentModule {
    public function __construct() {
        $this->name = 'litepaid';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Albert Peschar';
        parent::__construct();
        $this->displayName = $this->l('LitePaid');
        $this->description = $this->l('Accept digital currencies easy and fast.');
    }

    public function install() {
        if(!parent::install())
            return false;

        Configuration::updateValue('LITEPAID_API_KEY', '');
        Configuration::updateValue('LITEPAID_TEST_MODE', '');

        return true;
    }

    public function uninstall() {
        Configuration::deleteByName('LITEPAID_API_KEY');
        Configuration::deleteByName('LITEPAID_TEST_MODE');

        return parent::uninstall();
    }

    public function getContent() {
        $html = '<h1>' . $this->displayName . '</h1>';

        if(Tools::isSubmit('litepaid')) {
            Configuration::updateValue('LITEPAID_API_KEY', trim(Tools::getValue('api_key')));
            Configuration::updateValue('LITEPAID_TEST_MODE', Tools::getValue('test_mode') ? '1' : '');
            $html .= $this->displayConfirmation($this->l('Settings updated'));
        }

        ob_start();
        require 'html/admin_form.php';
        $html .= ob_get_clean();

        return $html;
    }

    public function escape($str) {
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }
}
