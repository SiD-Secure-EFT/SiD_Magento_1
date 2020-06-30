<?php
/*
 * Copyright (c) 2020 PayGate (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 *
 * Released under the GNU General Public License
 */
class Setcom_SID_NotifyController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        try {
            $data = $this->getRequest()->getParams();
            Mage::getModel( 'sid/paymentResponse' )->processResponse( $data );
        } catch ( Exception $e ) {
            Mage::logException( $e );
        }
    }
}
