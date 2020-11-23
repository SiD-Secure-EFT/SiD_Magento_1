<?php
/*
 * Copyright (c) 2020 PayGate (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 *
 * Released under the GNU General Public License
 */
class Setcom_SID_RedirectController extends Mage_Checkout_Controller_Action
{

    public function indexAction()
    {
        $result  = false;
        $onepage = Mage::getSingleton( 'checkout/type_onepage' );
        $session = $onepage->getCheckout();
        $data    = $this->getRequest()->getParams();
        try {
            $result = Mage::getModel( 'sid/paymentResponse' )->processResponse( $data );
            if ( $result == false ) {
                Mage::getModel( 'sid/paymentResponse' )->updateOrder( $data );
                $session->addError( "Your payment was unsuccessful" );

            }
        } catch ( Exception $e ) {
            $session->addError( $e->getMessage() );
            Mage::logException( $e );
        }

        if ( $data['SID_STATUS'] == "COMPLETED" ) {
            $quoteId       = $data["SID_CUSTOM_01"];
            $orderEntityId = $data["SID_CUSTOM_02"];
            $session->setLastQuoteId( $quoteId );
            $session->setLastSuccessQuoteId( $quoteId );
            $session->setLastOrderId( $orderEntityId );
            $this->_redirect( 'checkout/onepage/success', array( '_secure' => true ) );
        } else {
            $this->_redirect( 'checkout/onepage/failure', array( '_secure' => true ) );
        }
    }

    public function redirectAction()
    {
        $session = Mage::getSingleton( 'checkout/session' );

        $order = Mage::getModel( 'sales/order' );
        $order->loadByIncrementId( $session->getLastRealOrderId() );

        if ( $order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT ) {
            $order->setState( Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, "Redirected to SID" )->save();
        }

        if ( $session->getQuoteId() ) {
            $session->setSidQuoteId( $session->getQuoteId() );
            $session->getQuote()->setIsActive( false )->save();
        }

        $this->getResponse()->setBody( $this->getLayout()->createBlock( 'sid/redirect' )->toHtml() );
        $session->unsQuoteId();
    }

    public function continueAction()
    {
        $session = Mage::getSingleton( 'checkout/session' );
        $quoteId = $session->getSidQuoteId();

        if ( $quoteId ) {
            $quote = Mage::getModel( 'sales/quote' )->load( $quoteId );

            if ( $quote->getId() ) {
                $quote->setIsActive( true )->save();
                $session->setQuoteId( $quoteId );
            }
        }

        // Cancel order
        $order = Mage::getModel( 'sales/order' )->loadByIncrementId( $session->getLastRealOrderId() );
        if ( $order->getId() ) {
            $order->cancel()->save();
        }

        $this->_redirect( 'checkout/cart' );
    }

}
