<form method="POST" action="">
    <input type="hidden" name="litepaid" value="1">
    <fieldset>
        <legend>
            <img width=16 height=16 src="<?=$this->_path;?>/logo.png">
            <?=$this->l('LitePaid settings');?>
        </legend>

        <label for="litepaid-api-key"><?=$this->l('API key');?></label>
        <div class="margin-form">
            <input id="litepaid-api-key" type="text" name="api_key" value="<?=$this->escape(Configuration::get('LITEPAID_API_KEY'));?>" style="width:250px;">
        </div>

        <label for="litepaid-os-accepted"><?=$this->l('Status for successful payment');?></label>
        <div class="margin-form">
            <select name="LITEPAID_OS_ACCEPTED" id="litepaid-os-accepted">
                <?=$this->getOrderStatesOptions(Configuration::get('LITEPAID_OS_ACCEPTED'));?>
            </select>
        </div>

        <label for="litepaid-os-error"><?=$this->l('Status for failed payment');?></label>
        <div class="margin-form">
            <select name="LITEPAID_OS_ERROR" id="litepaid-os-error">
                <?=$this->getOrderStatesOptions(Configuration::get('LITEPAID_OS_ERROR'));?>
            </select>
        </div>

        <label><?=$this->l('Test mode');?></label>
        <div class="margin-form">
            <label class="t" for="litepaid-test-mode-on"><img src="../img/admin/enabled.gif" alt=""></label>
            <input type="radio" name="test_mode" value="1" id="litepaid-test-mode-on" <?php if(Configuration::get('LITEPAID_TEST_MODE')) echo 'checked'; ?>>
            <label class="t" for="litepaid-test-mode-on"><?=$this->l('Yes, enable test mode and don\'t process payments');?></label><br>

            <label class="t" for="litepaid-test-mode-off"><img src="../img/admin/disabled.gif" alt=""></label>
            <input type="radio" name="test_mode" value="0" id="litepaid-test-mode-off" <?php if(!Configuration::get('LITEPAID_TEST_MODE')) echo 'checked'; ?>>
            <label class="t" for="litepaid-test-mode-off"><?=$this->l('No, disable test mode');?></label>
        </div>
        <div class="margin-form">
            <input class="button" type="submit" value="<?=$this->l('Save changes');?>" />
        </div>
    </fieldset>
</form>
