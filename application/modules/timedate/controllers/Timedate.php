<?php  
	class Timedate extends MX_Controller
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function get_nice_date($timestamp, $format)
		{
			switch($format)
			{
				case 'full':
				//FULL: Friday 18th of February 2018 at 11:00:00
				$the_date = date('l jS \of F Y \a\t h:i:s A',$timestamp);
				break;

				case 'cool':
				//Cool: Friday 18th of February 2018
				$the_date = date('l jS \of F Y',$timestamp);
				break;

				case 'shorter':
				//Shorter: 18th of February 2018
				$the_date = date('jS \of F Y',$timestamp);
				break;

				case 'mini':
				//mini: 18th Feb 2018
				$the_date = date('jS M Y',$timestamp);
				break;

				case 'oldschool':
				//oldschool: 18/2/2018
				$the_date = date('j\/n\/y',$timestamp);
				break;

				case 'datepicker':
				//datepicker: 18/2/2018
				$the_date = date('d\-m\-Y',$timestamp);
				break;

				case 'datepicker_us':
				//datepicker: 18/2/2018
				$the_date = date('m\/d\/Y', $timestamp);
				break;

				case 'monyear':
				//monyear: 
				$the_date = date('F Y',$timestamp);
				break;
			}
				return $the_date;
			
		}

		public function make_timestamp_from_datepicker($datepicker)
		{
			$hour = 7; 
			$minute = 0;
			$second = 0;

			$day = substr($datepicker,0,2); //cat chuoi lay lan luot ngay thang nam
			$month = substr($datepicker,3,2);
			$year = substr($datepicker,6,4);

			$timestamp = mktime($hour,$minute,$second,$month,$day,$year);
			return $timestamp;
		}

		public function make_timestamp_from_datepicker_us($datepicker)
		{
			$hour = 7; 
			$minute = 0;
			$second = 0;

			$month = substr($datepicker,0,2); //cat chuoi lay lan luot ngay thang nam
			$day = substr($datepicker,3,2);
			$year = substr($datepicker,6,4);

			$timestamp = mktime($hour,$minute,$second,$month,$day,$year);
			return $timestamp; 
		}

		public function make_timestamp($day,$month,$year)
		{
			$hour = 7; 
			$minute = 0;
			$second = 0;

			$timestamp = mktime($hour,$minute,$second,$month,$day,$year);
			return $timestamp;
		}











	}




























?>
