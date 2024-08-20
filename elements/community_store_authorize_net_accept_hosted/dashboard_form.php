<?php defined('C5_EXECUTE') or die(_("Access Denied."));
extract($vars);
?>
<div class="form-group">
    <?=$form->label('authorizeNetAcceptHostedCurrency',t('Currency'))?>
    <?=$form->select('authorizeNetAcceptHostedCurrency',$authorizeNetAcceptHostedCurrencies,$authorizeNetAcceptHostedCurrency)?>
</div>

<div class="form-group">
    <?=$form->label('authorizeNetAcceptHostedMode',t('Mode'))?>
    <?=$form->select('authorizeNetAcceptHostedMode',array('test'=>t('Test'), 'live'=>t('Live')),$authorizeNetAcceptHostedMode)?>
</div>

<hr />


<div class="form-group">
    <label><?=t("Test Login ID")?></label>
    <input type="text" name="authorizeNetAcceptHostedTestLoginID" value="<?= $authorizeNetAcceptHostedTestLoginID?>" class="form-control">
</div>


<div class="form-group">
    <label><?=t("Test Transaction Key")?></label>
    <input type="text" name="authorizeNetAcceptHostedTestTransactionKey" value="<?= $authorizeNetAcceptHostedTestTransactionKey?>" class="form-control">
</div>
<div class="help-block"><?= t('To find the <strong>Login ID</strong> and <strong>Transaction Key</strong> within the Authorize.net dashboard, visit: Settings (under Account on left hand side menu) -> API Credentials & Keys (under Security Settings). The Login ID will be displayed, and a Transaction Key will need to be generated.');?></div>


<div class="form-group">
    <label><?=t("Test Public Client Key")?></label>
    <input type="text" name="authorizeNetAcceptHostedTestClientKey" value="<?= $authorizeNetAcceptHostedTestClientKey?>" class="form-control">
</div>

<div class="help-block"><?= t('To find the <strong>Public Client Key</strong> within the Authorize.net dashboard, visit: Settings (under Account on left hand side menu) -> Manage Public Client Key (under Security Settings)');?></div>



<hr />

<div class="form-group">
    <label><?=t("Live Login ID")?></label>
    <input type="text" name="authorizeNetAcceptHostedLiveLoginID" value="<?= $authorizeNetAcceptHostedLiveLoginID?>" class="form-control">
</div>


<div class="form-group">
    <label><?=t("Live Transaction Key")?></label>
    <input type="text" name="authorizeNetAcceptHostedLiveTransactionKey" value="<?= $authorizeNetAcceptHostedLiveTransactionKey?>" class="form-control">
</div>



<div class="form-group">
    <label><?=t("Live Public Client Key")?></label>
    <input type="text" name="authorizeNetAcceptHostedLiveClientKey" value="<?= $authorizeNetAcceptHostedLiveClientKey?>" class="form-control">
</div>
