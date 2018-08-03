<?php  defined('BASEPATH') OR exit('No direct script access allowed');
	class Invoices extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();

			$this->load->module('templates');
			$this->load->module('site_security');
			$this->load->module('site_settings');

		}


		public function test()
		{
			// Load all views as normal
		$this->load->view('test');
		// Get output html
		$html = $this->output->get_output();
		
		// Load library
		$this->load->library('dompdf_gen');
		
		// Convert to PDF
		$this->dompdf->load_html($html);
		$this->dompdf->render();
		$data['Attachment'] = FALSE;
		$this->dompdf->stream("ShareFoodBill.pdf",$data);
		
		}
	}
?>