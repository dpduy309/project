<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Paypal extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');
			$this->load->module('shipping');
			$this->load->module('store_orders');


			$this->load->model('mdl_paypal');
			

		}

		public function ipn_listener()
		{
			//the URL that accept things that Paypal has posted
			header('HTTP/1.1 200 OK'); //let Paypal know that all is well

			// STEP 1: read POST data
			// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
			// Instead, read raw POST data from the input stream.
			$raw_post_data = file_get_contents('php://input');
			$raw_post_array = explode('&', $raw_post_data);
			$myPost = array();
			foreach ($raw_post_array as $keyval) {
			  $keyval = explode ('=', $keyval);
			  if (count($keyval) == 2)
			    $myPost[$keyval[0]] = urldecode($keyval[1]);
			}
			// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
			$req = 'cmd=_notify-validate';
			if (function_exists('get_magic_quotes_gpc')) {
			  $get_magic_quotes_exists = true;
			}
			foreach ($myPost as $key => $value) {
			  if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
			    $value = urlencode(stripslashes($value));
			  } else {
			    $value = urlencode($value);
			  }
			  $req .= "&$key=$value";
			}

			// Step 2: POST IPN data back to PayPal to validate
			$ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
			// In wamp-like environments that do not come bundled with root authority certificates,
			// please download 'cacert.pem' from "https://curl.haxx.se/docs/caextract.html" and set
			// the directory path of the certificate as shown below:
			// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
			if ( !($res = curl_exec($ch)) ) {
			  // error_log("Got " . curl_error($ch) . " when processing IPN data");
			  curl_close($ch);
			  exit;
			}
			curl_close($ch);


			// inspect IPN validation result and act accordingly
			if (strcmp ($res, "VERIFIED") == 0) {
			  	$data['date_created'] = time();
				$posted_information[$key] = $value;

				foreach ($_POST as $key => $value) {
					if($key == 'custom')
					{
						$customer_session_id = $this->site_security->_decrypt_string($value);
						$value = $customer_session_id;
					}

					$posted_information .= 'key of '.$key.' was posted with a value of '.$value.'<br>'; 
				}

				$data['posted_information'] = serialize($posted_information);
				$this->_insert($data);
				$max_id = $this->get_max();
				$this->store_orders->_auto_generate_order($max_id, $customer_session_id);


			} else if (strcmp ($res, "INVALID") == 0) {
			  // IPN invalid, log for manual investigation
			}






			
		}

		public function _is_on_test_mode()
		{
			return FALSE;
		}

		public function thankyou()
		{
			$data['view_file'] = 'thankyou';
			$this->templates->public_bootstrap($data);
		}

		public function cancel()
		{
			$data['view_file'] = 'cancel';
			$this->templates->public_bootstrap($data);
		}

		public function _draw_checkout_btn($query)
		{
			foreach ($query->result() as $row) {
				$session_id = $row->session_id;
			}

			$on_test_mode = $this->_is_on_test_mode();

			if($on_test_mode == FALSE)
			{
				$data['form_location'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			}else
			{
				$data['form_location'] = 'https://www.paypal.com/cgi-bin/webscr';
			}

			$data['return'] = base_url('paypal/thankyou');
			$data['cancel_return'] = base_url('paypal/cancel');
			$data['shipping'] = $this->shipping->_get_shipping();
			$data['custom'] = $this->site_security->_encrypt_string($session_id);
			$data['paypal_email'] = $this->site_settings->_get_paypal_email();
			$data['currency_code'] = $this->site_settings->_get_currency_code();
			$data['query'] = $query;
			$this->load->view('checkout_btn',$data);
		}


		public function get_where($id)
		{
			$query = $this->mdl_paypal->get_where($id);
			return $query;
		}

		public function _insert($data)
		{
			$this->mdl_paypal->_insert($data);
		}

		public function _update($update_id,$data)
		{
			$this->mdl_paypal->_update($update_id,$data);
		}

		public function _delete($id)
		{
			$this->mdl_paypal->_delete($id);
		}

		public function get($order_by)
		{
			$query = $this->mdl_paypal->get($order_by);
			return $query;
		}

		public function get_where_custom($col,$value)
		{
			$query = $this->mdl_paypal->get_where_custom($col,$value);
			return $query;
		}

		public function get_with_limit($col,$value)
		{
			$query = $this->mdl_paypal->get_with_limit($col,$value);
			return $query;
		}

		public function _custom_query($mysql_query)
		{
			$query = $this->mdl_paypal->_custom_query($mysql_query);
			return $query;
		}

	}
?>