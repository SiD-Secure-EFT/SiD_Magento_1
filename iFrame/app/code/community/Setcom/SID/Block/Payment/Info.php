<?php
/*
 * Copyright (c) 2020 PayGate (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 *
 * Released under the GNU General Public License
 */
class Setcom_SID_Block_Payment_Info extends Mage_Payment_Block_Info_Cc
{
    protected function _prepareSpecificInformation( $transport = null )
    {
        $transport = parent::_prepareSpecificInformation( $transport );
        $payment   = $this->getInfo();
        $sidInfo   = Mage::getModel( 'sid/info' );
        if ( !$this->getIsSecureMode() ) {
            $info = $sidInfo->getPaymentInfo( $payment, true );
            return $transport->addData( $info );
        }
        return $transport;
    }
}
