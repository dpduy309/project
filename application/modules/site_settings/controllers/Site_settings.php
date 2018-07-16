<?php  
	class Site_settings extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->module('store_account');
		}

		public function _get_map_code()
		{
			$code = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.157934091906!2d106.73054795021442!3d10.799213261688307!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3175261adbee8aa3%3A0x952d7c5fe8147dbe!2zMTcgWGEgbOG7mSBIw6AgTuG7mWksIEFuIFBow7osIFF14bqtbiAyLCBI4buTIENow60gTWluaCwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1528118477163" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>';
			return $code;
		}

		public function _get_our_address()
		{
			$address = '17 Xa Lo Ha Noi <br>';
			$address.='Thao Dien, Q2, TPHCM';
			return $address;
		}

		public function _get_our_telnum()
		{
			$telnum = '0934 090 328';
			return $telnum;
		}

		public function _get_paypal_email()
		{
			$email = 'duy.doan.tdm@gmail.com';
			return $email;
		}

		public function _get_currency_code()
		{
			$code = 'VND';
			return $code;
		}

		public function _get_our_name()
		{
			$name = 'shopbinandjenny';
			return $name;
		}

		public function _get_support_team_name()
		{
			$name = "Customer Support";
			return $name;
		}

		public function _get_welcome_msg($customer_id)
		{
			$customer_name = $this->store_account->_get_customer_name($customer_id);

			$msg = "Hello ".$customer_name.",<br><br>";
			$msg.="Thank you for creating an account with us. If you have any question about any of our products and services then please do get in touch. We are here seven days a week and would be happy top help you.<br><br>";
			$msg.= "Regard, <br><br>";
			$msg.="shopbinandjenny";
			return $msg;
		}
		
		public function _get_cookie_name()
		{
			$cookie_name = 'htelbhz';
			return $cookie_name;
		}

		public function _get_item_segments()
		{
			//return the segments for the store_item page (produce page)
			$segments = "fashional/choice/";
			return $segments;
		}

		public function _get_items_segments()
		{
			//return the segments for the category page (produce page)
			$segments = "fashion/choices/";
			return $segments;
		}
	}


?>