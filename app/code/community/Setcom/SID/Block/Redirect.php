<?php
/*
 * Copyright (c) 2020 PayGate (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 * 
 * Released under the GNU General Public License
 */
class Setcom_SID_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $sid = Mage::getModel( 'sid/paymentMethod' );

        $form = new Varien_Data_Form();
        $form->setAction( $sid->getSidPaymentUrl() )
            ->setId( 'sid_checkout' )
            ->setName( 'sid_checkout' )
            ->setMethod( 'POST' )
            ->setUseContainer( true );
        foreach ( $sid->getCheckoutFormFields() as $field => $value ) {
            $form->addField( $field, 'hidden', array( 'name' => $field, 'value' => $value ) );
        }
        $submitButton = new Varien_Data_Form_Element_Submit( array(
            'value' => $this->__( 'Click here if you are not redirected within 10 seconds...' ),
        ) );
        $submitButton->setId( "submit_to_sid" );
        $form->addElement( $submitButton );
        $html = '<html><body>';
        $html .= $this->__( 'You will be redirected to the SID website in a few seconds.' );
        $html .= $form->toHtml();
        $html .= '<script type="text/javascript">document.getElementById("sid_checkout").submit();</script>';
        $html .= '</body></html>';

        return $html;
    }
}
