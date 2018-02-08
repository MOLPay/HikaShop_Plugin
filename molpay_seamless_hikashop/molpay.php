<?php
/**
 * @package     HikaShop for Joomla!
 * @version     3.0.0
 * @author      hikashop.com
 * @copyright   (C) 2010-2017 HIKARI SOFTWARE. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class plgHikashoppaymentMOLPay extends hikashopPaymentPlugin
{
        var $accepted_currencies = array('SGD', 'USD', 'MYR');
        var $multiple = true;
        var $name = 'molpay';

        //This function is to show the dropdownlist under the payment method
        function needCC(&$method) 
        {     
                $listChannel = "";
                foreach($this->currency as $key => $value)
                {
                        foreach($value as $k => $v)
                        {
                                if($k == "currency_code")
                                {
                                        $currency = $v;
                                }
                        }
                }

                if($method->payment_type == "molpay")
                {
                        $channels = $method->payment_params->mpschannel;
                        $molpay = new molpay();

                        $listChannel .= "<style>
                                        .col-xs-6
                                        {
                                                float: left;
                                                width: auto; 
                                        }
                                        .marginbttm{
                                                padding: 7px;
                                        }
                                        </style>";
                        $listChannel .= "<div class='row' id='payment_method' style='background: white; border-radius: 5px; border: 1px solid #f2f2f2; margin-left: 0px;'>";

                        foreach($channels as $key => $val) 
                        {
                                if($molpay->getChannelNameByCurrency($val, $currency) != "")
                                {
                                        $listChannel .= "<div class='col-md-2 col-xs-6 marginbttm text-center " . $currency . "'><label class='hand' for='payment" . $val . "'><img src='/joomla/images/channel_logos/" . $val . ".jpg' title='" . $molpay->getChannelNameByCurrency($val, $currency) . "'/></label><input style='margin: 8px 0px 0px 8px;' type='radio' name='channel' id='payment" . $val . "' value='" . $val . "' required/></div>";
                                }
                        }
                        $listChannel .= "</div>";

                        $method->custom_html = JText::_('Please select a payment channel from below to proceed to payment: ') . $listChannel 
                                             . "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'>
                                                </script>"
                                             . "<script type='text/javascript'>
                                                        $(document).ready(function(){
                                                                $('.hikashop_checkout_payment_submit').hide();
                                                        });
                                                </script>";
                }
        }

        function onPaymentSave(&$cart, &$rates, &$payment_id)
        {
                $app = &JFactory::getApplication();
                $app->setUserState(HIKASHOP_COMPONENT.'.channel', $_POST['channel']);

                return false;
        }
        
        //This function is called when the order is confirmed by pressing the finish button and it will redirect to the end page
        function onAfterOrderConfirm(&$order, &$methods, $method_id) 
        {
                parent::onAfterOrderConfirm($order, $methods, $method_id);

                $return_url = HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=molpay&tmpl=component&lang=en';
                $cancel_url = HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=order&task=cancel_order';
                //$notify_url = HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=molpay&tmpl=component&lang=en';

                $this->payment_params->return_url = $return_url;
                $this->payment_params->cancel_url = $cancel_url;
                //$this->payment_params->notify_url = $notify_url;

                $app = JFactory::getApplication();
                $channel = $app->getUserState(HIKASHOP_COMPONENT.'.channel');
                
                $vars = array(
                        'merchant_id' => $this->payment_params->merchantID,
                        'channel' => $channel,
                        'amount' => $order->order_full_price,
                        'orderid' => $order->order_id,
                        'bill_name' => $order->cart->billing_address->address_firstname . ' ' . $order->cart->billing_address->address_lastname,
                        'bill_email' => $order->customer->user_email,
                        'bill_mobile' => $order->cart->billing_address->address_telephone,
                        'bill_desc' => 'Order #' . $order->order_id,
                        'country' => $order->cart->billing_address->address_country->zone_code_2,
                        'vcode' => md5($order->order_full_price . $this->payment_params->merchantID . $order->order_id . $this->payment_params->verifyKey),
                        'currency' => $this->currency->currency_code,
                        'returnurl' => $return_url,
                        'cancelurl' => $cancel_url,
                        //'callbackurl' => $notify_url
                );
                $this->vars = $vars;

                return $this->showPage('end');
        }

        //This function will be called when notification is received from MOLPay server
        function onPaymentNotification(&$statuses)
        {
                $nbcb = $_POST['nbcb'];
                $tranID = $_POST['tranID'];
                $orderid = $_POST['orderid'];
                $status = $_POST['status'];
                $domain = $_POST['domain'];
                $amount = $_POST['amount'];
                $currency = $_POST['currency'];
                $appcode = $_POST['appcode'];
                $paydate = $_POST['paydate'];
                $channel = $_POST['channel'];
                $skey = $_POST['skey'];
                
                $dbOrder = $this->getOrder($orderid);
                $this->loadPaymentParams($dbOrder);
                if(empty($this->payment_params))
                        return false;
                $this->loadOrderData($dbOrder);
                
                $secretkey = $this->payment_params->secretKey;
                
                $pluginsClass = hikashop_get('class.plugins');
                $elements = $pluginsClass->getMethods('payment', 'molpay');
                $app = &JFactory::getApplication();
                $key0 = md5($tranID . $orderid . $status . $domain . $amount . $currency);
                $key1 = md5($paydate . $domain . $key0 . $appcode . $secretkey);

                if($skey != $key1)
                { 
                        $status = -1;
                }        

                $history = new stdClass();
                $history->type = JText::_('payment');

                if($status == '00')
                {
                        $history->data = JText::_('The Payment is successful on channel') . ' : ' . $channel;
                        $this->writeToLog('This payment is successful.\n===============\n' . date('Y-m-d H:i:s') . '\n===============\n' . print_r($_POST, true) . '===============\n\n\n');
                        //$this->modifyOrder($orderid, 'confirmed', $history, true);

                        if($nbcb != 1 && $nbcb != 2)
                        {
                        	if($dbOrder->order_status != 'confirmed')
                        	{
                            	$this->modifyOrder($orderid, 'confirmed', $history, true);
                            	$this->app->redirect(HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=checkout&task=after_end');
                        	}
                        	else
                        	{
                                 $this->app->redirect(HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=checkout&task=after_end');                       		
                        	}
                        }
                }
                else if($status == '11')
                {
                	if($dbOrder->order_status != 'confirmed')
                        {
                            $history->data = JText::_('The Payment has failed on channel') . ' : ' . $channel;
                            $this->writeToLog('This payment is still pending.\n===============\n' . date('Y-m-d H:i:s') . '\n===============\n' . print_r($_POST, true) . '===============\n\n\n');
                            //$this->modifyOrder($orderid, 'pending', $history, true);

                            if($nbcb != 1 && $nbcb != 2)
                            {
            					if($dbOrder->order_status != 'pending')
                        		{
                            		$this->modifyOrder($orderid, 'pending', $history, true);
                            		$this->app->redirect(HIKASHOP_LIVE . 'index.php/component/hikashop/checkout/');
                        		}
                        		else
                        		{
                                	$this->app->redirect(HIKASHOP_LIVE . 'index.php/component/hikashop/checkout/');
                        		}                   
                            }
                        }
                }
                else if($status == '22')
                {
                        $history->data = JText::_('The Payment is pending on channel') . ' : ' . $channel;
                        $this->writeToLog('This payment is still pending.\n===============\n' . date('Y-m-d H:i:s') . '\n===============\n' . print_r($_POST, true) . '===============\n\n\n');
                        //$this->modifyOrder($orderid, 'pending', $history, true);
                        
                        if($nbcb != 1 && $nbcb != 2)
                        {
                        	if($dbOrder->order_status != 'pending')
                        	{
                                $this->modifyOrder($orderid, 'pending', $history, true);
                                $this->app->redirect(HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=checkout&task=after_end');
                        	}
                        	else
                        	{
                        		$this->app->redirect(HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=checkout&task=after_end');
                        	}
                        }
                }
                else
                {
                        $history->data = JText::_('Skey not match!');
                        $this->writeToLog('Skey not match!\n===============\n' . date('Y-m-d H:i:s') . '\n===============\n');
                        //$this->modifyOrder($orderid, 'pending', $history, true);
                        if($dbOrder->order_status != 'pending')
                        {
                        	$this->modifyOrder($orderid, 'pending', $history, true);
                        }
                }

                if($nbcb == 1) 
                {
                        echo 'CBTOKEN:MPSTATOK';
                        exit();
                }
                else
                {
                        $_POST[treq] = 1; // Additional parameter for IPN
                        while ( list($k,$v) = each($_POST) ) 
                        {
                                $postData[]= $k."=".$v;
                        }
                        $postdata = implode("&",$postData);
                        
                        if($this->payment_params->accountType == "sandbox")
                                $url = "https://sandbox.molpay.com/MOLPay/API/chkstat/returnipn.php";
                        else if($this->payment_params->accountType == "production")
                                $url = "https://www.onlinepayment.com.my/MOLPay/API/chkstat/returnipn.php";

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_POST , 1 );
                        curl_setopt($ch, CURLOPT_POSTFIELDS , $postdata );
                        curl_setopt($ch, CURLOPT_URL , $url );
                        curl_setopt($ch, CURLOPT_HEADER , 1 );
                        curl_setopt($ch, CURLINFO_HEADER_OUT , TRUE );
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1 );
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , FALSE );
                        curl_setopt($ch, CURLOPT_SSLVERSION , 6 ); // use only TLSv1.2
                        $result = curl_exec( $ch );
                        curl_close( $ch );       
                }
        }
        
        //This function is to set the default payment params
        function getPaymentDefaultValues(&$element)
        {
                $element->payment_name = 'MOLPay';
        }
}

//This is the class functions which allow the foreign function to be called inside the payment method
class molpay 
{
        //This function is to return all bank list for the merchant to choose
        function retChannel()
        {
                $list = array(
                        'affinonline'           => 'Affin Bank',
                        'amb'                   => 'Am Bank',
                        'bankislam'             => 'Bank Islam',
                        'cimbclicks'            => 'CIMB Clicks',
                        'hlb'                   => 'Hong Leong Bank ',
                        'maybank2u'             => 'Maybank2u',
                        'pbb'                   => 'PublicBank',
                        'rhb'                   => 'RHB Bank',
                        'fpx'                   => 'MyClear FPX ',
                        'fpx_amb'               => 'FPX Am Bank',
                        'fpx_bimb'              => 'FPX Bank Islam',
                        'fpx_cimbclicks'        => 'FPX CIMB Clicks',
                        'fpx_hlb'               => 'FPX Hong Leong Bank',
                        'fpx_mb2u'              => 'FPX Maybank2u',
                        'fpx_pbb'               => 'FPX PublicBank',
                        'fpx_rhb'               => 'FPX RHB Bank',
                        'cash-711'              => '7-Eleven',
                        'credit'                => 'Credit Card/ Debit Card',
                        //'enetsD'                => 'eNets',
                        //'cash-epay'             => 'e-pay',
                        //'PEXPLUS'               => 'PEx+',
                        'jompay'                => 'JomPAY',
                        //'Cash-Esapay'           => 'Esapay',
                        'FPX_OCBC'              => 'FPX OCBC Bank',
                        'FPX_SCB'               => 'FPX Standard Chartered Bank',
                        'FPX_ABB'               => 'FPX Affin Bank',
                        //'singpost'              => 'Cash SAM',
                        //'fpx_abmb'              => 'FPX Alliance Bank',
                        //'fpx_uob'               => 'FPX United Overseas Bank',
                        //'fpx_bsn'               => 'FPX Bank Simpanan Nasional',
                        'th_pb_scbpn'           => 'SCBPN',
                        'th_pb_ktbpn'           => 'KTBPN',
                        'th_pb_baypn'           => 'BAYPN',
                        'th_pb_bblpn'           => 'BBLPN',
                        'th_pb_cash'            => 'CASH',
                        'vtc-vietcombank'       => 'Vietcom Bank',
                        'vtc-techcombank'       => 'Techcom Bank'
                );

                return $list;
        }

        //This function will return the channels based on the currency
        function getChannelNameByCurrency(&$channel, &$currency) 
        {
                if($currency=="MYR")
                {
                        $list = array(
                                'affinonline'           => 'Affin Bank',
                                'amb'                   => 'Am Bank',
                                'bankislam'             => 'Bank Islam',
                                'cimbclicks'            => 'CIMB Clicks',
                                'hlb'                   => 'Hong Leong Bank ',
                                'maybank2u'             => 'Maybank2u',
                                'pbb'                   => 'PublicBank',
                                'rhb'                   => 'RHB Bank',
                                'fpx'                   => 'MyClear FPX ',
                                'fpx_amb'               => 'FPX Am Bank',
                                'fpx_bimb'              => 'FPX Bank Islam',
                                'fpx_cimbclicks'        => 'FPX CIMB Clicks',
                                'fpx_hlb'               => 'FPX Hong Leong Bank',
                                'fpx_mb2u'              => 'FPX Maybank2u',
                                'fpx_pbb'               => 'FPX PublicBank',
                                'fpx_rhb'               => 'FPX RHB Bank',
                                'FPX_OCBC'              => 'FPX OCBC Bank',
                                'FPX_SCB'               => 'FPX Standard Chartered Bank',
                                'FPX_ABB'               => 'FPX Affin Bank',
                                'cash-711'              => '7-Eleven',
                                'credit'                => 'Credit Card/ Debit Card',
                                //'enetsD'                => 'eNets',
                                //'cash-epay'             => 'e-pay',
                                //'PEXPLUS'               => 'PEx+',
                                'jompay'                => 'JomPAY',
                                //'Cash-Esapay'           => 'Esapay'
                        );
                }
                else if($currency=="THB")
                {
                        $list = array(
                                'th_pb_scbpn'   => 'SCBPN',
                                'th_pb_ktbpn'   => 'KTBPN',
                                'th_pb_baypn'   => 'BAYPN',
                                'th_pb_bblpn'   => 'BBLPN',
                                'th_pb_cash'    => 'CASH'
                        );
                }
                else if($currency=="VND")
                {
                        $list = array(
                                'vtc-vietcombank'   => 'Vietcom Bank',
                                'vtc-techcombank'   => 'Techcom Bank'
                        );
                }

                if(!empty($list[$channel]))
                        return $list[$channel];
                else
                        return "";
        }
}