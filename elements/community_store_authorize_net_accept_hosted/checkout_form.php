<?php defined('C5_EXECUTE') or die(_("Access Denied."));
extract($vars);
?>

<script type="text/javascript" src="https://js<?php echo ($mode == 'test' ? 'test' : '');?>.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>

<input name="dataDescriptor" id="dataDescriptor" type="hidden" />
<input name="dataValue" id="dataValue"  type="hidden"  />

<button type="button" id="anpaymentbutton"
        style="display: none"
        class="AcceptUI"
        data-billingAddressOptions='{"show":false, "required":false}'
        data-apiLoginID="<?= $loginID; ?>"
        data-clientKey="<?= $clientKey; ?>"
        data-acceptUIFormBtnTxt="<?= t('Pay'); ?>"
        data-acceptUIFormHeaderTxt="<?= t('Payment Details'); ?>"
        data-paymentOptions='{"showCreditCard": true, "showBankAccount": false}'
        data-responseHandler="responseHandler"><?= t('Pay'); ?>
</button>

<script type="text/javascript">

    window.addEventListener('load', function () {
    document.querySelector("[data-payment-method-id='<?= $pmID; ?>'] .store-btn-complete-order").addEventListener("click", (event) => {
        event.preventDefault();
        document.getElementById('anpaymentbutton').click();

    });
    });


    function responseHandler(response) {
        if (response.messages.resultCode === "Error") {
            var i = 0;
            while (i < response.messages.message.length) {
                console.log(
                    response.messages.message[i].code + ": " +
                    response.messages.message[i].text
                );
                i = i + 1;
            }
        } else {
            paymentFormUpdate(response.opaqueData);
        }
    }

    function paymentFormUpdate(opaqueData) {
        document.getElementById("dataDescriptor").value = opaqueData.dataDescriptor;
        document.getElementById("dataValue").value = opaqueData.dataValue;
        document.getElementById("store-checkout-form-group-payment").submit();
    }

</script>


