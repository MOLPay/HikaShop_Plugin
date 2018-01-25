<!-- Here is the ending page, called at the end of the checkout, just before the user is redirected to the payment plateform -->
<div class="overlay" style="background: #000000; display: none; position: fixed; top: 0; right: 0; bottom: 0; left: 0; opacity: 0.5; height: 100%; z-index: 2147483647;">
</div>
<div class="hikashop_molpay_end" id="hikashop_molpay_end">
	<span id="hikashop_molpay_end_message" class="hikashop_molpay_end_message">
		<?php echo JText::_('CLICK_ON_BUTTON_IF_NOT_REDIRECTED');?>
	</span>
	<span id="hikashop_molpay_end_spinner" class="hikashop_molpay_end_spinner">
		<img src="<?php echo HIKASHOP_IMAGES.'spinner.gif';?>" />
	</span>
	<br/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	
	<?php
	if($this->payment_params->accountType == "sandbox")
			echo "<script src='https://sandbox.molpay.com/MOLPay/API/seamless/latest/js/MOLPay_seamless.deco.js'></script>";
	else if($this->payment_params->accountType == "production")
			echo "<script src='https://www.onlinepayment.com.my/MOLPay/API/seamless/latest/js/MOLPay_seamless.deco.js'></script>";

	if(isset($this->vars)) 
	{
			$your_process_status = true;
		 
			if( $your_process_status === true ) 
			{
					$params = array(
							'status'          => true,
							'mpsmerchantid'   => $this->vars['merchant_id'],
							'mpschannel'      => $this->vars['channel'],
							'mpsamount'       => $this->vars['amount'],
							'mpsorderid'      => $this->vars['orderid'],
							'mpsbill_name'    => $this->vars['bill_name'],
							'mpsbill_email'   => $this->vars['bill_email'],
							'mpsbill_mobile'  => $this->vars['bill_mobile'],
							'mpsbill_desc'    => $this->vars['bill_desc'],
							'mpscountry'      => $this->vars['country'],
							'mpsvcode'        => $this->vars['vcode'],
							'mpscurrency'     => $this->vars['currency'],
							'mpslangcode'     => "en",
							//'mpsextra'      => base64_encode("1#mpd_tonton:10.00"),
							//'mpstimer'	  => (int)$_POST['molpaytimer'],
							//'mpstimerbox'	  => "#counter",
							'mpscancelurl'	  => $this->vars['cancelurl'],
							'mpsreturnurl'    => $this->vars['returnurl'],
							'mpsapiversion'   => "latest"
					);
			} 
			elseif( $your_process_status === false ) 
			{
					$params = array(
							'status'          => false,
							'error_code'	  => "Your Error Code (Eg: 500)",
							'error_desc'      => "Your Error Description (Eg: Internal Server Error)",
							'failureurl'      => "index.html"
					);
			}
	}
	else
	{
			$params = array(
					'status'          => false,
					'error_code'	  => "500",
					'error_desc'      => "Internal Server Error",
					'failureurl'      => "index.html"
			);
	}
	//echo json_encode( $params );

	if ($params['status'])
	{
			echo "<button type=\"button\" id=\"seamless\" class=\"btn btn-primary btn-lg\" data-toggle=\"molpayseamless\" data-mpsmerchantid=\"".$params['mpsmerchantid']."\" data-mpschannel=\"".$params['mpschannel']."\" data-mpsamount=\"".$params['mpsamount']."\" data-mpsorderid=\"".$params['mpsorderid']."\" data-mpsbill_name=\"".$params['mpsbill_name']."\"  data-mpsbill_email=\"".$params['mpsbill_email']."\" data-mpsbill_mobile=\"".$params['mpsbill_mobile']."\" data-mpsbill_desc=\"".$params['mpsbill_desc']."\" data-mpscountry=\"".$params['mpscountry']."\" data-mpsvcode=\"".$params['mpsvcode']."\" data-mpscurrency=\"".$params['mpscurrency']."\" data=mpslangcode=\"".$params['mpslangcode']."\" data=mpsextra=\"".""."\" mpscancelurl=\"".$params['mpscancelurl']."\" data-mpsreturnurl=\"".$params['mpsreturnurl']."\" data-mpsapiversion=\"".$params['mpsapiversion']."\">Pay by seamless</button>";
			
			echo "<script type='text/javascript'>
		                $(document).ready( function(){
		                		$('#seamless').click(function(){
        								$('.overlay').show();
    							});
		                        $('#seamless').click();
		                });
		        </script>";
    }
	?>
</div>