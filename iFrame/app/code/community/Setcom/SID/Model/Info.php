<?php
/*
 * Copyright (c) 2020 PayGate (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 *
 * Released under the GNU General Public License
 */
class Setcom_SID_Model_Info
{
    const SID_TNXID     = 'sid_tnxid';
    const SID_RECEIPTNO = 'sid_receiptno';
    const SID_BANK      = 'sid_bank';
    const SID_STATUS    = 'sid_status';

    protected $_paymentMap = array(
        self::SID_TNXID     => 'sid_tnxid',
        self::SID_RECEIPTNO => 'sid_receiptno',
        self::SID_BANK      => 'sid_bank',
        self::SID_STATUS    => 'sid_status',
    );

    public function getPaymentInfo( Mage_Payment_Model_Info $payment, $labelValuesOnly = false )
    {
        $result = $this->_getFullInfo( array_values( $this->_paymentMap ), $payment, $labelValuesOnly );

        return $result;
    }

    public function importToPayment( $from, Mage_Payment_Model_Info $payment )
    {
        Varien_Object_Mapper::accumulateByMap( $from, array( $payment, 'setAdditionalInformation' ), $this->_paymentMap );
    }

    public function &exportFromPayment( Mage_Payment_Model_Info $payment, $to, array $map = null )
    {
        Varien_Object_Mapper::accumulateByMap( array( $payment, 'getAdditionalInformation' ), $to,
            $map ? $map : array_flip( $this->_paymentMap ) );

        return $to;
    }

    protected function _getFullInfo( array $keys, Mage_Payment_Model_Info $payment, $labelValuesOnly )
    {
        $result = array();
        foreach ( $keys as $key ) {
            if ( !isset( $this->_paymentMapFull[$key] ) ) {
                $this->_paymentMapFull[$key] = array();
            }
            if ( !isset( $this->_paymentMapFull[$key]['label'] ) ) {
                if ( !$payment->hasAdditionalInformation( $key ) ) {
                    $this->_paymentMapFull[$key]['label'] = false;
                    $this->_paymentMapFull[$key]['value'] = false;
                } else {
                    $value                                = $payment->getAdditionalInformation( $key );
                    $this->_paymentMapFull[$key]['label'] = $this->_getLabel( $key );
                    $this->_paymentMapFull[$key]['value'] = $value;
                }
            }
            if ( !empty( $this->_paymentMapFull[$key]['value'] ) ) {
                if ( $labelValuesOnly ) {
                    $result[$this->_paymentMapFull[$key]['label']] = $this->_paymentMapFull[$key]['value'];
                } else {
                    $result[$key] = $this->_paymentMapFull[$key];
                }
            }
        }
        return $result;
    }

    protected function _getLabel( $key )
    {
        switch ( $key ) {
            case 'sid_tnxid':
                $label = Mage::helper( 'sid' )->__( 'SID Transaction ID' );
                break;
            case 'sid_receiptno':
                $label = Mage::helper( 'sid' )->__( 'SID Receipt No' );
                break;
            case 'sid_bank':
                $label = Mage::helper( 'sid' )->__( 'SID Bank' );
                break;
            case 'sid_status':
                $label = Mage::helper( 'sid' )->__( 'SID Status' );
                break;
            default:
                $label = '';
                break;
        }

        return ( $label );
    }
}
