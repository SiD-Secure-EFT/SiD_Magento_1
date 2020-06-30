<?php
/*
 * Copyright (c) 2020 PayGate (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 *
 * Released under the GNU General Public License
 */
class Setcom_SID_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{

    protected $_code          = 'sid';
    protected $_formBlockType = 'sid/form';
    protected $_infoBlockType = 'sid/payment_info';

    protected $_allowCurrencyCode = array( 'ZAR' );

    protected $_isGateway              = true;
    protected $_canAuthorize           = true;
    protected $_canCapture             = true;
    protected $_canCapturePartial      = false;
    protected $_canRefund              = false;
    protected $_canVoid                = true;
    protected $_canUseInternal         = true;
    protected $_canUseCheckout         = true;
    protected $_canUseForMultishipping = true;
    protected $_canSaveCc              = false;

    public function getCheckout()
    {
        return Mage::getSingleton( 'checkout/session' );
    }

    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function canUseForCurrency( $currencyCode )
    {
        if ( in_array( $currencyCode, $this->getAcceptedCurrencyCodes() ) ) {
            return true;
        }
        return false;
    }

    public function getAcceptedCurrencyCodes()
    {
        if ( !$this->hasData( '_accepted_currency' ) ) {
            $acceptedCurrencyCodes   = $this->_allowCurrencyCode;
            $acceptedCurrencyCodes[] = $this->getConfigData( 'currency' );
            $this->setData( '_accepted_currency', $acceptedCurrencyCodes );
        }
        return $this->_getData( '_accepted_currency' );
    }

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl( 'sid/redirect/redirect', array( '_secure' => true ) );
    }

    public function getSidPaymentUrl()
    {
        return "https://www.sidpayment.com/paySID/";
    }

    public function getCheckoutFormFields()
    {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order            = Mage::getModel( 'sales/order' )->loadByIncrementId( $orderIncrementId );
        $quoteId          = $order->getQuoteId();
        $orderEntityId    = $order->getId();

        $address = $order->getBillingAddress();

        $merchantCode = $this->getConfigData( 'merchant_code' );
        $privateKey   = $this->getConfigData( 'private_key' );
        $currencyCode = $order->getOrderCurrencyCode();
        $countryCode  = $address->getCountry();
        $orderId      = $this->getCheckout()->getLastRealOrderId();
        $orderTotal   = $order->getGrandTotal();
        $consistent   = strtoupper( hash( 'sha512', $merchantCode . $currencyCode . $countryCode . $orderId . $orderTotal . $quoteId . $orderEntityId . $privateKey ) );

        $fields = array(
            'SID_MERCHANT'   => $merchantCode,
            'SID_CURRENCY'   => $currencyCode,
            'SID_COUNTRY'    => $countryCode,
            'SID_REFERENCE'  => $orderId,
            'SID_AMOUNT'     => $orderTotal,
            'SID_CUSTOM_01'  => $quoteId,
            'SID_CUSTOM_02'  => $orderEntityId,
            'SID_CONSISTENT' => $consistent,
        );

        return $fields;
    }
}
