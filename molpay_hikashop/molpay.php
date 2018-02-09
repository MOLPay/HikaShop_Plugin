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
        
        //This function is called when the order is confirmed by pressing the finish button and it will redirect to the end page
        function onAfterOrderConfirm(&$order, &$methods, $method_id) 
        {
                parent::onAfterOrderConfirm($order, $methods, $method_id);
                
                $return_url = HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=molpay&tmpl=component&lang=en';
                //$cancel_url = HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=order&task=cancel_order';
                //$notify_url = HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=molpay&tmpl=component&lang=en';

                $this->payment_params->return_url = $return_url;
                //$this->payment_params->cancel_url = $cancel_url;
                //$this->payment_params->notify_url = $notify_url;

                $vars = array(
                        'merchant_id' => $this->payment_params->merchantID,
                        'amount' => $order->order_full_price,
                        'orderid' => $order->order_id,
                        'bill_name' => $order->cart->billing_address->address_firstname . ' ' . $order->cart->billing_address->address_lastname,
                        'bill_email' => $order->customer->user_email,
                        'bill_mobile' => $order->cart->billing_address->address_telephone,
                        'bill_desc' => 'Order #' . $order->order_id,
                        'country' => $order->cart->billing_address->address_country->zone_code_2,
                        'vcode' => md5($order->order_full_price . $this->payment_params->merchantID . $order->order_id . $this->payment_params->verifyKey),
                        'returnurl' => $return_url,
                        //'cancelurl' => $cancel_url,
                        //'callbackurl' => $notify_url
                );

                if(empty($this->payment_params->payment_url)) 
                {
                        if($this->payment_params->accountType == "sandbox")
                                $this->payment_params->payment_url = 'https://sandbox.molpay.com/MOLPay/pay/' . $this->payment_params->merchantID . '/';
                        else if($this->payment_params->accountType == "production")
                                $this->payment_params->payment_url = 'https://www.onlinepayment.com.my/MOLPay/pay/' . $this->payment_params->merchantID . '/';
                }

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
                        if($dbOrder->order_status != 'confirmed')
                                $send_email = true;
                        else
                                $send_email = false;

                        $this->modifyOrder($orderid, 'confirmed', $history, $send_email);

                        if($nbcb != 1 && $nbcb != 2)
                        {
                                $this->app->redirect(HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=checkout&task=after_end');
                        }
                }
                else if($status == '11')
                {	
                        if($dbOrder->order_status != 'confirmed')
                        {
                                $history->data = JText::_('The Payment has failed on channel') . ' : ' . $channel;
                                $this->writeToLog('This payment is still pending.\n===============\n' . date('Y-m-d H:i:s') . '\n===============\n' . print_r($_POST, true) . '===============\n\n\n');
                                if($dbOrder->order_status != 'pending')
                                        $send_email = true;
                                else
                                        $send_email = false;

                                $this->modifyOrder($orderid, 'pending', $history, $send_email);

                                if($nbcb != 1 && $nbcb != 2)
                                {
                                        $this->app->redirect(HIKASHOP_LIVE . 'index.php/component/hikashop/checkout/');
                                }
                        }
                }
                else if($status == '22')
                {
                        $history->data = JText::_('The Payment is pending on channel') . ' : ' . $channel;
                        $this->writeToLog('This payment is still pending.\n===============\n' . date('Y-m-d H:i:s') . '\n===============\n' . print_r($_POST, true) . '===============\n\n\n');
                        if($dbOrder->order_status != 'pending')
                                $send_email = true;
                        else
                               $send_email = false;

                        $this->modifyOrder($orderid, 'pending', $history, $send_email);

                        if($nbcb != 1 && $nbcb != 2)
                        {
                                $this->app->redirect(HIKASHOP_LIVE . 'index.php?option=com_hikashop&ctrl=checkout&task=after_end');
                        }
                }
                else
                {
                        $history->data = JText::_('Skey not match!');
                        $this->writeToLog('Skey not match!\n===============\n' . date('Y-m-d H:i:s') . '\n===============\n');
                        if($dbOrder->order_status != 'pending')
                                $send_email = true;
                        else
                               $send_email = false;

                        $this->modifyOrder($orderid, 'pending', $history, $send_email);
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