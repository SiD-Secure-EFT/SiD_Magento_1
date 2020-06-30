<?php
/*
 * Copyright (c) 2020 PayGate (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 *
 * Released under the GNU General Public License
 */
class Setcom_SID_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate( 'sid/form.phtml' );
        if ( (int) Mage::getStoreConfig( 'payment/sid/display_additional_info_href' ) === 1 ) {
            $mark = Mage::getConfig()->getBlockClassName( 'core/template' );
            $mark = new $mark;
            $mark->setTemplate( 'sid/mark.phtml' );
            $this->setMethodLabelAfterHtml( $mark->toHtml() );

        }
    }
}
