<?php

namespace Concrete\Package\CommunityStoreAuthorizeNetAcceptHosted\Src\CommunityStore\Payment\Methods\CommunityStoreAuthorizeNetAcceptHosted;

use Concrete\Package\CommunityStore\Controller\SinglePage\Dashboard\Store;
use Concrete\Core\Support\Facade\Config;
use Exception;

use \Concrete\Package\CommunityStore\Src\CommunityStore\Payment\Method as StorePaymentMethod;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Utilities\Calculator as StoreCalculator;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Customer\Customer as StoreCustomer;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Shipping\Method\ShippingMethod as StoreShippingMethod;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Cart\Cart as StoreCart;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductOption\ProductOption as StoreProductOption;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductOption\ProductOptionItem as StoreProductOptionItem;


class CommunityStoreAuthorizeNetAcceptHostedPaymentMethod extends StorePaymentMethod
{

    public function dashboardForm()
    {
        $this->set('authorizeNetAcceptHostedMode', Config::get('community_store_authorize_net_accept_hosted.mode'));
        $this->set('authorizeNetAcceptHostedCurrency', Config::get('community_store_authorize_net_accept_hosted.currency'));
        $this->set('authorizeNetAcceptHostedTestLoginID', Config::get('community_store_authorize_net_accept_hosted.testLoginID'));
        $this->set('authorizeNetAcceptHostedLiveLoginID', Config::get('community_store_authorize_net_accept_hosted.liveLoginID'));
        $this->set('authorizeNetAcceptHostedTestClientKey', Config::get('community_store_authorize_net_accept_hosted.testClientKey'));
        $this->set('authorizeNetAcceptHostedLiveClientKey', Config::get('community_store_authorize_net_accept_hosted.liveClientKey'));
        $this->set('authorizeNetAcceptHostedTestTransactionKey', Config::get('community_store_authorize_net_accept_hosted.testTransactionKey'));
        $this->set('authorizeNetAcceptHostedLiveTransactionKey', Config::get('community_store_authorize_net_accept_hosted.liveTransactionKey'));
        $this->set('form', app()->make("helper/form"));

        $currencies = [
            'USD' => t('United States Dollar'),
            'CAD' => t('Canadian Dollar'),
            'CHF' => t('Swiss Franc'),
            'DKK' => t('Danish Krone'),
            'EUR' => t('Euro'),
            'GBP' => t('Pound Sterling'),
            'NOK' => t('Norwegian Krone'),
            'PLN' => t('Polish Zloty'),
            'SEK' => t('Swedish Krona'),
            'AUD' => t('Australian Dollar'),
            'NZD' => t('New Zealand Dollar')
        ];

        $this->set('authorizeNetAcceptHostedCurrencies', $currencies);
    }

    public function save(array $data = [])
    {
        Config::save('community_store_authorize_net_accept_hosted.mode', $data['authorizeNetAcceptHostedMode']);
        Config::save('community_store_authorize_net_accept_hosted.currency', $data['authorizeNetAcceptHostedCurrency']);
        Config::save('community_store_authorize_net_accept_hosted.testLoginID', trim($data['authorizeNetAcceptHostedTestLoginID']));
        Config::save('community_store_authorize_net_accept_hosted.liveLoginID', trim($data['authorizeNetAcceptHostedLiveLoginID']));
        Config::save('community_store_authorize_net_accept_hosted.testClientKey', trim($data['authorizeNetAcceptHostedTestClientKey']));
        Config::save('community_store_authorize_net_accept_hosted.liveClientKey', trim($data['authorizeNetAcceptHostedLiveClientKey']));
        Config::save('community_store_authorize_net_accept_hosted.testTransactionKey', trim($data['authorizeNetAcceptHostedTestTransactionKey']));
        Config::save('community_store_authorize_net_accept_hosted.liveTransactionKey', trim($data['authorizeNetAcceptHostedLiveTransactionKey']));
    }

    public function validate($args, $e)
    {
        return $e;
    }

    public function checkoutForm()
    {
        $mode = Config::get('community_store_authorize_net_accept_hosted.mode');
        $this->set('mode', $mode);
        $this->set('currency', Config::get('community_store_authorize_net_accept_hosted.currency'));

        if ($mode == 'live') {
            $this->set('loginID', Config::get('community_store_authorize_net_accept_hosted.liveLoginID'));
            $this->set('clientKey', Config::get('community_store_authorize_net_accept_hosted.liveClientKey'));
        } else {
            $this->set('loginID', Config::get('community_store_authorize_net_accept_hosted.testLoginID'));
            $this->set('clientKey', Config::get('community_store_authorize_net_accept_hosted.testClientKey'));
        }

        $customer = new StoreCustomer();

        $this->set('email', $customer->getEmail());
        $this->set('form', app()->make("helper/form"));
        $this->set('amount', number_format(StoreCalculator::getGrandTotal() * 100, 0, '', ''));

        $pmID = StorePaymentMethod::getByHandle('community_store_authorize_net_accept_hosted')->getID();
        $this->set('pmID', $pmID);
        $years = [];
        $year = date("Y");
        for ($i = 0; $i < 15; $i++) {
            $years[(int)$year + $i] = (int)$year + $i;
        }
        $this->set("years", $years);
    }

    public function submitPayment()
    {
        $customer = new StoreCustomer();

        $th = app()->make('helper/text');

        $currency = Config::get('community_store_authorize_net_accept_hosted.currency');
        $mode = Config::get('community_store_authorize_net_accept_hosted.mode');
        $total = number_format(StoreCalculator::getGrandTotal(), 2, '.', '');

        if ($mode == 'test') {
            $loginID = Config::get('community_store_authorize_net_accept_hosted.testLoginID');
            $transactionKey = Config::get('community_store_authorize_net_accept_hosted.testTransactionKey');
        } else {
            $loginID = Config::get('community_store_authorize_net_accept_hosted.liveLoginID');
            $transactionKey = Config::get('community_store_authorize_net_accept_hosted.liveTransactionKey');
        }

        $cart = StoreCart::getCart();

        if (isset($_POST['dataDescriptor']) && isset($_POST['dataValue'] )) {
            $transRequestXmlStr =
                '<?xml version="1.0" encoding="UTF-8"?>
<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
      <merchantAuthentication>
        <name>' . $loginID . '</name>
        <transactionKey>' . $transactionKey . '</transactionKey>
        </merchantAuthentication>
      <transactionRequest>
         <transactionType>authCaptureTransaction</transactionType>
         <amount>' . $total . '</amount>
         <currencyCode>' . $currency . '</currencyCode>
         <payment>
            <opaqueData>
               <dataDescriptor>' . $_POST['dataDescriptor'] . '</dataDescriptor>
               <dataValue>' . $_POST['dataValue'] . '</dataValue>
            </opaqueData>
         </payment>';


            $transRequestXmlStr .= '<lineItems>';
            $itemid = 1;
            foreach ($cart as $k => $cartItem) {
                $transRequestXmlStr .= '<lineItem>';
                $transRequestXmlStr .= '<itemId>' . $itemid++ . '</itemId>';

                $qty = $cartItem['product']['qty'];
                $product = $cartItem['product']['object'];

                $transRequestXmlStr .= '<name>' . h(trim($th->shortText($product->getName(), 31, ''))) . '</name>';

                $description = '';
                $descriptions = [];

                // this code should be refactored into the core at some point
                foreach ($cartItem['productAttributes'] as $groupID => $valID) {
                    $optionvalue = '';

                    if (substr($groupID, 0, 2) == 'po') {
                        $groupID = str_replace("po", "", $groupID);
                        $optionvalue = StoreProductOptionItem::getByID($valID);

                        if ($optionvalue) {
                            $optionvalue = $optionvalue->getName();
                        }
                    } elseif (substr($groupID, 0, 2) == 'pt') {
                        $groupID = str_replace("pt", "", $groupID);
                        $optionvalue = $valID;
                    } elseif (substr($groupID, 0, 2) == 'pa') {
                        $groupID = str_replace("pa", "", $groupID);
                        $optionvalue = $valID;
                    } elseif (substr($groupID, 0, 2) == 'ph') {
                        $groupID = str_replace("ph", "", $groupID);
                        $optionvalue = $valID;
                    } elseif (substr($groupID, 0, 2) == 'pc') {
                        $groupID = str_replace("pc", "", $groupID);
                        $optionvalue = $valID;
                    }

                    $optiongroup = StoreProductOption::getByID($groupID);

                    if ($optionvalue) {
                        $descriptions[] = ($optiongroup ? h($optiongroup->getName()) : '') . ': ' . ($optionvalue ? h($optionvalue) : '');
                    }
                }

                $description = implode(', ', $descriptions);

                $transRequestXmlStr .= '<description>' . $th->shortText(trim(h($description)), 255, '') . '</description>';
                $transRequestXmlStr .= '<quantity>' . $qty . '</quantity>';

                $productprice = $product->getActivePrice();

                if (!$productprice) {
                    $productprice = (float)$cartItem['product']['customerPrice'];
                }

                $transRequestXmlStr .= '<unitPrice>' . $productprice . '</unitPrice>';

                $transRequestXmlStr .= '</lineItem>';
            }
            $transRequestXmlStr .= '</lineItems>';

            $transRequestXmlStr .= '<customer>
            <email>' . h($customer->getEmail()) . '</email>
         </customer>
         <billTo>
            <firstName>' . trim(h($customer->getValue('billing_first_name'))) . '</firstName>
            <lastName>' . trim(h($customer->getValue('billing_last_name'))) . '</lastName>
            <address>' . trim(h($customer->getAddressValue('billing_address', 'address1')) . ' ' . h($customer->getAddressValue('billing_address', 'address2'))) . '</address>
            <city>' . trim(h($customer->getAddressValue('billing_address', 'city'))) . '</city>
            <state>' . trim(h($customer->getAddressValue('billing_address', 'state_province'))) . '</state>
            <zip>' . h($customer->getAddressValue('billing_address', 'postal_code')) . '</zip>
            <country>' . h($customer->getAddressValue('billing_address', 'country')) . '</country>
            <phoneNumber>' . h($customer->getValue("billing_phone")) . '</phoneNumber>
         </billTo>';

            $shipping = StoreShippingMethod::getActiveShippingMethod();

            if ($shipping) {
                $transRequestXmlStr .= '<shipTo>
            <firstName>' . trim(h($customer->getValue('shipping_first_name'))) . '</firstName>
            <lastName>' . trim(h($customer->getValue('shipping_last_name'))) . '</lastName>
            <address>' . trim(h($customer->getAddressValue('shipping_address', 'address1')) . ' ' . h($customer->getAddressValue('billing_address', 'address2'))) . '</address>
            <city>' . trim(h($customer->getAddressValue('shipping_address', 'city'))) . '</city>
            <state>' . trim(h($customer->getAddressValue('shipping_address', 'state_province'))) . '</state>
            <zip>' . h($customer->getAddressValue('shipping_address', 'postal_code')) . '</zip>
            <country>' . h($customer->getAddressValue('shipping_address', 'country')) . '</country>
            </shipTo>';
            }


            $transRequestXmlStr .= '</transactionRequest>
</createTransactionRequest>';


            $url = 'https://api' . ($mode == 'test' ? 'test' : '') . '.authorize.net/xml/v1/request.api';

            try {    //setting the curl parameters.
                $ch = curl_init();
                if (FALSE === $ch)
                    throw new Exception('failed to initialize');
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $transRequestXmlStr);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
                $content = curl_exec($ch);
                if (FALSE === $content)
                    throw new Exception(curl_error($ch), curl_errno($ch));
                curl_close($ch);

                $xmlResult = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOWARNING);

                $resultcode = $xmlResult->transactionResponse->responseCode[0];

                if ($resultcode == '1' || $resultcode == '4') {
                    return ['error' => 0, 'transactionReference' => $xmlResult->transactionResponse->transId[0]];
                } else {
                    return ['error' => 1, 'errorMessage' => (string)$xmlResult->transactionResponse->errors[0]->error->errorText];
                }

            } catch (Exception $e) {
                return ['error' => 1, 'errorMessage' => t('An error occurred, the transaction did not succeed')];
            }
        }

        return ['error' => 1, 'errorMessage' => t('An error occurred, the transaction did not succeed')];
    }

    public function getPaymentMethodName()
    {
        return 'Authorize.Net Accept Hosted';
    }

    public function getPaymentMethodDisplayName()
    {
        return $this->getPaymentMethodName();
    }

    public function getPaymentMinimum() {
        return 0.01;
    }


}

return __NAMESPACE__;
