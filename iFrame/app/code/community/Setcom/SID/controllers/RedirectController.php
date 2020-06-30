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
                if ( $session->getLastRealOrderId() ) {
                    $lastQuoteId = (int) $data["SID_CUSTOM_01"];
                    if ( $lastQuoteId === (int) $session->getLastQuoteId() ) {
                        $quote = Mage::getModel( 'sales/quote' )->load( $lastQuoteId );
                        $quote->setIsActive( true )->save();
                    }
                }
            }
        } catch ( Exception $e ) {
            $session->addError( $e->getMessage() );
            Mage::logException( $e );
        }
        $this->clearCart();
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
    public function clearCart()
    {
        Mage::getSingleton( 'checkout/session' )->clear();
        foreach ( Mage::getSingleton( 'checkout/session' )->getQuote()->getItemsCollection() as $item ) {
            Mage::getSingleton( 'checkout/cart' )->removeItem( $item->getId() )->save();
        }
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

        $order = Mage::getModel( 'sales/order' )->loadByIncrementId( $session->getLastRealOrderId() );
        if ( $order->getId() ) {
            $order->cancel()->save();
        }

        $this->_redirect( 'checkout/cart' );
    }

    public function createOrderAction()
    {
        $quote = Mage::getSingleton( 'checkout/session' )->getQuote();
        $quote->collectTotals()->save();
        $service = Mage::getModel( 'sales/service_quote', $quote );
        try {
            $service = Mage::getModel( 'sales/service_quote', $quote );
            $service->submitAll();
            $increment_id = $service->getOrder()->getRealOrderId();
            $order        = $service->getOrder();
        } catch ( Exception $ex ) {
            echo $ex->getMessage();
        } catch ( Mage_Core_Exception $e ) {
            echo $e->getMessage();
        }

        if ( $increment_id ) {
            $model      = Mage::getModel( 'sid/paymentmethod' );
            $formFields = $model->getCheckoutFormFields( $order, $increment_id );
            echo json_encode( $formFields );
        }
        exit( 0 );
    }

}
