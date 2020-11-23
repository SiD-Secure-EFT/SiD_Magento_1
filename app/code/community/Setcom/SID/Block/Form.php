<?php
/*
 * Copyright (c) 2020 PayGate (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 *
 * Released under the GNU General Public License
 */
class Setcom_SID_Block_Form extends Mage_Core_Block_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate( 'sid/form.phtml' );
    }
}
